<?php

namespace BotCredifintech;

class Constantes {

  public function __construct(){
    echo __CLASS__ . '<br />';
  }

  //Configuración de la aplicación

  const APP_SECRET = "e4647b87a6b18da6803bddc3b3349674";
  const VERIFICATION = "d8wkg9wkflaaeha54qyhf5yadfjaibs3iwro203852";

  const APP_SECRET_TEST = "e4647b87a6b18da6803bddc3b3349674";
  const VERIFICATION_TEST = "HOLAmundoCREDIfintech1010101";
  const BOTENCIO_TEST = "EAAGrT16HtJgBAEP8D5cBZCZAAFH3Sn8aclF2WUZCPH61cIc3ZC05zl5KMvmmnZAAxHwQ7k0PZBxuVNsNhgDYzvslPhCy9BEJ7c0tToGV7Y8CVxgdGi4HRp4ZCLV7tWHcaAYSF6sEd5ml78NtdFTYcl19cW5TQPhxEDNkZCYG3snm7JTGyiLSyZCv9AaRhZAbDdHlkZD";

  public $page_token_array;

  public static function generateTokenArray(){
     return $page_token_array = [
      "T_BOTENCIO" => "EAAGrT16HtJgBAPbslwpr637H9ZBrPY6oNWfoQxHBkoZCzpZCE1VbGcHKfW8ZAigtTdJgmZAK0h1gS0TBYDvEZAxrpZB7v8EWFIU4lX2D1NJwqyPZAboAVHlf2mNraS9Uzw3kpeEHRdWzTM0ZC6rbscEOGD5Re4rQeFUBA7lG1XTbQHwbHj0AzKAZBT",
      "T_CF" => "EAAGrT16HtJgBANcy1trAD3kht0pIoW18gHaaUY9DcXjsTGBfifvKxXEhtGox1yd6iWqRlpiAKrTxwmM9Ow1I71x7ZBI0OOFgsxuXD3rx1bxk55NlovIwJAoi5EWpNGYsMRDcKurUZCL2EWxen8fWZCCX9L6c7S2eHiFt0ZC8eZA4qVHym8yOT",
    ];
  }

  //Mensajes para conversaciones

  const PEDIR_NOMBRE = "Escriba su nombre (sin apellidos)";
  const PEDIR_APELLIDO = "Escriba sus apellidos";
  const PEDIR_TELEFONO = "Escriba su número de teléfono";
  const PEDIR_NSS = "Escriba su NSS";
  const PEDIR_MATRICULA = "Escriba su número de matrícula";
  const PEDIR_INE = "Para conocer los beneficios a los que tienes acceso y darte la asesoria solicitada, es necesario que nos compartas una foto de tu talon o informe de pago. ¿Cuentas con este documento? ";
  const PEDIR_TALON = "Para conocer los beneficios a los que tienes acceso y darte la asesoria solicitada, es necesario que nos compartas una foto de tu talon o informe de pago. ¿Cuentas con este documento? ";
  const PEDIR_TALON_NOMINA = "Por favor tome una foto de sutalón de nómina";
  const PEDIR_INFORME_PAGO = "Por favor tome una foto de su Informe de pago";
  const PEDIR_EMAIL = "Por favor ingrese su correo electrónico";
  const PEDIR_MONTO = "¿Cuánto dinero necesita?";
  const PEDIR_NUM_DELEGACION_ADSCRIPCION = "Por favor, ingrese el número de la delegación en la que está adscrito";
  const PEDIR_FOTO = "Toma la foto de messenger o seleccionala y enviala para seguir. ";

  // Este mensaje se muestra cuando el usuario no coincide con el perfil de prospecto.
  const MENSAJE_NO = "Lo sentimos, aún no contamos con convenios para tu área, le agradecemos su interés y sugerimos que nos siga en nuestras redes para futuras actualizaciones.";

  // Se despliega para solicitar más datos al usuario que no coincidió con los prospectos.
  const MENSAJE_NO_DATOS = "Gracias por su tiempo, nos contactaremos con usted en cuanto tengamos servicios disponibles para su caso";

