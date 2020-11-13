<?php
   /** Get all leads with a limit of 500 results */
   $limit = 500;                                                                         
   $offset = 0;                                                                          
                                                                                          
   $method = 'createLeads';                                                                 
   $params = array(
	'objects' => array (
		array( 
			'firstName'		=> 'Christiannnn',
			'lastName'		=> 'Perez',
			'phoneNumber'	=> '4451147863',
			"companyName"   => 'intera',
			'emailAddress'	=> 'christian482@accelweb.ca'
		)
	)
);    
   $requestID = session_id();       
   $accountID = 'F47BF91480DC9BB7126544EF8FFC3E63';
   $secretKey = '00C274DE6B1D2AA4ED5D5494BB4A3F65';                                                     
   $data = array(                                                                                
       'method' => $method,                                                                      
       'params' => $params,                                                                      
       'id' => $requestID,                                                                       
   );                                                                                            
                                                                                                 
   $queryString = http_build_query(array('accountID' => $accountID, 'secretKey' => $secretKey)); 
   $url = "http://api.sharpspring.com/pubapi/v1/?$queryString";                             
                                                                                                 
   $data = json_encode($data);                                                                   
   $ch = curl_init($url);                                                                        
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                              
   curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                  
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                               
   curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                   
       'Content-Type: application/json',                                                         
       'Content-Length: ' . strlen($data),
       'Expect: '                                                        
   ));                                                                                           
                                                                                                 
   $result = curl_exec($ch);                                                                     
   curl_close($ch);                                                                              
                                                                                                 
   echo $result;                                                                                 
  
?>