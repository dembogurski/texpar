<?php
// Including all required classes
require('class/BCGFont.php');
require('class/BCGColor.php');
require('class/BCGDrawing.php'); 

// Including the barcode technology
include('class/BCGcode39.barcode.php'); 

class RadPlusBarcode{

  function __construct(){ 
  }
  function parseCode($codigo,$escala=2,$thickness=30){
  		// Loading Font
	//$font = new BCGFont('C:/xampp/htdocs/plus/barcodegen/class/font/Arial.ttf', 18);
	
	// The arguments are R, G, B for color.
	$color_black = new BCGColor(0, 0, 0);
	$color_white = new BCGColor(255, 255, 255); 
	
	$code = new BCGcode39();
	$code->setScale($escala); // Resolution
	$code->setThickness($thickness); // Thickness
	$code->setForegroundColor($color_black); // Color of bars
	$code->setBackgroundColor($color_white); // Color of spaces
	$code->setFont(0); // Font (or 0)
	$code->parse($codigo); // Text
	
	
	/* Here is the list of the arguments
	1 - Filename (empty : display on screen)
	2 - Background color */
	$drawing = new BCGDrawing('', $color_white);
	$drawing->setBarcode($code);
	$drawing->draw();
	
	// Header that says it is an image (remove it if you save the barcode to a file)
	 //header('Content-Type: image/png');
	
	// Draw (or save) the image into PNG format.
	  $filename = "../barcodegen/barcodeimages/".$codigo.".png";
	  $drawing->setFilename($filename);
	  $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
	  
	  return $filename;
  }
  
}
?>

