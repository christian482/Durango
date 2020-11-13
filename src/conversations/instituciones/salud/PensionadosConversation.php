<?php

namespace BotCredifintech\Conversations\Instituciones\Salud;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "/../../../Constantes.php";
require_once __DIR__ . "/../../SalidaConversation.php";
require_once __DIR__ . "/ConstantesSalud.php";
require_once __DIR__ . "/../../../prospectos/ProspectoSaludPensionado.php";

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
use BotCredifintech\Prospectos\ProspectoSaludPensionado;

class PensionadosConversation extends Conversation
{

  private $prospecto, $pPensionado;

  public function __construct($prospecto)
  {
      $this->prospecto = $prospecto;
      $this->pPensionado = new ProspectoSaludPensionado();
      $this->pPensionado->nombre = $prospecto->nombre;
      $this->pPensionado->telefono = $prospecto->telefono;
      $this->pPensionado->email = $prospecto->email;
      $this->pPensionado->identificacion = $prospecto->identificacion;
      $this->pPensionado->monto = $prospecto->identificacion;
      $this->pPensionado->id = $prospecto->id;
  }

  public function askInformacion(){
    $pp = $this->pPensionado;
    $this -> askNSS($pp); 
  }

  public function stopsConversation(IncomingMessage $message)
	{
    $texto = $message->getText();
		if ($texto == 'Deme un momento') {
			return true;
		}

		return false;
  }

  public function askNSS($pp){
    $this -> ask(Constantes::PEDIR_NSS, function(Answer $response) use ($pp){
      $pp->nss = $response->getText();

      $note = array(
        "subject"=>"NSS",
        "description"=>$pp->nss,
        "contact_ids"=>array($pp->id),
      );
      $note = json_encode($note);

      $note_result = curl_wrap("notes", $note, "POST", "application/json");

      $this->askInformePago($pp);
    });
  }

  public function askInformePago($pp)
  {
    $this->askForImages(Constantes::PEDIR_INFORME_PAGO, function ($images) use ($pp) {
      $pp->informeDePago = $images;
      
      foreach ($images as $image) {
        $url = $image->getUrl(); // The direct url
        
        $note = array(
          "subject"=>"Informe de Pago",
          "description"=>$url,
          "contact_ids"=>array($pp->id),
        );
        $note = json_encode($note);

        $note_result = curl_wrap("notes", $note, "POST", "application/json");
        

      }

      $this->askTerminar($pp); 
    });
  }

  public function askTerminar($pp){
    $this->ask(Constantes::MENSAJE_SOLICITUD_TERMINADA, function(Answer $response){
      return false;
    });
  }

  public function run() {
    $this -> askInformacion();
  }
}

?>