<?php

namespace BotCredifintech\CRM;

require_once __DIR__ . "../curlwrap_v2.php"; 

function getUserByEmail($email){
  return curl_wrap("contacts/search/email/".$email, null, "GET", "application/json");
}

?>