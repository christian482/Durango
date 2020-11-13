<?php
namespace BotCredifintech\Conversations\ProspectoConversation;

require __DIR__ . './../../vendor/autoload.php';

require_once __DIR__ . "./../Constantes.php";

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Messages\Conversations\Conversation;

use Mpociot\BotMan\Cache\DoctrineCache;

class ProspectoConversation extends Conversation{
  protected $prospecto;

  public function __construct($user)
  {
      $this->$prospecto = $user;
  }

}

?>