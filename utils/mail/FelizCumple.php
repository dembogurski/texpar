<?php
require_once("../../Y_DB_MySQL.class.php");


$db = new My();
$db->Database = 'marijoa_sap';

$db->Query('SELECT nombre,apellido,email FROM usuarios WHERE DATE_FORMAT( fecha_nac,"%d-%m") =  DATE_FORMAT( CURRENT_DATE,"%d-%m") and email is not null ');

if ($db->NumRows() > 0) {
    $message = "";
    $i = 0;
    while ($db->NextRecord()) {
        $i++;
        $nombre = $db->Record['nombre'];        
        $apellido = $db->Record['apellido'];
        $email = $db->Record['email'];
        
        //$email = "dembogurski@gmail.com";
        $message.="$nombre $apellido Muy Feliz Cumplea�os!  <br>  Sistema Marijoa.";
        
        enviarMail($email, "Feliz Cumplea�os", $message, true);
    }
    
    //

    //enviarMail('0986393670@mms.tigo.com.py',"Hola Soy el Servidor, se esta llenando el disco",$text,true);
}

function enviarMail($email, $subject, $message, $isHTML) {

    require_once("phpmailer-gmail/class.phpmailer.php");
    require_once("phpmailer-gmail/class.smtp.php");

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "ssl";
    $mail->Host = "mail.marijoa.com";
	$mail->Port = 465;
    $mail->Username = "sistema@marijoa.com";
    $mail->Password = "rootdba";

    $mail->From = "sistema@marijoa.com";
    $mail->FromName = "Sistema Marijoa";
    $mail->Subject = "$subject";
    $mail->MsgHTML("$message");
    $mail->AltBody = "$message";

    $mail->AddAddress("$email", "$email");

    $mail->IsHTML($isHTML);

    if (!$mail->Send()) {
        echo "Error: " . $mail->ErrorInfo;
    } else {
        echo "Mensaje enviado correctamente a $email.../n <br>";
    }
}
?>
 