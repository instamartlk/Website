<?php

//decode & get POST parameters
$payment = base64_decode($_POST ["payment"]);
$signature = base64_decode($_POST ["signature"]);
$custom_fields = base64_decode($_POST ["custom_fields"]);
//load public key for signature matching
$publickey = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDVcm0QpmsGNul0yuoaAbgMkMc0
Hy6QZ6p1iphtwwzhHIbo6w7ZFVxCbhfZH9PHOcw720zTPuRmXJjj3H3PJ2AZK+UV
sVtCkVPJMzIJSpA+1mYeMunD+TdMWY/KU2lCCnDBplN5UI6rDQaajH9mo2ELsWfM
nL11hRIuJPWB6Gu3nQIDAQAB
-----END PUBLIC KEY-----";
openssl_public_decrypt($signature, $value, $publickey);

$signature_status = false ;

if($value == $payment){
	$signature_status = true ;
}

//get payment response in segments
//payment format: order_id|order_refference_number|date_time_transaction|payment_gateway_used|status_code|comment;
$responseVariables = explode('|', $payment);      

if($signature_status == true)
{
	//display values
	echo $signature_status;
	$custom_fields_varible = explode('|', $custom_fields);
	var_dump($custom_fields_varible);
	echo '<br/>';
	var_dump($responseVariables);
}else
{
	echo 'Error Validation'; 
}
	
?>  