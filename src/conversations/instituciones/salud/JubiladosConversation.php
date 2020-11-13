<?php

namespace BotCredifintech\Conversations\Instituciones\Salud;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "/../../../Constantes.php";
require_once __DIR__ . "/../../SalidaConversation.php";
require_once __DIR__ . "/ConstantesSalud.php";
require_once __DIR__ . "/../../../prospectos/ProspectoSaludJubilado.php";

use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use Mpociot\BotMan\Cache\DoctrineCache;

use BotCredifintech\Constantes;
use BotCredifintech\Conversations\SalidaConversation;
use BotCredifintech\Conversations\Insituciones\SaludConversation;
use BotCredifintech\Conversations\Instituciones\Salud\ConstantesSalud;
use BotCredifintech\Prospectos\ProspectoSaludJubilado;

class JubiladosConversation extends Conversation
{
  protected $prospecto, $pJubilado;

  public function __construct($prospecto)
  {
      $this->prospecto = $prospecto;
      $this->pJubilado = new ProspectoSaludJubilado();
      $this->pJubilado->nombre = $prospecto->nombre;
      $this->pJubilado->telefono = $prospecto->telefono;
      $this->pJubilado->email = $prospecto->email;
      $this->pJubilado->identificacion = $prospecto->identificacion;
      $this->pJubilado->monto = $prospecto->identificacion;
      $this->pJubilado->id = $prospecto->id;
  }


  public function askInformacion(){
    $pj = $this->pJubilado;
    $this -> askNSS($pj); 
  }

  public function stopsConversation(IncomingMessage $message)
	{
    $texto = $message->getText();
		if ($texto == 'Deme un momento') {
			return true;
		}

		return false;
  }
  
  public function askNSS($pj){
    $this -> ask(Constantes::PEDIR_NSS, function(Answer $response) use ($pj){
      $pj->nss = $response->getText();

      $note = array(
        "subject"=>"NSS",
        "description"=>$pj->nss,
        "contact_ids"=>array($pj->id),
      );
      $note = json_encode($note);

      $note_result = curl_wrap("notes", $note, "POST", "application/json");

      $this->askInformePago($pj);
    });
  }

  public function askInformePago($pj)
  {
    $this->askForImages(Constantes::PEDIR_INFORME_PAGO, function ($images) use ($pj) {
      $pj->informeDePago = $images;

      foreach ($images as $image) {
        $url = $image->getUrl(); // The direct url
        
        $note = array(
          "subject"=>"Informe de Pago",
          "description"=>$url,
          "contact_ids"=>array($pj->id),
        );
        $note = json_encode($note);

        $note_result = curl_wrap("notes", $note, "POST", "application/json");
        

      }

      $this->askTerminar($pj); 
    });
  }

  public function askTerminar(){
    $this->ask(Constantes::MENSAJE_SOLICITUD_TERMINADA, function(Answer $response){
      return false;
    });
  }

  public function run() {
    $this -> askInformacion();
  }
}

?>