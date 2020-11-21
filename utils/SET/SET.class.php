<?php

/**
 * Description of SET
 * @author Ing.Douglas
 * @date 31/03/2017
 */
class SET {

    function __construct() {

        $cotiz = array();
        $rates = "{";

        $web = 'wget -q http://www.set.gov.py/portal/PARAGUAY-SET -O -| grep "UICotizacion" | grep "G." | grep "," > SET';
        $output = shell_exec($web);

        echo "Cotizaciones bajadas\n\r";
        sleep(1);

        $handler = fopen("SET", "r");
        if ($handler) {
            $i = 1;
            $k = 0;
            while (($line = fgets($handler)) !== false) {
                $cut = str_replace('<span class="UICotizacion">G.', "", $line);
                $cut = str_replace('</span>', "", $cut);
                $cut = trim(str_replace('</div>', "", $cut));
                if ($i % 2) {
                    if ($i != 7 && $i != 8) {
                        //echo "$cut \n\r";
                        $cotiz[$k] = $cut;
                        $valor = $cut;
                        //$valor = str_replace(".","", $cut);
                        //$valor = str_replace(",",".", $valor);
                        $moneda = "";
                        switch ($k) {
                            case 0:
                                $moneda = "U$";
                                break;
                            case 1:
                               $moneda = "E$";
                                break;
                            case 2:
                                $moneda = "P$";
                                break;
                            case 3:
                                $moneda = "R$";
                                break;
                        }
                        if($moneda != "E$"){    
                           $rates.='"'.$moneda.'":"'.$valor.'",';   
                        }

                        $k++;
                    }
                }
                $i++;
                if ($i == 11) {
                    break;
                }
            }
        }
        fclose($handler);
        
                            

        print_r($cotiz);
        $rates = substr($rates,0, -1);  
        $rates .= "}";
        echo "Enviando: ".$rates;
                          
        $url = "http://192.168.2.222/marijoa/utils/SET/SetParser.class.php?content=$rates";
        /*                    
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($cotiz)
            )
        );
        $context = stream_context_create($options);*/
        $result = file_get_contents($url);
        echo $result;
                            
    }

}

new SET();
?>
