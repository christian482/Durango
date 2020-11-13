<?php

namespace BotCredifintech\Conversations\Instituciones\Gobierno;

require __DIR__ . './../../../../vendor/autoload.php';

require_once __DIR__ . "/../../../Constantes.php";
require_once __DIR__ . "/../../SalidaConversation.php";
require_once __DIR__."/ConstantesGobierno.php";
require_once __DIR__ . "/../../../prospectos/ProspectoGobierno.php";
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
use BotCredifintech\Conversations\Instituciones\Gobierno\ConstantesGobierno;
use BOtCredifintech\Prospectos\ProspectoGobierno;


class GobiernoConversation extends Conversation {

  protected $prospecto, $pGobierno;

  public function __construct($prospecto)
  {
      $this->prospecto = $prospecto;
      $this->pGobierno = new ProspectoGobierno();
      $this->pGobierno->nombre = $prospecto->nombre;
      $this->pGobierno->telefono = $prospecto->telefono;
      $this->pGobierno->email = $prospecto->email;
      $this->pGobierno->identificacion = $prospecto->identificacion;
      $this->pGobierno->monto = $prospecto->identificacion;
  }

  protected $errores = 0;
  protected $estadoSeleccionado = "";

  public function error(){
    $this->errores += 1;
    if($this->errores >= 3){
      $this->llamarAsesor();
    } else {
      $this->say(Constantes::MENSAJE_NAVEGACION_BOTONES);
      $this->askCategoria();
    }
  }

  public function askEstados($pg){

    $estados = ConstantesGobierno::$estados;

    //Crea el arreglo de opciones de botones de estados disponibles.
    $buttonArray = array();
    foreach($estados as $e){
      array_push($buttonArray, Button::create($e)->value($e));
    }
    array_push($buttonArray, Button::create("Otro")->value("Otro"));

    $question = Question::create("¿De cuál de los siguientes estados es parte la dependencia en la que labora?")
        ->fallback('Si no pertenece a alguna de las anteriores categorías no se podrá proceder con la solicitud, lo sentimos, estamos en contacto')
        ->callbackId('ask_lista_estados')
        ->addButtons($buttonArray);

    $this->ask($question, function (Answer $answer) use ($estados, $pg) {
      if ($answer->isInteractiveMessageReply()) {
        $gob = "Gobierno";

        $selectedValue = $answer->getValue();
        $pg->estado = $selectedValue;

        $fromCRM = curl_wrap("contacts/search/email/".$pg->email, null, "GET", "application/json");
        $fromCRMarr = json_decode($fromCRM, true, 512, JSON_BIGINT_AS_STRING);
        $id = $fromCRMarr["id"];
        //$this->say("info: ".$fromCRMarr);
    
        $contact_update = array(
          "id" => $id, //It is mandatory field. Id of contact
          "tags" => array($gob, $selectedValue),
        );
        $contact_update = json_encode($contact_update);
        $output = curl_wrap("contacts/edit/tags", $contact_update, "PUT", "application/json");


        if(in_array($selectedValue, $estados)){
          $this -> $estadoSeleccionado = $selectedValue;
          $this->askRequerimientos($pg);
        } else {
          $this->bot->startConversation(new SalidaConversation());
        }
      } else {
        $this->error();
      }
    });
  }

  public function askRequerimientos($pg){

    $conversations = [];

    $question = Question::create(Constantes::PREGUNTA_DOCUMENTACION)
        ->fallback('En orden de realizar esta solicitud son necesarios estos documentos y datos, sin ellos no podrá continuar')
        ->callbackId('ask_documentos_e_informacion')
        ->addButtons([
            Button::create("Listo, empecemos")->value("Listo, empecemos"),
        ]);
    
      $this->say(Constantes::MENSAJE_DATOS_REQUERIDOS);
      $this->say("Dos recibos de pago");
      $this->ask($question, function (Answer $answer) use ($tipo, $pg){
      $this->tipoInstitucion = $answer->getValue();
      if ($answer->isInteractiveMessageReply()) {
        $selectedValue = $answer->getValue(); // Tipo/Gobierno / Tipo/Privado / Tipo/Pensionado
        if($selectedValue=="Listo, empecemos"){
          $this->askInformacion($pg);
        }
      } else {
        $this->error();
      }
    });
  
  }

  public function askInformacion($pg){
    $this -> askInformePago($pg); 
  }

  public function stopsConversation(IncomingMessage $message)
	{
    $texto = $message->getText();
		if ($texto == 'Deme un momento') {
			return true;
		}

		return false;
  }

  public function askInformePago($pg)
  {
    $this->askForImages("Tome una foto a sus últimos dos recibos de pago, envíelas de preferencia en grupo", function ($images) {
      $pg->informeDePago = $images;    
      $this->askTerminar(); 
    });
  }


  public function askTerminar(){
    $this->ask(Constantes::MENSAJE_SOLICITUD_TERMINADA, function(Answer $response){
      return false;
    });
  }

  public function run(){
    $pg = $this->pGobierno;
    $this -> askEstados($pg);
  }

}

?>