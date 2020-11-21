<?php

function enviarMail($email,$subject, $message,$attach ,$isHTML ) {
  
    
        require_once("phpmailer-gmail/class.phpmailer.php");
        require_once("phpmailer-gmail/class.smtp.php");
        

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "sv42.byethost42.org";
        $mail->Port = 290;
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
    
    $date = date("d-m-Y H:i");   
    $subject = "Informe Mensual Programado $date";
     
    
    require_once("../plus/include/Y_DB.class.php");
    
    
    $db = new Y_DB();
    
    $db->Database = 'marijoa';
    
    $message = "Reportes de Fin de mes";
    /**
     * Actualizar Codigo de Articulo
     */
	 /*
    $db->Query("SELECT  f_fecha <= CURRENT_DATE AS ACTUALIZAR ,DATE_ADD(f_fecha ,   INTERVAL 1 YEAR) FROM tmp");
    $db->NextRecord();
    $ACTUALIZAR = $db->Record['ACTUALIZAR'];
    if ($ACTUALIZAR > 0) {

        $db->Query("UPDATE tmp SET f_fecha = DATE_ADD(f_fecha ,   INTERVAL 1 YEAR);");
        $db->Query("UPDATE __autonum__ SET value = 1 WHERE name = 'p_cod' ");
        $message.="#################### CODIGO DE PRODUCTO ACTUALIZADO A 1  ####################";
    }else{
        $message.="#################### CODIGO DE PRODUCTO NO HA SIDO ACTUALIZADO INFORMAR A INFORMATICA URGENTE!!!  ####################";
    } */
    
    // Stock Actual
    
    $db->Query("SELECT p_local AS SUC, sum(p_cant) AS STOCK,round(sum(p_cant * p_compra),2) as CIF, round(  sum(p_compra +  (p_cant * p_compra + p_porc_recargo / 100 )),2) AS COSTO
    FROM mnt_prod WHERE prod_fin_pieza <> 'Si' AND p_cant > 0  GROUP BY SUC ASC");
    
    
   
   
   
    if($db->NumRows() > 0){
       $message .= "<table border='1' cellpadding='2' cellspacing='2' width='50%' style='text-align:center;padding:2px'>";
	   $message .= "<tr> <th colspan='4'> Stock Actual  </th>    </tr>";
       $message .= "<tr> <th> SUC </th> <th>STOCK</th><th>CIF</th><th>COSTO</th>    </tr>";
          
        while($db->NextRecord()){
             
            $SUC = $db->Record['SUC'];  
            $STOCK = number_format($db->Record['STOCK'] , 2, ',', '.'); 
            $CIF = number_format($db->Record['CIF'] , 2, ',', '.'); 
            $COSTO = number_format($db->Record['COSTO'] , 2, ',', '.');   
            $message .= "<tr><td>$SUC</td> <td>$STOCK</td> <td>$CIF</td> <td>$COSTO</td> </tr>"; 
        } 
        $message .= "</table>";
    }   
	
	
	    // Stock Actual sin Mercaderias en Transito
    
    $db->Query("SELECT p_local AS SUC, sum(p_cant) AS STOCK,round(sum(p_cant * p_compra),2) as CIF, round(  sum(p_compra +  (p_cant * p_compra + p_porc_recargo / 100 )),2) AS COSTO
    FROM mnt_prod WHERE prod_fin_pieza <> 'Si' AND p_cant > 0 AND  prod_fin_pieza <> 'Tr' GROUP BY SUC ASC");
     
   
   
    if($db->NumRows() > 0){
       $message .= "<table border='1' cellpadding='2' cellspacing='2' width='50%' style='text-align:center;padding:2px'>";
	   $message .= "<tr> <th colspan='4'> Stock Actual excluyendo Mercaderias en Transito  </th>    </tr>";
       $message .= "<tr> <th> SUC </th> <th>STOCK</th><th>CIF</th><th>COSTO</th>    </tr>";
          
        while($db->NextRecord()){
             
            $SUC = $db->Record['SUC'];  
            $STOCK = number_format($db->Record['STOCK'] , 2, ',', '.'); 
            $CIF = number_format($db->Record['CIF'] , 2, ',', '.'); 
            $COSTO = number_format($db->Record['COSTO'] , 2, ',', '.');   
            $message .= "<tr><td>$SUC</td> <td>$STOCK</td> <td>$CIF</td> <td>$COSTO</td> </tr>"; 
        } 
        $message .= "</table>";
    }   
    
    
    
    // Obtener Rango Mes anterior
    $db->Query("SELECT   LEFT( DATE(now() - INTERVAL 5 DAY),8) as mes ");
    $db->NextRecord();
    $mes = $db->Record['mes'];
     
    // Agrupacion de Ajustes
    $db->Query("SELECT p_local AS SUC,aj_signo AS SIGNO, sum( aj_ajuste) AS AJUSTE, aj_oper OPER, round(sum(p_compra * aj_ajuste) ,2)  AS COSTO_SIN_RECARGO,
    round(  sum( (p_compra + (p_compra * p_porc_recargo / 100)) * aj_ajuste),2) AS COSTO_CON_RECARGO  
    FROM mnt_ajustes a, mnt_prod p WHERE a.aj_prod = p.p_cod AND  aj_fecha  LIKE '$mes%'  AND aj_prod <> '0' 
    GROUP BY SIGNO, SUC,aj_oper ORDER BY SIGNO, SUC ASC ,aj_oper ASC ");
    
    $message .="<br><br>Ajustes<br><br>";
    
    if($db->NumRows() > 0){
       $message .= "<table border='1' cellpadding='2' cellspacing='2' width='50%' style='text-align:center;padding:2px'>";
       $message .= "<tr> <th> SUC </th> <th>SIGNO </th><th>AJUSTES</th> <th>OPERACION</th> <th>COSTO_SIN_RECARGO</th>  <th>COSTO_CON_RECARGO</th>   </tr>";
          
        while($db->NextRecord()){
             
            $SUC = $db->Record['SUC'];  
            $SIGNO = $db->Record['SIGNO'];  
            
            $AJUSTES= number_format($db->Record['AJUSTE'] , 2, ',', '.'); 
            $OPERACION= $db->Record['OPER'];  
            $COSTO_SIN_RECARGO = number_format($db->Record['COSTO_SIN_RECARGO'] , 2, ',', '.');   
            $COSTO_CON_RECARGO = number_format($db->Record['COSTO_CON_RECARGO'] , 2, ',', '.');   
             
            $message .= "<tr><td>$SUC</td> <td>$SIGNO</td> <td>$AJUSTES</td> <td style='text-align:left;margin-left:2px'>$OPERACION</td> <td style='text-align:right;margin-right:2px'>$COSTO_SIN_RECARGO</td>  <td style='text-align:right;margin-right:2px'>$COSTO_CON_RECARGO</td> </tr>"; 
        } 
        $message .= "</table>";
    }   
    
    $message .="<br><br> Productos cargados en Facturas a&uacute;n Abiertas<br><br>";
    
    //  Facturas Abiertas
    
    
        $db->Query("SELECT c_ref AS INTERNO,c_factura AS FACTURA,c_fechafac AS FECHA_FACTURA, c.c_fecha AS FECHA_INS,c_valor_total AS VALOR_TOTAL,c_sobre_costo AS PORC_RECARGO,c_fi,c_fn,c_di,c_otros,c_moneda, 
            c.c_cambio,
        round(c_valor_total * c_cambio +(c_valor_total * c_cambio * (c_sobre_costo / 100)),2) AS GUARANIZADO,

        round(sum(p_compra * p_cant_compra),2) AS 'COSTO_SIN_RECARGO', 

        round(sum((p_compra + (p_compra * p_porc_recargo / 100)) * p_cant_compra),2) AS 'COSTO_CON_RECARGO'

        FROM mov_compras c, mnt_prod p, _audit_log_ a WHERE c.c_ref = p.p_ref AND c.c_estado = 'Abierta' AND p.p_padre = '' AND a.accion = 'INSERTAR' 

        AND a.descrip = p.p_cod AND a.fecha <= CURRENT_DATE GROUP BY c_ref");
 
    if($db->NumRows() > 0){
       $message .= "<table border='1' cellpadding='0' cellspacing='0' width='100%' style='text-align:center;padding:2px'>";
       $message .= "<tr> <th> INTERNO </th> <th>FACTURA </th><th>FECHA_FAC</th> <th>FECHA INS</th> <th>VALOR TOTAL</th>  <th>% RECARGO</th>  <th>FI</th> <th>FN </th> <th>DI</th> <th>OTROS</th><th>COTIZ</th><th>MONEDA</th><th>EN G$</th><th>COSTO S/REC</th> <th> COSTO C/REC </th>        </tr>";
          
        while($db->NextRecord()){
             
            $INTERNO= $db->Record['INTERNO'];  
            $FACTURA = $db->Record['FACTURA'];  
            $FECHA_FACTURA = $db->Record['FECHA_FACTURA'];  
            $FECHA_INS= $db->Record['FECHA_INS'];  
            $VALOR_TOTAL= number_format($db->Record['VALOR_TOTAL'] , 2, ',', '.'); 
            $PORC_RECARGO = number_format($db->Record['PORC_RECARGO'] , 2, ',', '.');   
            
            $c_fi = number_format($db->Record['c_fi'] , 2, ',', '.');   
            $c_fn = number_format($db->Record['c_fn'] , 2, ',', '.');   
            $c_di = number_format($db->Record['c_di'] , 2, ',', '.');   
            $c_otros = number_format($db->Record['c_otros'] , 2, ',', '.');    
            $c_cambio = number_format($db->Record['c_cambio'] , 2, ',', '.');    
            $c_moneda = $db->Record['c_moneda'];  
            $GUARANIZADO = number_format($db->Record['GUARANIZADO'] , 2, ',', '.');    
            $COSTO_SIN_RECARGO = number_format($db->Record['COSTO_SIN_RECARGO'] , 2, ',', '.');    
            $COSTO_CON_RECARGO = number_format($db->Record['COSTO_CON_RECARGO'] , 2, ',', '.');    
             
             
            $message .= "<tr><td>$INTERNO</td> <td>$FACTURA</td> <td>$FECHA_FACTURA</td> <td>$FECHA_INS </td> 
            <td style='text-align:left;margin-right:2px'>$VALOR_TOTAL</td> 
            <td style='text-align:right;margin-right:2px'>$PORC_RECARGO</td> 
            <td style='text-align:right;margin-right:2px'>$c_fi</td> 
            <td style='text-align:right;margin-right:2px'>$c_fn</td> 
            <td style='text-align:right;margin-right:2px'>$c_di</td> 
            <td style='text-align:right;margin-right:2px'>$c_otros</td> 
            <td style='text-align:right;margin-right:2px'>$c_cambio</td> 
            <td  >$c_moneda</td> 
            <td style='text-align:right;margin-right:2px'>$GUARANIZADO</td> 
            <td style='text-align:right;margin-right:2px'>$COSTO_SIN_RECARGO</td>  
            <td style='text-align:right;margin-right:2px'>$COSTO_CON_RECARGO</td> 
            
            </tr>"; 
        } 
        $message .= "</table>";
    }
	//Diferencia de Cambio
	if($db->Query("update mov_compras m set m.c_cant_mts_cmp = (select sum(p.p_cant_compra) from mnt_prod p where p.p_ref=m.c_ref group by p.p_ref)")){
		
		$db->Query("update mov_compras set c_cambio_merc = 1 where c_cambio_merc = 0  and c_moneda='G$'");		
		//$db->Query("SELECT c_ref,c_factura,c.c_estado,c.c_fecha AS FechaReg, c.c_iva, c.c_moneda,c.c_valor_total,c.c_cambio,c.c_cambio_merc,c.c_sobre_costo, ROUND(c_valor_total + (c_valor_total * c.c_sobre_costo / 100) * c_cambio,2) AS ValorGS, ROUND(c_valor_total + (c_valor_total * c.c_sobre_costo / 100) * c_cambio_merc ,2)AS ValorGS_CotizMerc, c.c_cant_mts_cmp AS CantCompra, sum(p_compra) AS PrecioCompra,  sum(ROUND(p.p_compra + (p.p_compra * p.p_porc_recargo / 100),2))  AS CostoXMetro,  sum(ROUND(((p.p_compra + (p.p_compra * p.p_porc_recargo / 100)) / c.c_cambio) * c.c_cambio_merc,2)) AS CostoXMetroCM,  SUM( df_cantidad) AS MetrosVendidos ,  SUM( ROUND( (p.p_compra + (p.p_compra * p.p_porc_recargo / 100) )* df_cantidad ,2))   AS CostoXMetroMTSV,   SUM(ROUND(((((p.p_compra + (p.p_compra * p.p_porc_recargo / 100)) / c.c_cambio) * c.c_cambio_merc)) * df_cantidad,2) )    AS CostoXMetroCM_MTV ,     SUM(ROUND( (p.p_compra + (p.p_compra * p.p_porc_recargo / 100) )* df_cantidad ,2) -  ROUND(((((p.p_compra + (p.p_compra * p.p_porc_recargo / 100)) / c.c_cambio) * c.c_cambio_merc)) * df_cantidad,2)) AS Diff,  date_format(date_add(last_day(date_add(date(now()),interval -2 month)), interval 1 day),'%d-%m-%Y') as inicio, date_format(last_day(date_add(date(now()),interval -1 month)),'%d-%m-%Y') as fin FROM factura f, det_factura d, mnt_prod p, mov_compras c  WHERE f.fact_nro = d.df_fact_num  AND d.df_cod_prod = p.p_cod AND p.p_ref = c.c_ref    AND f.fact_fecha BETWEEN date_add(last_day(date_add(date(now()),interval -2 month)), interval 1 day) AND last_day(date_add(date(now()),interval -1 month)) AND c_ref > 0 AND c.c_moneda <> 'G$' AND f.fact_estado = 'Cerrada' GROUP BY c_ref LIMIT 1000000");
		$db->Query("SELECT c_ref,c_factura,c.c_estado,c.c_fecha AS FechaReg, c.c_iva, c.c_moneda,c.c_valor_total,c.c_cambio,c.c_cambio_merc,c.c_sobre_costo, ROUND(c_valor_total + (c_valor_total * c.c_sobre_costo / 100) * c_cambio,2) AS ValorGS, ROUND(c_valor_total + (c_valor_total * c.c_sobre_costo / 100) * c_cambio_merc ,2)AS ValorGS_CotizMerc,c.c_cant_mts_cmp AS CantCompra,sum(p_compra) AS PrecioCompra,sum(ROUND(p.p_compra + (p.p_compra * p.p_porc_recargo / 100),2))  AS CostoXMetro,sum(ROUND(((p.p_compra + (p.p_compra * p.p_porc_recargo / 100)) / c.c_cambio) * c.c_cambio_merc,2)) AS CostoXMetroCM,SUM( df_cantidad) AS MetrosVendidos ,SUM( ROUND( (p.p_compra + (p.p_compra * p.p_porc_recargo / 100) )* df_cantidad ,2))   AS CostoXMetroMTSV,SUM(ROUND(((((p.p_compra + (p.p_compra * p.p_porc_recargo / 100)) / c.c_cambio) * c.c_cambio_merc)) * df_cantidad,2) )    AS CostoXMetroCM_MTV , SUM(ROUND( (p.p_compra + (p.p_compra * p.p_porc_recargo / 100) )* df_cantidad ,2) -  ROUND(((((p.p_compra + (p.p_compra * p.p_porc_recargo / 100)) / c.c_cambio) * c.c_cambio_merc)) * df_cantidad,2)) AS Diff,round((SUM( df_cantidad)/c.c_cant_mts_cmp),2) as `CantVendida` ,round(c.c_iva*c.c_cambio*(SUM( df_cantidad)/c.c_cant_mts_cmp),2) as DeduccionIVA FROM factura f, det_factura d, mnt_prod p, mov_compras c WHERE f.fact_nro = d.df_fact_num  AND d.df_cod_prod = p.p_cod AND p.p_ref = c.c_ref    AND f.fact_fecha BETWEEN date_add(last_day(date_add(date(now()),interval -2 month)), interval 1 day) AND last_day(date_add(date(now()),interval -1 month))  AND c_ref > 0  AND f.fact_estado = 'Cerrada'GROUP BY c_ref LIMIT 1000000");
		if($db->NextRecord()){
			$message .= "<br/><br/><br/>";
			$message .= "<table border='1' cellpadding='0' cellspacing='0' width='50%' style='empty-cells:show;text-align:center;padding:0;marging:0;'>";
			$message .= "<thead><tr><th colspan='20'><b>Reporte de Diferencia de Cambio, Per&iacute;odo: ".$db->Record['inicio']." al ".$db->Record['fin']."</b></th></tr>";
			$message .= "<tr><th>Ref</th><th>Factura</th><th>Estado</th><th>FechaReg</th><th>IVA</th><th>Moneda</th><th>Valor Total</th><th>Cambio</th><th>Cambio Merc.</th><th>Sobre Costo</th><th>ValorGS</th><th>ValorGS_CotizMerc</th><th>CantCompra</th><th>PrecioCompra</th><th>CostoXMetro</th><th>CostoXMetroCM</th><th>MetrosVendidos</th><th>CostoXMetroMTSV</th><th>CostoXMetroCM_MTV</th><th>Diff</th><th>%CantVendida</th><th>DeduccionIVA</th></tr></thead><tbody>";
			do{
				$ref = $db->Record['c_ref'];
				$fact = $db->Record['c_factura'];
				$estado = $db->Record['c_estado'];
				$fechaReg = $db->Record['FechaReg'];
				$IVA = $db->Record['c_iva'];
				$moneda = $db->Record['c_moneda'];
				$valorTotal = $db->Record['c_valor_total'];
				$cambio = $db->Record['c_cambio'];
				$cambioMerc = $db->Record['c_cambio_merc'];
				$sobreCosto = $db->Record['c_sobre_costo'];
				$valGS = $db->Record['ValorGS'];
				$valGSMerc = $db->Record['ValorGS_CotizMerc'];
				$cantCompra = $db->Record['CantCompra'];
				$precioCompra = $db->Record['PrecioCompra'];
				$costoXMetro = $db->Record['CostoXMetro'];
				$costoXMetroCM = $db->Record['CostoXMetroCM'];
				$metrosVendidos = $db->Record['MetrosVendidos'];
				$costoXMetroMTSV = $db->Record['CostoXMetroMTSV'];
				$costoXMetroCM_MTSV = $db->Record['CostoXMetroCM_MTV'];
				$CantVendida = $db->Record['CantVendida'];
				$DeduccionIVA = $db->Record['DeduccionIVA'];
				$diff = $db->Record['Diff'];
				$message .="<tr>
			  <td>$ref</td>
			  <td>$fact</td>
			  <td>$estado</td>
			  <td>$fechaReg</td>
			  <td>$IVA</td>
			  <td>$moneda</td>
			  <td>$valorTotal</td>
			  <td>$cambio</td>
			  <td>$cambioMerc</td>
			  <td>$sobreCosto</td>
			  <td>$valGS</td>
			  <td>$valGSMerc</td>
			  <td>$cantCompra</td>
			  <td>$precioCompra</td>
			  <td>$costoXMetro</td>
			  <td>$costoXMetroCM</td>
			  <td>$metrosVendidos</td>
			  <td>$costoXMetroMTSV</td>
			  <td>$costoXMetroCM_MTSV</td>
			  <td>$diff</td>
			  <td>$CantVendida</td>
			  <td>$DeduccionIVA</td>
			</tr>";
			}while($db->NextRecord());
			$message .= "</tbody><tfood></tfood></table>";
		}
    }
    
    //echo $message;
    
    $attatch = array();
	//enviarMail('jack@corporaciontextil.com.py',$subject,$message,$attatch,true);
    enviarMail('dembogurski@gmail.com',$subject,$message,$attatch,true);           // Doglas 
	enviarMail('arnaldoa@corporaciontextil.com.py',$subject,$message,$attatch,true);           //  
       
 
        
        // Mensaje al Telefono
		$smssub = "Informe de Fin de Mes";
		$sms = "El informe de fin de mes ha sido enviado a su correo...";
        //enviarMail('0983593615@mms.tigo.com.py',$smssub,$sms,$attatch,false); // Douglas
		//enviarMail('0985374073@mms.tigo.com.py',$smssub,$sms,$attatch,false); // Arnaldo Aquino
        
         
    
    //enviarMail('0981568859@mms.tigo.com.py',$subject,$message);
    
    
    
    ?>