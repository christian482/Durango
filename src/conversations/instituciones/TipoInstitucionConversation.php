<?php
namespace BotCredifintech\Conversations\Instituciones;

require __DIR__ . './../../../vendor/autoload.php';

require_once __DIR__ . "./../../Constantes.php";

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use Mpociot\BotMan\Cache\DoctrineCache;

use BotCredifintech\Constantes;

class TipoInsitucionConversation extends Conversation
{
    protected $tipoInstitucion;

    protected $listaGobierno;
    protected $listaSalud;
    protected $listaEducacion;

    public function AskTipo()
    {
        $question = Question::create("¿A cuál de las siguientes áreas perteneces?")
          ->fallback('Si no pertenece a alguna de las anteriores áreas no se podrá proceder con la solicitud, lo sentimos, estamos en contacto')
          ->callbackId('ask_institucion')
          ->addButtons([
              Button::create('Gobierno')->value('Tipo/Gobierno'),
              Button::create('Privada')->value('Tipo/Privado'),
              Button::create('Soy pensionado')->value('Tipo/Pensionado'),
          ]);

        $this->ask($question, function(Answer $answer) {
            $this->tipoInstitucion = $answer->getValue();
            if ($answer->isInteractiveMessageReply()) {
              $selectedValue = $answer->getText(); // Tipo/Gobierno / Tipo/Privado / Tipo/Pensionado
              if($selectedValue=="'Tipo/Privado"){
                $this->say(Constantes::MENSAJE_NO);
              }
              if($selectedValue=="Tipo/Gobierno"){
                $this->askGobierno();
              }
              if($selectedValue=="Tipo/Pensionado"){
                $this->say('Pensionado');
              }
              return null;
            }
        });
    }

    public function askGobierno()
    {
      $question = Question::create("Seleccione la opción de la cuál quiere ver la lista de instituciones relacionadas con nosotros")
          ->fallback('Si no pertenece a alguna de las anteriores áreas no se podrá proceder con la solicitud, lo sentimos, estamos en contacto')
          ->callbackId('ask_area_gobierno')
          ->addButtons([
              Button::create('Salud')->value('Area/Salud'),
              Button::create('Educación')->value('Area/Educación'),
              Button::create('Gobierno')->value('Area/Gobierno'),
          ]);

        $this->ask($question, function(Answer $answer) {
            $this->tipoInstitucion = $answer->getValue();
            if ($answer->isInteractiveMessageReply()) {
              $selectedValue = $answer->getValue(); // Tipo/Gobierno / Tipo/Privado / Tipo/Pensionado
              if($selectedValue=="Area/Salud"){
                $this->say('Estas son las instituciones con las que tenemos convenio');
                $this->say('[Lista]');
                $this->askPertenencia();
              }
              if($selectedValue=="Area/Educación"){
                $this->say('Estas son las instituciones con las que tenemos convenio');
                $this->say('[Lista]');
                $this->askPertenencia();
              }
              if($selectedValue=="Area/Gobierno"){
                $this->say('Estas son las instituciones con las que tenemos convenio');
                $this->say('[Lista]');
                $this->askPertenencia();
              }
            }
        });
    }

    function askPertenencia(){
      $question = Question::create("¿Pertenece a alguna de las instituciones en la lista?")
          ->fallback('Si no pertenece a alguna de las anteriores áreas no se podrá proceder con la solicitud, lo sentimos, estamos en contacto')
          ->callbackId('ask_institucion_gobierno')
          ->addButtons([
              Button::create('Si')->value('Pertenencia/Si'),
              Button::create('No')->value('Pertenencia/No'),
          ]);

        $this->ask($question, function(Answer $answer) {
            $this->tipoInstitucion = $answer->getValue();
            if ($answer->isInteractiveMessageReply()) {
              $selectedValue = $answer->getText(); // Tipo/Gobierno / Tipo/Privado / Tipo/Pensionado
              if($selectedValue=="Area/Salud"){
                $this->askDatos();
              }
              if($selectedValue=="Pertenencia/No"){
                $this->say(Constantes::MENSAJE_NO);
              }
            }
        });
    }

    public function askInstitucionAparte(){
      $question = "Le pedimos nos proporcione los siguientes datos para avisarle de futuras actualizaciones";
    }

    public function run()
    {
        // This will be called immediately
        $this->AskTipo();
    }
}

?>