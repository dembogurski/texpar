<?php


echo getcwd();

chdir("/www/mail/");

echo getcwd();

require_once("../plus/include/Y_DB.class.php");
    
    
    $db = new Y_DB();    
    $db->Database = 'marijoa';
    
    $db->Query("SELECT nombre, suc,DATE_FORMAT(desde,'%d-%m-%Y') AS Inicio,DATE_FORMAT(hasta,'%d-%m-%Y') AS Culminacion, tarea AS Obs FROM  cronog_jornales WHERE hasta = CURRENT_DATE;");
    
    if($db->NumRows() > 0){
        $message = "";
        $i = 0;
        while($db->NextRecord()){
            $i++;
            $nombre = $db->Record['nombre'];
            $suc = $db->Record['suc'];
            $Inicio = $db->Record['Inicio'];
            $Culminacion = $db->Record['Culminacion'];
            $Obs = $db->Record['Obs'];            
            $message.="<b>N&deg;:</b> $i <hr><b>Nombre:</b>$nombre   <br><b>Sucursal:</b> $suc   <br><b>Inicio:</b> $Inicio  <b>Culminacion:</b>$Culminacion   <br>";
            $message.="<b>Obs:</b>  $Obs<br><hr><br><br>";
        }
        $douglas = "dembogurski@gmail.com";
        $rrhh = "rrhh@corporaciontextil.com.py";
        $subject = "Avisos";
	 
        enviarMail($douglas, $subject, $message, true);  
        enviarMail($rrhh, $subject, $message, true);   
        //enviarMail('0986393670@mms.tigo.com.py',"Hola Soy el Servidor, se esta llenando el disco",$text,true);
    }
 

function enviarMail($email,$subject, $message, $isHTML ) {
  
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
        $mail->FromName = "AVISOS!";
        $mail->Subject = "$subject";
        $mail->MsgHTML("$message");
        $mail->AltBody = "$message";
    
        $mail->AddAddress("$email","$email" );
        
        $mail->IsHTML($isHTML);

        if(!$mail->Send()) {
           echo "Error: " . $mail->ErrorInfo;
        } else {
           echo "Mensaje enviado correctamente.../n <br>";
        }

    }

 
?>
