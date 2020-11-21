<?php

function enviarMail($email,$subject, $message ) {
 

        //$sendTo = "$mail, tratohechopy@gmail.com, bordonfederico@gmail.com, dembogurski@gmail.com";
        
		//para el envío en formato HTML
		/*$headers = "Content-type: text/html;";
		
        $headers .= "From: Services Tratohecho Paraguay <no-reply@tratohecho.com.py>";
        $headers .= "<" . $mail . ">\r\n "; */
 
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
        
    
        $mail->AddAddress("$email","$email" );
         
	//$mail->AddBCC("douglas@marijoa.com","Ing. Douglas A. Dembogurski"); 
        
        $mail->AddAttachment("test.pdf");
		 
        
        $mail->IsHTML(true);

        if(!$mail->Send()) {
           echo "Error: " . $mail->ErrorInfo;
        } else {
           echo "Mensaje enviado correctamente";
        }

    }
    
    $subject = "Informe Sistema Marijoa";
    $message .= "<b> Esto va salir en Negrita </b>  <i>Esto en Kursiva </i>  ";
    $message .= "<table border='1'>";
    $message .= "<tr> <td> Dato </td><td> Dato2 </td><td> Dato3 </td> </tr>";
    $message .= "<tr> <td> Dato </td><td> Dato2 </td><td> Dato3 </td> </tr>";
    $message .= "<tr> <td> Dato </td><td> Dato2 </td><td> Dato3 </td> </tr>";
    $message .= "<tr> <td> Dato </td><td> Dato2 </td><td> Dato3 </td> </tr>";
    $message .= "<tr> <td> Dato </td><td> Dato2 </td><td> Dato3 </td> </tr>";
    $message .= "<tr> <td> Dato </td><td> Dato2 </td><td> Dato3 </td> </tr>";
    $message .= "</table>";

    
         
    enviarMail('douglas@marijoa.com',$subject,$message);
    //enviarMail('0981568859@mms.tigo.com.py',$subject,$message);
    //enviarMail('0983593615@mms.tigo.com.py',$subject,$message);
    
    
    ?>