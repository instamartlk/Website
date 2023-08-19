<?php

// unique_order_id|total_amount
$plaintext = '525|10';
$publickey = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDVcm0QpmsGNul0yuoaAbgMkMc0
Hy6QZ6p1iphtwwzhHIbo6w7ZFVxCbhfZH9PHOcw720zTPuRmXJjj3H3PJ2AZK+UV
sVtCkVPJMzIJSpA+1mYeMunD+TdMWY/KU2lCCnDBplN5UI6rDQaajH9mo2ELsWfM
nL11hRIuJPWB6Gu3nQIDAQAB
-----END PUBLIC KEY-----";
//load public key for encrypting
openssl_public_encrypt($plaintext, $encrypt, $publickey);

//encode for data passing
$payment = base64_encode($encrypt);
//checkout URL
$url = 'https://stagingxpay.info/index.php?route=checkout/billing';

//custom fields
//cus_1|cus_2|cus_3|cus_4
$custom_fields = base64_encode('cus_1|cus_2|cus_3|cus_4');

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="John Doe">
    <title>WebXPay | Sample checkout form</title>
  </head>
  <body>     	  
       <form action="<?php echo $url; ?>" method="POST">
			First name: <input type="text" name="first_name" value="John"><br>
			Last name: <input type="text" name="last_name" value="Doe"><br>
			Email: <input type="text" name="email" value="customer_email@email.com"><br>
			Contact Number: <input type="text" name="contact_number" value="0773606370"><br>
			Address Line 1: <input type="text" name="address_line_one" value="46/46, Green Lanka Building"><br>
			Address Line 2: <input type="text" name="address_line_two" value="Nawam Mawatha"><br>
			City: <input type="text" name="city" value="Colombo"><br>
			State: <input type="text" name="state" value="Western"><br>
			Zip/Postal Code: <input type="text" name="postal_code" value="10300"><br>
			Country: <input type="text" name="country" value="Sri Lanka"><br>
			currency: <input type="text" name="process_currency" value="LKR"><br> <!-- currency value must be LKR or USD -->
			CMS : <input type="text" name="cms" value="PHP">
			custom: <input type="text" name="custom_fields" value="<?php echo $custom_fields; ?>">
			Mechanism: <input type="text" name="enc_method" value="JCs3J+6oSz4V0LgE0zi/Bg==">
			<br/>		   
			<!-- POST parameters -->
			<input type="hidden" name="secret_key" value="ed1d252f-e1c9-4d9a-a957-563987e82d9d" >  
			<input type="hidden" name="payment" value="<?php echo $payment; ?>" >                         
			<input type="submit" value="Pay Now" >
        </form>      
  </body>
</html>
