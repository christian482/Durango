<?php
/////////LINK PAGINA ->https://credifintech2020.herokuapp.com/
////////////////
///////////
//Este archivo importa todos los drivers necesarios para imoprtarlos con "use"
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/botman/botman/src/Interfaces/UserInterface.php';
require __DIR__ . '/vendor/botman/driver-facebook/src/FacebookDriver.php';
require __DIR__ . '/vendor/botman/botman/src/BotMan.php';
require __DIR__ . '/vendor/botman/botman/src/Drivers/Tests/ProxyDriver.php';
//Importando archivos necesarios
require_once __DIR__."/src/Constantes.php";
require_once __DIR__."/src/conversations/instituciones/TipoInstitucionConversation.php";
require_once __DIR__."/src/conversations/MenuConversation.php";
require_once __DIR__."/src/conversations/SalidaConversation.php";

//Extra Facebook Drivers
//require_once __DIR__."/vendor/botman/driver-facebook/src/FacebookDriver.php";
require_once __DIR__."/vendor/botman/driver-facebook/src/FacebookImageDriver.php";
require_once __DIR__."/vendor/botman/driver-facebook/src/FacebookFileDriver.php";

//Configurando namespace de clases de botman (Plantillas de facebook)
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;

use BotMan\BotMan\Drivers\DriverManager;

use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\Drivers\Facebook\Extensions\Element;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use BotMan\BotMan\Cache\DoctrineCache;
use Doctrine\Common\Cache\FilesystemCache;

//Configurando namespace de clases personalizadas
use BotCredifintech\Constantes;
use BotCredifintec\Conversations\Instituciones\TipoInsitucionConversation;
use BotCredifintech\Conversations\MenuConversation;
use BotCredifintech\Conversations\SalidaConversation;

//Ca,
// Driver de chatbot para Facebook
DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
DriverManager::loadDriver(\Botman\Drivers\Facebook\FacebookImageDriver::class);
DriverManager::loadDriver(\Botman\Drivers\Facebook\FacebookFileDriver::class);

$config = [
    'facebook' =>
	//VILLAHERMOSA
	[
  	'token' => 'EAASXewHWGFEBAGcGhrJPKElXI9t2EMjlZC5nSey2xN2prScJ8axwaS7ZC7Ltv9FATclm219fwctJqancKlvI4qU57Pag5fGh0TpVRZCP5XwEi0AsE94h6u9J48cI5wxvq7IVVGGEiZCGnp0kYWysUz98tkIXUo6bmXsZCUhD6LqmOLKYhce3T',
	'app_secret' => 'de8f0b94310a8a7c6b032d0dc4a4dd1d',
    'verification'=>'d8wkg9wkflaaeha54qyhf5yadfjaibs3iwro203852',
	],
[
  	'token' => 'EAAGrT16HtJgBALXkCgeR29RFU2YZCm8xgPewUOjr6aDdxcu8CrqGeX4NbgpcSBcEjLjhsXZABZCr5l93I1cuAG68KSD6AKSsmb0koPvK2tmtoRViP6fOw1fAZCH2NZBtg5NI8UYkTzzMCuul28fPBEZBPZBaNGvwb8KZCpEMaMxyOA9XuWBDFYYq',
	'app_secret' => 'e4647b87a6b18da6803bddc3b3349674',
    'verification'=>'d8wkg9wkflaaeha54qyhf5yadfjaibs3iwro203852',
]
];

$doctrineCacheDriver = new FilesystemCache(__DIR__);
$botman = BotManFactory::create($config, new DoctrineCache($doctrineCacheDriver));

$botman->hears('^(?!.*\basesor|ASESOR|Asesor\b).*$', function (BotMan $bot) {
  $nombre = $bot->getUser()->getFirstName();
  $incomingMessageText = $bot->getMessage()->getText();

  $nombre = $bot->getUser()->getFirstName();
  $bot -> reply("Mucho gusto  $nombre Soy Villahermosa");
  $bot -> reply("Credifintech Villahermosa se enfoca en apoyar a todos los trabajadores de SNTE 29, PEMEX e IMSS, ademas Jubilados y Pensionados. Ofreciéndoles crédito de manera rápida y sencilla.");
  $bot->reply("Para regresar a este menú, escriba la palabra 'menu' en cualquier parte de la conversación");
  $bot -> startConversation(new MenuConversation($nombre));
});


 $botman->hears('no', function (BotMan $bot) {
     $bot->reply('no 🤘');
 });

$botman->hears('.*(Menu|menu|Menú|MENU|menú).*', function(BotMan $bot) {

})->stopsConversation();

$botman->hears('.*(asesor|ASESOR|Asesor).*', function(BotMan $bot) {
    $bot->reply(Constantes::MENSAJE_AYUDA_ASESOR);
})->stopsConversation();

$botman->listen();
//echo "This is botman running";
?>
