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
           echo "Mensaje enviado correctamente... $email \n <br>";
        }

    }
    
 
    
    // Solo si no es domingo
    
    $hoy =  date("D");
  
    if($hoy !='Sun'){
      

    
    
    $date = date("d-m-Y H:i");   
    $subject = "Informe de Prueba, Efectividad sobre Procesamiento de Pedidos.  Fecha $date (Se le ha enviado un email con mas detalles)"; 
    
    $onlydate = date("d-m-Y"); 
  
    require_once("../plus/include/Y_DB.class.php");
    
    
    $db = new Y_DB(); 
    $db->Database = 'marijoa';
    
    
    
    
    
    
    
    /*##############################      00      ############################*/
    
    
    /*############################ SALDO ANTERIOR ############################*/
    
    function saldoAnterior($suc){
        $db = new Y_DB(); 
        $db->Database = 'marijoa';
        $db->Query('SELECT x1_ as  SALDO_ANTERIOR FROM _audit_log_ WHERE x0_ = "'.$suc.'" AND   fecha  <  CURRENT_DATE and accion = "SALDO_ANT_PED" order by id desc limit 1'); 
        if($db->NumRows()>0){ 
          $db->NextRecord(); 
          $saldo_anterior = $db->Record['SALDO_ANTERIOR'];
          return $saldo_anterior;
        }else{
            return 0;
        }
    
    }
     
    
    /*############################ PEDIDOS TOTALES ENTRADOS EN LA FECHA ############################*/
    
    function pedidosEntrantes($suc){
        $db = new Y_DB(); 
        $db->Database = 'marijoa';
        $db->Query('SELECT count(*) AS PEDIDOS_ENTRANTES FROM nota_pedido p, nota_pedido_det d WHERE p.nro = d.nro_pedido AND  
                    p.__locald LIKE "'.$suc.'"  
                    AND   p.estado !="Abierta"   AND p.fecha = CURRENT_DATE');
        $db->NextRecord();
        $pedidos_entrantes = $db->Record['PEDIDOS_ENTRANTES'];
        return $pedidos_entrantes;
    }
         
    /*############################ PEDIDOS ENVIADOS ############################*/
    function pedidosProcesados($suc){
        $db = new Y_DB(); 
        $db->Database = 'marijoa';
        $db->Query('SELECT count(*) AS PEDIDOS_ENVIADOS FROM nota_pedido p, nota_pedido_det d WHERE p.nro = d.nro_pedido AND  
                    p.__locald LIKE "'.$suc.'"   
                    AND   p.estado !="Abierta"   AND p.fecha = CURRENT_DATE AND d.estado = "Enviado"');
        $db->NextRecord();
        $pedidos_enviados = $db->Record['PEDIDOS_ENVIADOS'];
        return $pedidos_enviados;
    }  
    
    function registrarSaldoDeLaFecha($suc,$saldo){
        $db = new Y_DB(); 
        $db->Database = 'marijoa';
        $db->Query("INSERT INTO _audit_log_ (usuario,fecha,hora,accion,descrip,x0_,x1_)VALUES('Sistema',CURRENT_DATE,CURRENT_TIME,'SALDO_ANT_PED','Procesamiento de Pedidos','$suc','$saldo');");
    }
    
    
    // 00
    $SA_00 =  saldoAnterior("00");
    $PE_00 = pedidosEntrantes("00");
    $PENV_00 = pedidosProcesados("00"); 
    $diff00 = ($SA_00 + $PE_00) - $PENV_00; 
    // Registro la diferencia como saldo
    registrarSaldoDeLaFecha('00',$diff00);
     
    
    $total00 = $SA_00 + $PE_00; 
    $efec00 = number_format( ($PENV_00 * 100 ) / $total00 , 1, ',', '.');   
    
    
    // 08
    $SA_08 =  saldoAnterior("08");
    $PE_08 = pedidosEntrantes("08");
    $PENV_08 = pedidosProcesados("08"); 
    $diff08 = ($SA_08 + $PE_08) - $PENV_08;  
    // Registro la diferencia como saldo
    registrarSaldoDeLaFecha('08',$diff08);
    
    $total08 = $SA_08 + $PE_08; 
    $efec08 = number_format( ($PENV_08 * 100 ) / $total08 , 1, ',', '.');   
    
    // 09
    $SA_09 =  saldoAnterior("09");
    $PE_09 =  pedidosEntrantes("09");
    $PENV_09 = pedidosProcesados("09");
    $diff09 = ($SA_09 + $PE_09) - $PENV_09;  
    // Registro la diferencia como saldo
    registrarSaldoDeLaFecha('09',$diff09);
    
    $total09 = $SA_09 + $PE_09; 
    $efec09 = number_format( ($PENV_09 * 100 ) / $total09 , 1, ',', '.');   
    
    $TOTALES = $total00 + $total08 + $total09;
    $TOTAL_PROCESADOS = $PENV_00 + $PENV_08 + $PENV_09;
    $EFECTIVIDAD_TOTAL =  number_format( ($TOTAL_PROCESADOS * 100 ) / $TOTALES , 1, ',', '.');   
    
    $TOTAL_SALDO_ANT = $SA_00 + $SA_08 + $SA_09;
    
    $TOTAL_ENTRANTES = $PE_00 + $PE_08 + $PE_09;
    
    $TOTAL_SALDO = $diff00 + $diff08 + $diff09;
    
    $message = "  <h1> <div> Reporte sobre Procesamiento de Pedidos   </div>  </h1>";
      
     
            
            $message .= "";
            $message .= "<table border='1' cellpadding='2' cellspacing='2'>";
            $message .= "<tr> <th> $onlydate  </th> </tr>";
            $message .= "<tr> <th> DEPOSITOS </th> <th colspan='2' style='background:#FDF5E6'>Dep. 00 (Canal 7)</th><th colspan='2' style='background:#9ACD32'>Dep. 08 (San Isidro)</th><th colspan='2' style='background:#FFDEAD'>Dep. 09 (Cap. Miranda) </th> <th>Totales</th>  </tr>";
            $message .= 
            
            "<tr>
                              <td> Saldo Anterior </td>    <td style='text-align:center;'> $SA_00    </td>  <td> </td> 
                                                           <td style='text-align:center;'> $SA_08    </td>  <td> </td> 
                                                           <td style='text-align:center;'> $SA_09    </td>  <td> </td> <td style='text-align:center;'>$TOTAL_SALDO_ANT </td>                     
            </tr>
            <tr>
                              <td> Pedididos del Dia </td> <td style='text-align:center;'> $PE_00 </td> <td> </td> 
                                                           <td style='text-align:center;'> $PE_08</td>  <td> </td>
                                                           <td style='text-align:center;'> $PE_09</td>  <td> </td>  <td style='text-align:center;'>$TOTAL_ENTRANTES</td>              
            </tr>
            <tr>
                              <td> <b>Total </b></td>  <td style='text-align:center;'> <b> $total00  </b></td> <td></td>
                                                       <td style='text-align:center;'> <b> $total08  </b></td> <td></td>
                                                       <td style='text-align:center;'> <b> $total09  </b></td> <td></td> <td style='text-align:center;'> <b>$TOTALES</b> </td>                  
            </tr>
            
            <tr>
                              <td> Procesados </td>  <td style='text-align:center;'> $PENV_00  </td>   <td>$efec00%</td>  
                                                     <td style='text-align:center;'> $PENV_08  </td>   <td>$efec08%</td>  
                                                     <td style='text-align:center;'> $PENV_09  </td>   <td>$efec09%</td>  <td style='text-align:center;'> $TOTAL_PROCESADOS / $TOTALES &nbsp;&nbsp; ( $EFECTIVIDAD_TOTAL )%</td>                  
            </tr>
            
            <tr>
                              <td> <b> Saldo p/ el dia Siguiente </b> </td>  <td style='text-align:center;'><b> $diff00 </b> </td><td></td>
                                                                             <td style='text-align:center;'><b> $diff08 </b> </td><td></td>
                                                                             <td style='text-align:center;'><b> $diff09 </b> </td><td></td><td style='text-align:center;'> <b>$TOTAL_SALDO </b></td>  
            </tr>
            
            ";
                               
            
            $message .= "</table>";
                        
            $attatch =  Array();
            enviarMail('douglas@marijoa.com',$subject,$message,$attatch,true);                // Doglas 
            enviarMail('logisticamarijoa@gmail.com',$subject,$message,$attatch,true);         // Victor Agüero 
            enviarMail('ricardoyunis@marijoa.com',$subject,$message,$attatch,true);      // Ricardo
            enviarMail('saidyunis@marijoa.com',$subject,$message,$attatch,true);     // José Said 
            enviarMail('alfredo@marijoa.com',$subject,$message,$attatch,true);       // Alfredo
                 
         
        
        // Mensaje de Texto
        $date = date("d-m-Y"); 
        $smssub = "Prueba sobre Procesamiento de Pedidos (Se le ha enviado un email con mas detalles)";
        $sms =  "(00) Saldo Anterior:$SA_00  Pedididos del Dia : $PE_00  Procesados:$PENV_00  Saldo: $diff00  Efectividad: $efec00"; 
        $sms .= "(08) Saldo Anterior:$SA_08  Pedididos del Dia : $PE_08  Procesados:$PENV_08  Saldo: $diff08  Efectividad: $efec08";
        $sms .= "(09) Saldo Anterior:$SA_09  Pedididos del Dia : $PE_09  Procesados:$PENV_09  Saldo: $diff09  Efectividad: $efec09";
        $sms .= "Total Saldo Ant.: $TOTAL_SALDO_ANT  Total Entrantes: $TOTAL_ENTRANTES  Procesados: $TOTAL_PROCESADOS  Efectividad: $EFECTIVIDAD_TOTAL"; 
         
        $a =  Array();
        // Mensaje al Telefono
        enviarMail('0983593615@mms.tigo.com.py',$smssub,$sms,$a,true); // Douglas
        enviarMail('0985157287@mms.tigo.com.py',$smssub,$sms,$a,true); //Victor Agüero
        enviarMail('0981568859@mms.tigo.com.py',$smssub,$sms,$a,false); // Ricardo
        enviarMail('0981183241@mms.tigo.com.py',$smssub,$sms,$a,false); // José Said
        enviarMail('0981226738@mms.tigo.com.py',$smssub,$sms,$a,false); // Alfredo
        
          
        
     
       //enviarMail('0981568859@mms.tigo.com.py',$subject,$message);
    
     } 
    
    ?>