<?php

function enviarMail($email,$subject, $message,$attach ,$isHTML ) {
  
    
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
        
                
        foreach ($attach as $value) {
           print  $value."\n";
           $mail->AddAttachment($value); 
        }
          	 
        
        $mail->IsHTML($isHTML);

        if(!$mail->Send()) {
           echo "Error: " . $mail->ErrorInfo;
        } else {
           echo "Mensaje enviado correctamente.../n <br>";
        }

    }
    
 function getImages($nro){
       
       $fullpath =  "/www/plus/file_upload/files/$nro/";
       $attatch = array();
       
       

        if ($handle = @opendir($fullpath)) { 
 
            /* This is the correct way to loop over the directory. */
            while (false !== ($img = readdir($handle))) { 
                if($img != 'Thumbs.db' && $img != '..' && $img != '.'  ){  
                   $full = $fullpath."".$img;
                   array_push($attatch,$full);
                } 
            }
            
            closedir($handle);
        }else{
            
        }
        return $attatch;
}   
    
    $date = date("d-m-Y H:i");   
    $subject = "Informe sobre Modificación de Precios x Fallas de la fecha $date";
    
     
    
    require_once("../plus/include/Y_DB.class.php");
    
    
    $db = new Y_DB();
    
    $db->Database = 'marijoa';
    
    $db->Query("SELECT nro,date_format(fecha,'%d-%m-%Y') AS fecha,usuario,h.codigo,h.p_cant as p_cant,h.p_valmin as p_valmin,h.p_compra as p_compra,
     h.p_precio_1  AS p1, p_precio_1n as p1n,
     h.p_precio_2  AS p2, p_precio_2n as p2n,
     h.p_precio_3  AS p3, p_precio_3n as p3n,
     h.p_precio_4  AS p4, p_precio_4n as p4n,
     h.p_precio_5  AS p5, p_precio_5n as p5n,    

     motivo,obs,p_fam,p_grupo,p_tipo, p_color , volumen_orig, vol_actual, t.codigo as falla,t.descrip as nombre_falla
     FROM hist_precios h, mnt_prod p, mnt_tipo_fallas t WHERE p.p_cod = h.codigo  AND  h.motivo = t.codigo   AND fecha = CURRENT_DATE");
    
    $all = "  <h1> <div> Modificaciones hoy All in One!   </div>  </h1>";
    
    if($db->NumRows() > 0){
         
        $i = 0;
        while($db->NextRecord()){
            $i++;
            $message = "";
            $message .= "<table border='1' cellpadding='2' cellspacing='2' width='96%' style='text-align:center;padding:2px'>";
            $message .= "<tr> <th> Nro </th> <th>Usuario</th><th>Fecha</th><th>Codigo</th><th>Familia</th> <th>Grupo</th> <th>Tipo</th> <th>Color</th>
            <th>Cant</th> <th>Val. Min</th>  <th>P.Compra</th>  <th>Vol.Act.S/Orig.</th>  <th>%</th>  </tr>";
            
            $nro = $db->Record['nro']; 
            $usuario = $db->Record['usuario']; 
            $fecha = $db->Record['fecha']; 
            $codigo = $db->Record['codigo'];
            $fam = $db->Record['p_fam'];
            $grupo = $db->Record['p_grupo'];
            $tipo = $db->Record['p_tipo'];
            $color = $db->Record['p_color'];
            $obs = $db->Record['obs'];
            //$falla = $db->Record['falla'];
            $nombre_falla = $db->Record['nombre_falla'];
            $vol_actual = $db->Record['vol_actual'];
            $volumen_orig = $db->Record['volumen_orig'];
            $porc =  number_format(($vol_actual * 100) / $volumen_orig , 1, ',', '.');    
            
            
            $cant = $db->Record['p_cant']; 
            $p_valmin = $db->Record['p_valmin'];
            $p_compra = $db->Record['p_compra'];
              
            $p1 = number_format($db->Record['p1'] , 0, ',', '.');
            $p2 = number_format($db->Record['p2'] , 0, ',', '.');
            $p3 = number_format($db->Record['p3'] , 0, ',', '.');
            $p4 = number_format($db->Record['p4'] , 0, ',', '.');
            $p5 = number_format($db->Record['p5'] , 0, ',', '.');
            
            $p1n = number_format($db->Record['p1n'] , 0, ',', '.');
            $p2n = number_format($db->Record['p2n'] , 0, ',', '.');
            $p3n = number_format($db->Record['p3n'] , 0, ',', '.');
            $p4n = number_format($db->Record['p4n'] , 0, ',', '.');
            $p5n = number_format($db->Record['p5n'] , 0, ',', '.');
            
            
            $n1 = "";
            $c1 = "white";
            if($p1 > $p1n){
                $n1 = "&#8595;";
                $c1 = "orange";
            }else if($p1 < $p1n) {
                $n1 = "&#8593;";
                $c1 = "green";
            }else{
              $n1 = "&equiv;";  
            }  
            
            $n2 = "";
            $c2 = "white";
            if($p2 > $p2n){
                $n2 = "&#8595;";
                $c2 = "orange";
            }else if($p2 < $p2n) {
                $n2 = "&#8593;";
                $c2 = "green";
            }else{
              $n2 = "&equiv;";  
            }  
            
            $n3 = "";
            $c3 = "white";
            if($p3 > $p3n){
                $n3 = "&#8595;";
                $c3 = "orange";
            }else if($p3 < $p3n) {
                $n3 = "&#8593;";
                $c3 = "green";
            }else{
              $n3 = "&equiv;";  
            }  
            $n4 = "";
            $c4 = "white";
            if($p4 > $p4n){
                $n4 = "&#8595;";
                $c4 = "orange";
            }else if($p4 < $p4n) {
                $n4 = "&#8593;";
                $c4 = "green";
            }else{
              $n4 = "&equiv;";  
            }  
            $n5 = "";
            $c5 = "white";
            if($p5 > $p5n){
                $n5 = "&#8595;";
                $c5 = "orange";
            }else if($p5 < $p5n) {
                $n5 = "&#8593;";
                $c5 = "green";
            }else{
              $n5 = "&equiv;";  
            }  
            
            
            $motivo = $db->Record['motivo']; 
            $obs = $db->Record['obs']; 
            
            $title_vol_actual = 'Volumen actual x Famila/Grupu/Tipo y Color con fallas de la misma Factura de Compra + la cantidad de Falla en Actual';
            
            $message .= "<tr>
                <td> $nro </td>
                <td> $usuario </td> 
                <td>$fecha</td>
                <td>$codigo</td>
                <td>$fam</td>
                <td>$grupo</td>
                <td>$tipo</td>
                <td>$color</td>
                <td>$cant</td>
                <td>$p_valmin</td>
                <td>$p_compra</td> 
                <td title='$title_vol_actual'>$vol_actual / $volumen_orig</td> 
                <td title='Porcentaje respecto del volumen Original Cargado Total x Famila/Grupu/Tipo y Color de la misma factura'>$porc</td> 
            </tr> 
            <tr>
                <td colspan='2' rowspan='2'><b>Precios Anteriores</b></td>
                <td><b>Precio 1</b></td>
                <td><b>Precio 2</b></td>
                <td><b>Precio 3</b></td>
                <td><b>Precio 4</b></td>
                <td><b>Precio 5</b></td>
            </tr>
            <tr> 
                <td>$p1</td>
                <td>$p2</td>
                <td>$p3</td>
                <td>$p4</td>
                <td>$p5</td>
            </tr>
            <tr>
                <td colspan='2'><b>Precios Modificados</b></td>
                <td><label style='background:$c1'>&nbsp;$n1&nbsp;</label>&nbsp;$p1n</td>
                <td><label style='background:$c2'>&nbsp;$n2&nbsp;</label>&nbsp;$p2n</td>
                <td><label style='background:$c3'>&nbsp;$n3&nbsp;</label>&nbsp;$p3n</td>
                <td><label style='background:$c4'>&nbsp;$n4&nbsp;</label>&nbsp;$p4n</td>
                <td><label style='background:$c5'>&nbsp;$n5&nbsp;</label>&nbsp;$p5n</td>
            </tr>
            <tr>
              <td colspan='4'><b>Falla:</b>&nbsp; $motivo / $nombre_falla </td>  <td colspan='8'><b>Obs:</b>&nbsp; $obs </td> 
            </tr>";
                     
            
            
            
            $message .= "</table>";
                        
            $attatch = getImages($nro); 
            $have_images = count($attatch);
            if($have_images > 0){  // si tiene imagenes envio email sino append
              //  enviarMail('douglas@marijoa.com',$subject,$message,$attatch,true);           // Doglas 
                enviarMail('ricardoyunis@corporaciontextil.com.py',$subject,$message,$attatch,true);      // Ricardo
                //enviarMail('gerenciacomercial@marijoa.com',$subject,$message,$attatch,true); // Andrea R.
                //enviarMail('martinacocian@hotmail.com',$subject,$message,$attatch,true);     // Sonia
                //enviarMail('saidyunis@marijoa.com.com',$subject,$message,$attatch,true);     // José Said  
		//ennviarMail('mariaestela@marijoa.com',$subject,$message,$attatch,true);     // Maria Mercedes  
                 
            }else{
                $all .= "<br><br>".$message;
            }        
            
            
        } 
        // Envio los que no tienen imagenes
        $a =  Array();
        //  enviarMail('douglas@marijoa.com',          $subject,$all,$a ,true);     // Doglas 
        enviarMail('ricardoyunis@corporaciontextil.com.py',     $subject,$all,$a,true);      // Ricardo
        enviarMail('andrea@corporaciontextil.com.py',$subject,$all,$a,true);      // Andrea R.
        enviarMail('saidyunis@corporaciontextil.com.py',    $subject,$all,$a,true);      // José Said  
	    //enviarMail('mariaestela@marijoa.com',       $subject,$all,$a ,true);     // Doglas 
         
        
        
        // Mensaje de Texto
        $date = date("d-m-Y"); 
        $smssub = "Alteración de Precios hoy Cant.: $i";
        $sms = "Modificaciones hoy All in One, Cantidad de Productos modificados $i Reporte sobre Modificacion de Precios x Fallas disponible en Menu del sistema Varios --> Reportes Varios --> Reporte de Modificacion de Precios"; 
         
        
        // Mensaje al Telefono
        // enviarMail('0983593615@mms.tigo.com.py',$smssub,$sms,$a,false); // Douglas
        //enviarMail('0981568859@mms.tigo.com.py',$smssub,$sms,$a,false); // Ricardo
        //enviarMail('0986407567@mms.tigo.com.py',$smssub,$sms,$a,false); // Andrea Rotzen
        // enviarMail('0985745100@mms.tigo.com.py',$smssub,$sms,$a,false); // Sonia
        //enviarMail('0981183241@mms.tigo.com.py',$smssub,$sms,$a,false); // José Said   
        
          
        
    }
    
    //enviarMail('0981568859@mms.tigo.com.py',$subject,$message);
    
    
    
    ?>