  // Preguntas
  const PREGUNTA_NOMBRE = "Escriba su nombre completo";
  const PREGUNTA_TELEFONO = "Proporcionenos su número telefónico o celular";

  // Preguntas Pensionados
  const PREGUNTA_PENSIONADO_MATRICULA = "Proporcionenos su matrícula de la institución";
  const PREGUNTA_PENSIONADO_DELEGACIÓN = "Escriba el nombre de la delegación donde se encuentra la estancia donde trabajó";

  const MENSAJE_NAVEGACION_BOTONES = "Por favor, conteste presionando los botones que aparecen en pantalla después de cada mensaje";
  const MENSAJE_AYUDA_ASESOR = "Actualmente se está comunicando a través de un sistema automatizado, pero parece requerir ayuda adicional, un asesor especializado se comunicará con usted";
  const MENSAJE_INSTRUCCIONES_LLAMAR_ASESOR = "Parece tener problemas con nuestro servicio automatizado, para llamar a un asesor especializado para que lo atienda personalmente, ingrese la palabra 'asesor' y espere a que el personal especializado se contacte con usted";

  //Antes de pedir los datos se verifica que el prospecto cuente con la documentación requerida
  const PREGUNTA_DOCUMENTACION = "¿Cuenta con los datos/documentos que se requieren?";

  //Estos dos mensajes conforman el enlistado y confirmación de que se cuenta con los datos requeridos
  const MENSAJE_DATOS_REQUERIDOS = "Para realizar esta solicitud de crédito se requerirán los siguientes datos/documentos, presione el boton de todo listo cuando esté seguro de tener toda la información a la mano";

  //Mensaje de finalizacion del registro
  const MENSAJE_SOLICITUD_TERMINADA = "¡Todo listo! en un momento un asesor especializado se contactará con usted";


  const PREGUNTAR_ESTADO = "¿En que estado te encuentras?";
  const PREGUNTAR_DEPENDENCIA = "¿En qué tipo de dependencia trabajas?";
  const SELECCIONE_OPCION = "Seleccione una opción:";
  const PREGUNTAR_TE_URGE_UN_PRESTAMO ="¿Te urge un préstamo? Solo sigue los siguientes pasos para que te contactemos. ";
  const PREGUNTAR_CONVENIO ="Selecciona uno de los siguientes convenios.";
  const ESCRIBRE_DONDE_TRABAJAS = "Escribe donde trabajas:";


  const MENSAJE_SOY_IMSS = "Perfecto, por ser Personal Activo del IMSS tiene derecho a su credito personalizado.";
  const MENSAJE_SOY_PENSIONADO = "Perfecto, por ser Pensionado Ley tiene derecho a su crédito personalizado. ";
  const MENSAJE_SOY_JUBILADO = "Perfecto, por ser Jubilado IMSS tiene derecho a su crédito personalizado.";
  const MENSAJE_SOY_NINGUNO = "Otorgamos Créditos a otros Sectores como ( SEP, UNAM, PEMEX, CFE, SNTE, ISSSTE, GOBIERNO FEDERAL y DEL ESTADO) Entre Otros.";
  const MENSAJE_ESCRIBA = "Escriba los datos que le solicitamos y en breve se contactará un agente especializado. ";

  const ESCRIBE_NUMERO_IMSS = "Escriba su numero de seguro social (En caso de no contar con el escriba: “x”)";
  const ESCRIBE_NUMERO_MATRICULA = "Escriba su matricula (En caso de no contar con el escriba: “x”)";
  const ADJUNTA_TALONES = "Si cuenta con los últimos talones de pago envíe una foto, si no omita este paso. ";  
  const MENSAJE_DESPEDIDA = "Excelente. Si tiene alguna duda mande un WhatsApp al Director Erick Maldonado Acosta y con Gusto le Atendera Tel: 4811462763";
  const EXPLICAR_SERVICIO = "Credifintech se enfoca en apoyar a todos los Jubilados,Pensionados y Activos del IMSS asi como de Otras Dependencias de Gobierno Federales y Estatales con una Amplia Gama de Servicios Financieros Profesionles.";
}
