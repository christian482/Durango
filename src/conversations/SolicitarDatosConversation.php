<?php

namespace BotCredifintech\Conversations;

require __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . "/../Constantes.php";
require_once __DIR__ . "/../prospectos/Prospecto.php";
require_once __DIR__ . "/instituciones/salud/SaludConversation.php";
require_once __DIR__ . "/instituciones/gobierno/GobiernoConversation.php";
require_once __DIR__ . "/instituciones/educacion/EducacionConversation.php";
require_once __DIR__."/SalidaConversation.php";
//require_once __DIR__ . "/../curlwrap_v2.php";
//require_once __DIR__ . "/../sendToSharpSpring.php";

use BotCredifintech\Conversations\Instituciones\Salud\SaludConversation;
use BotCredifintech\Conversations\Instituciones\Educacion\EducacionConversation;
use BotCredifintech\Conversations\Instituciones\Gobierno\GobiernoConversation;
use BotCredifintech\Conversations\SalidaConversation;
use BotCredifintech\Prospectos\Prospecto;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Facebook\Extensions\Message;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

use Mpociot\BotMan\Cache\DoctrineCache;

use BotCredifintech\Constantes;

class SolicitarDatosConversation extends Conversation{

  private $selectedValue;
  private $prospecto;

  public function __construct($selectedValue)
  {
      $this->selectedValue = $selectedValue;
      $this->prospecto = new Prospecto();
  }

  public function askInformacion(){
    $sv = $this->selectedValue;
    $p = $this->prospecto;
    $this -> iniciaConversacion($p, $sv);
    //$this -> askNombre($p, $sv);
  }

  //Funciones para juntar datos
  public function iniciaConversacion($p, $sv){
            if($sv=='IMSS'){
                $this->say(Constantes::MENSAJE_SOY_IMSS);
            }else if($sv=='PEMEX'){
                  $this->say(Constantes::MENSAJE_SOY_PEMEX);
            }else if($sv=='SNTE29'){
                  $this->say(Constantes::MENSAJE_SOY_SNT29);
            }else if($sv=='Ninguno'){
              $this->say(Constantes::MENSAJE_SOY_NINGUNO);
              $this-> askNombre($p, $sv);
            }
            $this->say(Constantes::MENSAJE_ESCRIBA);
            $this-> askNombre($p, $sv);
  }


  //Funciones para juntar datos
  public function askNombre($p, $sv){
    $this -> ask(Constantes::PEDIR_NOMBRE, function(Answer $response) use ($p, $sv){
      $nombre = $response->getText();
      $p->nombre = $nombre;
      $this-> askApellido($p, $sv);
    });
  }

  public function askApellido($p, $sv){
    $this -> ask(Constantes::PEDIR_APELLIDO, function(Answer $response) use ($p, $sv){
      $apellido = $response->getText();
      $p->apellido = $apellido;
      $this-> askTelefono($p, $sv);
    });
  }

  public function askNumeroIMSS($p, $sv){
    $this -> ask(Constantes::ESCRIBE_NUMERO_IMSS, function(Answer $response) use ($p, $sv){
      $numIMSS = $response->getText();
      $p->convenio = $numIMSS;
      $this-> enviarDatosSinFoto($p, $sv);
    });
  }

  public function askTelefono($p, $sv){
    $this -> ask(Constantes::PEDIR_TELEFONO, function(Answer $response) use ($p, $sv){
      $telefono = $response->getText();
      $p->telefono = $telefono;
      if($sv=='IMSS'){
          $this->askNumeroIMSS($p, $sv);
      }else if($sv=='PEMEX'){
            $this->askImagenesPEMEX($p, $sv);
      }else if($sv=='SNTE29'){
            $this->askImagenesSNTE29($p, $sv);
      }else if($sv=='Ninguno'){
        $this->say(Constantes::MENSAJE_ESCRIBA);
      }
    });
  }

