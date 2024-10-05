<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require "/opt/cpanel/ea-php54/root/usr/share/pear/Mail.php";

//----------------Estas variables de Dominio las dejamos asi por el momento de favor:--------------

$mailid=time()+1;
$vempresa="Fragolan Linking People";

//----------Este email esta redireccionado a fragolan.mail@gmail.com, fragolan.sistemas@gmail.com y fragolan.soporte@gmail.com:------------------

$vemail="actualizaciones@fragolan.com";
$vemailhost="mail.fragolan.com";
$vemailusuario="actualizaciones@fragolan.com";
$vemailpassword="l3&WQR@Dh9#A";

//----Variables del Mensaje:-------------------------------------------------------------------

$asunto="Email de Actualizaciones de ".$vempresa." (Mail ID: ".$mailid.")";
$mensaje="El sistema realizó las siguientes Actualizaciones:<br><br>";

//-----------Aquí van las Actualizaciones que se generaron:----------------------------

$mensaje.="Segunda prueba Correcta";

//-----NO TOCAR:--------------------------------------------------------------------------------

$elhtml=$mensaje;
$mensaje=strip_tags($mensaje);
$from = $vemail;
$to = $vemail;
$replyto = $vemail;
$subject = $asunto;
$boundary = uniqid();
$content_type = 'text/html; boundary=' . $boundary;
$lafechamail=date(DATE_RFC2822);
$body = $elhtml;

$headers = array ('From' => $from, 'To' => $to, 'Subject' => $subject, 'Reply-To' => $replyto, 'MIME-Version' => '1.0', 'Content-Type' => $content_type, 'Date' => $lafechamail);
$smtp = Mail::factory('smtp', array ('host' => $vemailhost, 'auth' => true, 'username' => $vemailusuario, 'password' => $vemailpassword));
$mail = $smtp->send($to, $headers, $body);

if(PEAR::isError($mail)) {
	echo "<br><br>Ocurrió un Error:<br><br>".$mail->getMessage()."<br>";
}
else {
	echo "<br><br>Mail enviado!";
}
