<?php

namespace BotCredifintech\Conversations\Instituciones\Salud;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "/../../../Constantes.php";
require_once __DIR__ . "/../../SalidaConversation.php";
require_once __DIR__ . "/PensionadosConversation.php";
require_once __DIR__ . "/ConfianzaConversation.php";
require_once __DIR__ . "/JubiladosConversation.php";
require_once __DIR__ . "/ParitariaConversation.php";
require_once __DIR__ . "/../../../curlwrap_v2.php";

use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use Mpociot\BotMan\Cache\DoctrineCache;

use BotCredifintech\Constantes;
use BotCredifintech\Conversations\SalidaConversation;
use BotCredifintech\Conversations\Instituciones\Salud\PensionadosConversation;
use BotCredifintech\Conversations\Instituciones\Salud\ConfianzaConversation;
use BotCredifintech\Conversations\Instituciones\Salud\JubiladosConversation;
use BotCredifintech\Conversations\Instituciones\Salud\ParitariaConversation;

class SaludConversation extends Conversation
{

  private $prospecto;

  public function __construct($prospecto)
  {
      $this->prospecto = $prospecto;
  }

  const PENSIONADOS = "Pensionados";
  const CONFIANZA = "Confianza";
  const JUBILADOS = "Jubilados";
  const PARITARIA = "Paritaria";
  
  var $requerimientos = "";


  protected $errores = 0;

  public function askCategoria($prospecto){
    if(!isset($prospecto)){
      $prospecto = $this->prospecto;
    }
    $question = Question::create("$prospecto->nombre, ¿En que categoría se encuentra usted?")
        ->fallback('Si no pertenece a alguna de las anteriores categorías no se podrá proceder con la solicitud, lo sentimos, estamos en contacto')
        ->callbackId('ask_area_gobierno')
        ->addButtons([
            Button::create(self::PENSIONADOS)->value(self::PENSIONADOS),
            Button::create(self::CONFIANZA)->value(self::CONFIANZA),
            Button::create(self::JUBILADOS)->value(self::JUBILADOS),
            Button::create(self::PARITARIA)->value(self::PARITARIA),
        ]);

    $this->ask($question, function (Answer $answer) use ($prospecto){
      if ($answer->isInteractiveMessageReply()) {
        $selectedValue = $answer->getValue();
        if($selectedValue==self::PENSIONADOS){
          $prospecto->tipo = self::PENSIONADOS;
          $this->askRequerimientos(self::PENSIONADOS, ConstantesSalud::DATOS_PENSIONADO, $prospecto);
        }
        if($selectedValue==self::CONFIANZA){
          $prospecto->tipo = self::CONFIANZA;
          $this->askRequerimientos(self::CONFIANZA, ConstantesSalud::DATOS_CONFIANZA, $prospecto);
        }
        if($selectedValue==self::JUBILADOS){
          $prospecto->tipo = self::JUBILADOS;
          $this->askRequerimientos(self::JUBILADOS, ConstantesSalud::DATOS_JUBILADOS, $prospecto);
        }
        if($selectedValue==self::PARITARIA){
          $prospecto->tipo = self::PARITARIA;
          $this->askRequerimientos(self::PARITARIA, ConstantesSalud::DATOS_PARITARIA, $prospecto);
        }
      } else {
        $this->errores += 1;
        if($this->errores >= 3){
          $this->llamarAsesor();
        } else {
          $this->say(Constantes::MENSAJE_NAVEGACION_BOTONES);
          $this->askCategoria($prospecto);
        }
        
    }
    });
  }

  public function askRequerimientos($tipo, $req, $p){

    $conversations = [];
    $imss = "IMSS";
    $p->etiquetas.array_push($imss, $tipo);
    
    //$this->say("info: ".$fromCRMarr);

    $contact_update = array(
      "id" => $p->id, //It is mandatory field. Id of contact
      "tags" => array($imss, $tipo),
    );
    $contact_update = json_encode($contact_update);
    curl_wrap("contacts/edit/tags", $contact_update, "PUT", "application/json");

    $question = Question::create(Constantes::PREGUNTA_DOCUMENTACION)
        ->fallback('En orden de realizar esta solicitud son necesarios estos documentos y datos, sin ellos no podrá continuar')
        ->callbackId('ask_documentos_e_informacion')
        ->addButtons([
            Button::create("Listo, empecemos")->value("Listo, empecemos"),
        ]);
    
      $this->say(Constantes::MENSAJE_DATOS_REQUERIDOS);
      $this->say($req);
      $this->ask($question, function (Answer $answer) use ($tipo, $req, $p){
      $this->tipoInstitucion = $answer->getValue();
      if ($answer->isInteractiveMessageReply()) {
        $selectedValue = $answer->getValue(); // Tipo/Gobierno / Tipo/Privado / Tipo/Pensionado
        if($selectedValue=="Listo, empecemos"){
          $conversations = [
            self::PENSIONADOS => new PensionadosConversation($p),
            self::CONFIANZA => new ConfianzaConversation($p),
            self::JUBILADOS => new JubiladosConversation($p),
            self::PARITARIA => new ParitariaConversation($p),
          ];

          $this->bot->StartConversation($conversations[$tipo]);
        }
      } else {
        $this->errores += 1;
        if($this->errores >= 3){
          $this->llamarAsesor();
        } else {
          $this->say(Constantes::MENSAJE_NAVEGACION_BOTONES);
          $this->askRequerimientos($tipo, $req,$p);
        }
      }
    });
  
  }

  public function llamarAsesor(){
    $this->say(Constantes::MENSAJE_AYUDA_ASESOR);
  }

  public function stopsConversation(IncomingMessage $message)
	{
		if (strcasecmp($message->getText(), 'asesor') == 0) {
      $this->say("La conversación se ha detenido, espere al asesor");
			return true;
		}
		return false;
	}

  public function run(){
    $this->askCategoria($this->$prospecto);
  }
}

?>