  public function askImagenesPEMEX($p, $sv) {
    $this->askForImages(Constantes::ADJUNTA_TRILLAS, function ($images) use ($p, $sv) {
      $p->identificacion = $images;
      // Primer guardado de información

      $contact_json =array(
        "properties"=>array(
          array(
            "name"=>"first_name",
            "value"=>$p->nombre,
            "type"=>"SYSTEM"
          ),
          array(
            "name"=>"last_name",
            "value"=>$p->apellido,
            "type"=>"SYSTEM"
          ),
          array(
            "name"=>"email",
            "value"=>$p->email,
            "type"=>"SYSTEM"
          ),
          array(
              "name"=>"phone",
              "value"=>$p->telefono,
              "type"=>"SYSTEM"
          ),
          array(
              "name"=>"company",
              "value"=>$sv,
              "type"=>"SYSTEM"
          ),
        ),
      );

      $contact_json = json_encode($contact_json);
      $output = curl_wrap("contacts", $contact_json, "POST", "application/json");

      $fromCRM = curl_wrap("contacts/search/email/".$p->email, null, "GET", "application/json");
      $fromCRMarr = json_decode($fromCRM, true, 512, JSON_BIGINT_AS_STRING);
      $id = $fromCRMarr["id"];

      $p->id = $id;

      foreach ($images as $image) {
        $url = $image->getUrl(); // The direct url
        $this->say('imagen.'.$url );
        $note_INE = array(
          "subject"=>"Imagen de identificación",
          "description"=>$url,
          "contact_ids"=>array($p->id),
        );
        $note_INE = json_encode($note_INE);

        $note_result = curl_wrap("notes", $note_INE, "POST", "application/json");


      }
      //$this->enviarASharSpring($p, $sv,$url);
      //$this->say('Perfecto, te contactara un asesor para darte a conocer los beneficios que tenemos para ti. ' );
    });
  }

  public function askImagenesSNTE29($p, $sv) {
    $this->askForImages(Constantes::ADJUNTA_TALONES, function ($images) use ($p, $sv) {
      $p->identificacion = $images;
      // Primer guardado de información

      $contact_json =array(
        "properties"=>array(
          array(
            "name"=>"first_name",
            "value"=>$p->nombre,
            "type"=>"SYSTEM"
          ),
          array(
            "name"=>"last_name",
            "value"=>$p->apellido,
            "type"=>"SYSTEM"
          ),
          array(
            "name"=>"email",
            "value"=>$p->email,
            "type"=>"SYSTEM"
          ),
          array(
              "name"=>"phone",
              "value"=>$p->telefono,
              "type"=>"SYSTEM"
          ),
          array(
              "name"=>"company",
              "value"=>$sv,
              "type"=>"SYSTEM"
          ),
        ),
      );

      $contact_json = json_encode($contact_json);
      $output = curl_wrap("contacts", $contact_json, "POST", "application/json");

      $fromCRM = curl_wrap("contacts/search/email/".$p->email, null, "GET", "application/json");
      $fromCRMarr = json_decode($fromCRM, true, 512, JSON_BIGINT_AS_STRING);
      $id = $fromCRMarr["id"];

      $p->id = $id;

      foreach ($images as $image) {
        $url = $image->getUrl(); // The direct url

        $note_INE = array(
          "subject"=>"Imagen de identificación",
          "description"=>$url,
          "contact_ids"=>array($p->id),
        );
        $note_INE = json_encode($note_INE);

        $note_result = curl_wrap("notes", $note_INE, "POST", "application/json");


      }
      //$this->enviarASharSpring($p, $sv,$url);
      //$this->say('Perfecto, te contactara un asesor para darte a conocer los beneficios que tenemos para ti. ' );
    });
  }

  public function cierre($p, $sv){
    if($sv=='Jubilado' || $sv=='Pensionado'){
        $texto ="Escriba su numero de seguro social (En caso de no contar con el escriba: 'x')";
    }else if($sv=='Ninguno'){
      $texto = "¿En dónde trabaja?";
    }

    $this -> ask($texto, function(Answer $response) use ($p, $sv){
      $email = $response->getText();
      $p->convenio = $email;
      $this-> contestacionFinal($p, $sv);
    });

  }

  public function askEmail($p, $sv){
    $this -> ask(Constantes::PEDIR_EMAIL, function(Answer $response) use ($p, $sv){
      $email = $response->getText();
      $p->email = $email;
        $this-> cierre($p, $sv);
    });
  }


  public function askIdentificacion($p, $sv){
    $this -> ask("Ingrese su NSS, RFC o CURP", function(Answer $response) use ($p, $sv){
      $identificacion = $response->getText();
      $p->identificacion = $identificacion;
      $this-> contestacionFinal($p, $sv);
    });
  }


  public function stopsConversation(IncomingMessage $message)
	{
    $texto = $message->getText();
		if ($texto == 'Deme un momento') {
			return true;
		}

		return false;
  }





