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
    
    $subject = "Informe Gramajes Fuera del 10% sobre el Gramaje de Muestra";
    
    require_once("../plus/include/Y_DB.class.php");
    
    
    $db = new Y_DB();
    
    $db->Database = 'marijoa';
    
    $db->Query("SELECT p.p_cod AS codigo, a.aj_usuario AS usuario, date_format(a.aj_fecha,'%d-%m-%Y') AS Fecha,a.aj_hora AS Hora , p_fam  AS Sector, p_grupo AS Grupo, p_tipo AS Tipo ,p_cant_compra,
    a.aj_inicial as aj_ini,a.aj_ajuste as ajuste,a.aj_final as aj_final, p.p_kg_real as kg_real,m.p_ancho as ancho, p_tara, p.p_gram AS Gramaje_muestra,m.p_gram AS Gramaje_final,
    ROUND(p.p_gram - (p.p_gram * 10 / 100),0) AS 'GM-10', ROUND(p.p_gram + (p.p_gram * 10 / 100),0) AS 'GM+10'  
    FROM packing_list p , mov_compras c, mnt_prod m, mnt_ajustes a WHERE   p.pack_ref = c.c_ref AND c.c_fecha > '2014-03-01' 
    AND p.p_cod = m.p_cod   AND m.prod_fin_pieza <> 'Tr' AND p.p_cod = a.aj_prod AND aj_motivo = 'Ajuste en metraje original'

    AND m.p_gram   NOT  BETWEEN  ROUND(p.p_gram - (p.p_gram * 10 / 100),0) AND ROUND(p.p_gram + (p.p_gram * 10 / 100),0)  

    AND a.aj_fecha =   DATE_SUB(CURRENT_DATE,INTERVAL 1 DAY)    ");

 

    
    if($db->NumRows() > 0){
        $message = "";
            $message .= "<table border='1' cellpadding='1' cellspacing='1'>";
            $message .= "<tr> <th> Codigo </th> <th>Usuario</th><th>Fecha</th><th>Hora</th><th>Sectpr</th> <th>Grupo</th> <th>Tipo</th> <th>Cant. Compra</th> <th>C.Inicial</th> <th>Ajuste</th>  <th>C. Final</th> <th>Kg. Real</th><th>Ancho</th> <th>Tara</th> <th>Gram. Muestra</th> <th>Gram. Actual</th>    </tr>"; 
        $i = 0;
        while($db->NextRecord()){
            $i++; 
            $codigo = $db->Record['codigo']; 
            $usuario = $db->Record['usuario'];
            $fecha = $db->Record['Fecha'];
            $hora = $db->Record['Hora'];
            $sector = $db->Record['Sector'];
            $grupo = $db->Record['Grupo'];
            $tipo = $db->Record['Tipo'];
            $p_cant_compra = $db->Record['p_cant_compra'];
            $aj_ini = $db->Record['aj_ini'];
            $ajuste = $db->Record['ajuste'];
            $aj_final = $db->Record['aj_final'];
            $kg_real = $db->Record['kg_real'];
            $ancho = $db->Record['ancho'];
            $p_tara = $db->Record['p_tara'];
            $Gramaje_muestra = $db->Record['Gramaje_muestra'];
            $Gramaje_final = $db->Record['Gramaje_final'];  
            $message .= "<tr> <td> $codigo </td> <td>$usuario</td><td>$fecha</td><td>$hora</td><td>$sector</td> <td>$grupo</td> <td>$tipo</td> <td>$p_cant_compra</td> <td>$aj_ini</td> <td>$ajuste</td>  <td>$aj_final</td> <td>$kg_real</td><td>$ancho</td> <td>$p_tara</td> <td>$Gramaje_muestra</td> <td>$Gramaje_final</td>    </tr>"; 
            
        }
        $message .="</table>";
    }   

    
         
    enviarMail('douglas@marijoa.com',$subject,$message);
    //enviarMail('0981568859@mms.tigo.com.py',$subject,$message);
    //enviarMail('0983593615@mms.tigo.com.py',$subject,$message);
    
    
    ?>
