<?php

namespace BotCredifintech\Conversations;

require __DIR__ . './../../vendor/autoload.php';

require_once __DIR__ . "./../Constantes.php";
require_once __DIR__ . "/SolicitarDatosConversation.php";
require_once __DIR__."/SalidaConversation.php";

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use Mpociot\BotMan\Cache\DoctrineCache;

use BotCredifintech\Constantes;
use BotCredifintech\Conversations\SalidaConversation;
use BotCredifintech\Conversations\SolicitarDatosConversation;

class MenuConversation extends Conversation
{
  protected $tipoInstitucion;

  protected $errores = 0;

  protected $conversation;

  public function menu()
  {

      $question = Question::create(Constantes::SELECCIONE_OPCION)
        ->callbackId('ask_institucion')
        ->addButtons([
            Button::create('Soy Trabajador SEP')->value('SEP'),
            Button::create('Soy Activo IMSS')->value('IMSS'),
            Button::create('Soy Jubilado o Pensionado')->value('JUBILADO'),
            Button::create('No soy ninguno')->value('Ninguno'),
        ]);
      //$this->say("Para regresar a este menú, escriba la palabra 'menu' en cualquier parte de la conversación");
      //$this->say(Constantes::PREGUNTAR_TE_URGE_UN_PRESTAMO);
      $this->ask($question, function(Answer $answer) {
          if ($answer->isInteractiveMessageReply()) {
            $this->errores = 0;
            $selectedValue = $answer->getValue();

            if($selectedValue=="No"){
              $this->bot->startConversation(new SalidaConversation());
            }
            $this->bot->startConversation(new SolicitarDatosConversation($selectedValue));


          } else {
              $this->errores += 1;
              if($this->errores >= 3){
                $this->llamarAsesor();
              } else {
                $this->say(Constantes::MENSAJE_NAVEGACION_BOTONES);
                $this->menu();
              }

          }
      });
  }

  public function askSituacionActual()
  {
    $question = Question::create("¿ASIN TEXTO?")
        ->fallback('Si no pertenece a alguna de las anteriores áreas no se podrá proceder con la solicitud, lo sentimos, estamos en contacto')
        ->callbackId('ask_area_gobierno')
        ->addButtons([
            Button::create('IMSS')->value('Area/Salud'),
            Button::create('Educación')->value('Area/Educación'),
            Button::create('Gobierno')->value('Area/Gobierno'),
            Button::create('Otra o Privada')->value('Area/Ninguna'),
        ]);

      $this->ask($question, function(Answer $answer) {
          $this->tipoInstitucion = $answer->getValue();
          if ($answer->isInteractiveMessageReply()) {
            $this->errores = 0;
            $selectedValue = $answer->getValue();
            $this->bot->startConversation(new SolicitarDatosConversation($selectedValue));
          } else {
            $this->errores += 1;
              if($this->errores >= 3){
                $this->llamarAsesor();
              } else {
                $this->say(Constantes::MENSAJE_NAVEGACION_BOTONES);
                $this->askInstituciones();
              }
          }
      });
  }


  public function askInstituciones()
  {
    $question = Question::create("¿A cuál sector del gobierno pertenece/trabaja?")
        ->fallback('Si no pertenece a alguna de las anteriores áreas no se podrá proceder con la solicitud, lo sentimos, estamos en contacto')
        ->callbackId('ask_area_gobierno')
        ->addButtons([
            Button::create('IMSS')->value('Area/Salud'),
            Button::create('Educación')->value('Area/Educación'),
            Button::create('Gobierno')->value('Area/Gobierno'),
            Button::create('Otra o Privada')->value('Area/Ninguna'),
        ]);

      $this->ask($question, function(Answer $answer) {
          $this->tipoInstitucion = $answer->getValue();
          if ($answer->isInteractiveMessageReply()) {
            $this->errores = 0;
            $selectedValue = $answer->getValue();
            $this->bot->startConversation(new SolicitarDatosConversation($selectedValue));
          } else {
            $this->errores += 1;
              if($this->errores >= 3){
                $this->llamarAsesor();
              } else {
                $this->say(Constantes::MENSAJE_NAVEGACION_BOTONES);
                $this->askInstituciones();
              }
          }
      });
  }



  //Funciones para llamar al asesor

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

  public function run()
  {
      // This will be called immediately
      $this->menu();
  }
}

?>
