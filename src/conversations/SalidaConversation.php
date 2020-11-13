<?php

namespace BotCredifintech\Conversations;

require __DIR__ . './../../vendor/autoload.php';

require_once __DIR__ . "./../Constantes.php";

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

class SalidaConversation extends Conversation
{

  var $nombre, $telefono, $email;

  public function askDatosSalida(){
    $this -> say(Constantes::MENSAJE_NO);
    $this -> say(Constantes::MENSAJE_NO_DATOS);
  }

  public function run(){
    $this -> askDatosSalida();
  }
}

?>