  public function askFoto($p, $sv)
  {
    if($sv == 'Jubilado o Pensionado IMSS'){
      $question = Question::create("Excelente, para continuar necesitaría una foto de sus ultimos 3 tarjetónes o informes. ¿Cuenta con estos documentos?")
          ->fallback('Unable to ask question')
          ->callbackId('ask_reason')
          ->addButtons([
              Button::create('Si')->value('si'),
              Button::create('No')->value('no'),
          ]);
      }else{
        $question = Question::create("Para conocer los beneficios a los que tienes acceso y
          darte la asesoria solicitada,es necesario que nos compartas una foto de tu talon o informe de pago.
          ¿Cuentas con este documento? ")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Si')->value('si'),
                Button::create('No')->value('no'),
            ]);
      }
      return $this->ask($question, function (Answer $answer) use ($p, $sv){
          if ($answer->isInteractiveMessageReply()) {
              if ($answer->getValue() === 'si') {
                  $this->askTalon($p, $sv);
              } else {
                  //$this->enviarDatosSinFoto($p, $sv);
              }
          }
      });
  }


  public function enviarDatosSinFoto($p, $sv) {
    $p->email = "christian48342@gmail.com";
    $this->say('nombre.'.$p->nombre );
    $this->say('apellido.'.$p->apellido );
    $this->say('telefono.'.$p->telefono );
    $this->say('email.'.$p->email );
    $this->say('Compañia.'.$sv );
    $this->say('identificacion.'.$p->identificacion );
    //$this->say('estado.'.$p->estado );
    $this->say('convenio.'.$p->convenio );
    /*$address = array(
        "address" => $p->identificacion,
        "city" => $p->convenio,
        "state" => $p->estado,
        "country" => "Mexico"
    );*/
    /*
    $address = array(
    "address" => $identificacion,
    "city" => "Belo Horizonte",
    "state" => $estado,
    "country" => "Mexico"
);*/
    //$this->contestacionFinal();
    ////////////////ESTA PRTE ES PARA MOSTRAR MENSAJE FINAL AL USUARIO Y DESPUES ENVIAR DATOS A CRM
    $contact_json =array(
      "properties"=>array(
        array(
          "name"=>"first_name",
          "value"=>$p->nombre,
          "type"=>"SYSTEM"
        ),
        array(
          "name"=>"last_name",
          "value"=>$p->apellido,
          "type"=>"SYSTEM"
        ),
        array(
          "name"=>"email",
          "value"=>$p->email,
          "type"=>"SYSTEM"
        ),
        array(
            "name"=>"phone",
            "value"=>$p->telefono,
            "type"=>"SYSTEM"
        ),
        array(
            "name"=>"title",
            "value"=>$p->convenio,
            "type"=>"SYSTEM"
        ),
      /*  array(
            "name"=>"address",
            "value"=>json_encode($address),
            "type"=>"SYSTEM"
        ),*/
        array(
            "name"=>"company",
            "value"=>$sv,
            "type"=>"SYSTEM"
        ),
      ),
    );

    $contact_json = json_encode($contact_json);
    $output = curl_wrap("contacts", $contact_json, "POST", "application/json");

    $fromCRM = curl_wrap("contacts/search/email/".$p->email, null, "GET", "application/json");
    $fromCRMarr = json_decode($fromCRM, true, 512, JSON_BIGINT_AS_STRING);
    $id = $fromCRMarr["id"];

    $p->id = $id;
    //$this->enviarASharSpring($p, $sv,'');
  }



  public function enviarASharSpring($p, $sv,$linkFoto){
    $params = array(
              'objects' => array (
                array(
                  'firstName'		=> $p->nombre,
                  'lastName'		=> $p->apellido,
                  'phoneNumber'	=> $p->telefono,
                  'companyName'   => $sv,
                  'website'       => $linkFoto,
                  'emailAddress'	=> $p->email
                )
              )
              );
              $output = curl_wrap($params);
	}

  public function contestacionFinal($p, $sv){
    if($sv=='Ninguno'){
      $this->say(Constantes::MENSAJE_GRACIAS3);
    }else{
      $this->say(Constantes::MENSAJE_DESPEDIDA);
    }
      //$this->enviarDatosSinFoto($p, $sv);
      //$this->say('En breve te contactara uno de nuestros asesores en tu estado.' );

      $this->say('nombre.'.$p->nombre );
      $this->say('apellido.'.$p->apellido );
      $this->say('telefono.'.$p->telefono );
      $this->say('email.'.$p->email );
      $this->say('Compañia.'.$sv );
      $this->say('identificacion.'.$p->identificacion );
      $this->say('estado.'.$p->estado );
      $this->say('convenio.'.$p->convenio );

	}


  public function run() {
    $this -> askInformacion();
  }

}
