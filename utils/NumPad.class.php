<?php

/**
 * Description of Numpad
 * @author Ing.Douglas
 * @date 12/02/2016
 */

require_once("../Y_Template.class.php");



class NumPad {
    function __construct(){          
        $path = "http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."/marijoa";   
        chdir($_SERVER['DOCUMENT_ROOT']."/marijoa");       
        $n = new Y_Template("utils/NumPad.html");  
        $n->Set("path",$path);                
        $n->Show("header");
        $n->Show("keypad");
    }
}
?>

  