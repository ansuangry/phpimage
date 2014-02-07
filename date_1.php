<?php
putenv('GDFONTPATH=' . realpath('.'));
//echo date("Y/m/d") . "<br>";
//echo date("Y.m.d") . "<br>";
//echo date("Y-m-d") . "<br>";

$strDate  = (($strDate = $_GET['date'])   ? strtolower($strDate)  : NULL);
$strSize  = (($strSize = $_GET['size'])   ? strtolower($strSize)  : NULL);
//echo $strDate ;
if($strSize == NULL){
	$strSize = '500x500';
}

if($strDate != NULL){
	$dateInfo = date_parse_from_format('yy-m-d', $strDate);
$unixTimestamp = mktime( 
  	$dateInfo['hour'], $dateInfo['minute'], $dateInfo['second'],
  	$dateInfo['month'], $dateInfo['day'], $dateInfo['year'],
  	$dateInfo['is_dst']
);

  //print_r ($dateInfo );
  //  echo 'year : '.$dateInfo['year']."<br>" ;
  //  echo 'month : '. $dateInfo['month']."<br>";
  //  echo 'day : '. $dateInfo['day']."<br>";
  //  echo 'hour : '. $dateInfo['hour']."<br>";
  //  echo 'minute : '. $dateInfo['minute']."<br>"; 
  //  echo 'second : '.$dateInfo['second']."<br>";
  $day = date('D', $unixTimestamp);
  //$bgcolor ;
    CreateImage($strSize, 'png', getBackgroundColor($day), 'ffffff', $dateInfo['day']);
  // CreateImage('500x500', 'jpg', '32c2dc', 'ffffff', $day);
}else{
  CreateImage($strSize, 'jpg', '32c2dc', 'ffffff', '31');
}

function CreateImage($strSize, $strType, $strBg, $strColor,$strUrlText)
{
// Handle the parameters.
  //?size=500x500&type=jpg&bg=000000&color=bb0000&text=beep%20beep..
  //$strSize  = (($strSize = $_GET['size'])   ? strtolower($strSize)  : NULL);
  //$strType  = (($strType = $_GET['type'])   ? strtolower($strType)  : 'png');
  //$strBg    = (($strBg = $_GET['bg'])       ? strtolower($strBg)    : '000000');
  //$strColor = (($strColor = $_GET['color']) ? strtolower($strColor) : 'ffffff');
  //$strUrlText = (($strUrlText = $_GET['text']) ? strtolower($strUrlText) : 'Not..');

// Now let's check the parameters.
if ($strSize == NULL) {
	die('<b>You have to provide the size of the image.</b> Example: 250x320.</b>');
}
if ($strType != 'png' and $strType != 'gif' and $strType != 'jpg') {
	die('<b>The selected type is wrong. You can chose between PNG, GIF or JPG.');
}
if (strlen($strBg) != 6 and strlen($strBg) != 3) {
	die('<b>You have to provide the background color as hex.</b> Example: 000000 (for black).');
}
if (strlen($strColor) != 6 and strlen($strColor) != 3) {
	die('<b>You have to provide the font color as hex.</b> Example: ffffff (for white).');
}

// Get width and height from current size.
list($strWidth, $strHeight) = split('x', $strSize);
// If no height is given, we'll return a squared picture.
if ($strHeight == NULL) $strHeight = $strWidth;

// Check if size and height are digits, otherwise stop the script.
if (ctype_digit($strWidth) and ctype_digit($strHeight)) {
	// Check if the image dimensions are over 9999 pixel.
	if (($strWidth > 9999) or ($strHeight > 9999)) {
		die('<b>The maximum picture size can be 9999x9999px.</b>');
	}

	// Let's define the font (size. And NEVER go above 9).
  $intFontSize = $strWidth /2;
  //if ($intFontSize < 9) $intFontSize = 9;
  	
  // $intFontSize = 200 ; //---
  
 	$strFont = 'DroidSansMono.ttf';
  //$strText = $strWidth . 'x' . $strHeight;
  	$strText = $strUrlText ;
  //echo '<h1>'.$strFont.'</h1>';
  //echo '<h1>'.$strText.'</h1>';
	// Create the picture.
	$objImg = @imagecreatetruecolor($strWidth, $strHeight) or die('Sorry, there is a problem with the GD lib.');
  
	// Color stuff.
	function html2rgb($strColor) {
		if (strlen($strColor) == 6) {
			list($strRed, $strGreen, $strBlue) = array($strColor[0].$strColor[1], $strColor[2].$strColor[3], $strColor[4].$strColor[5]);
		} elseif (strlen($strColor) == 3) {
			list($strRed, $strGreen, $strBlue) = array($strColor[0].$strColor[0], $strColor[1].$strColor[1], $strColor[2].$strColor[2]);
		}

		$strRed   = hexdec($strRed);
		$strGreen = hexdec($strGreen);
		$strBlue  = hexdec($strBlue);

		return array($strRed, $strGreen, $strBlue);
	}

	$strBgRgb    = html2rgb($strBg);
	$strColorRgb = html2rgb($strColor);
	$strBg       = imagecolorallocate($objImg, $strBgRgb[0], $strBgRgb[1], $strBgRgb[2]);
	$strColor    = imagecolorallocate($objImg, $strColorRgb[0], $strColorRgb[1], $strColorRgb[2]);

	// Create the actual image.
	imagefilledrectangle($objImg, 0, 0, $strWidth, $strHeight, $strBg);
  	//Insert frame cal.png
  
  	$frame = imagecreatefrompng('cal.png');

  //imagecopymerge($objImg, $frame, 0, 0, 0, 0, 267, 268, 100);
  
	// Insert the text.
	$arrTextBox    = imagettfbbox($intFontSize, 0, $strFont, $strText);
	$strTextWidth  = $arrTextBox[4] - $arrTextBox[1];
	$strTextHeight = abs($arrTextBox[7]) + abs($arrTextBox[1]);
	$strTextX      = ($strWidth - $strTextWidth) / 2;
	$strTextY      = ($strHeight - $strTextHeight) / 2 + $strTextHeight;
	imagettftext($objImg, $intFontSize, 0, $strTextX, $strTextY, $strColor, $strFont, $strText);

	// Give out the requested type.
	switch ($strType) {
		case 'png':
      		header('Content-Type: image/png');
			imagepng($objImg);
			break;
		case 'gif':
      		header('Content-Type: image/gif');
			imagegif($objImg);
			break;
		case 'jpg':
      		header('Content-Type: image/jpeg');
			imagejpeg($objImg);
			break;
	}

	// Free some memory.
	imagedestroy($objImg);
} else {
	die('<b>You have to provide the size of the image.</b> Example: 250x320.</b>');
}
}


function getBackgroundColor($day){
  $colors = array('Mon'=>'20C8EA', 'Tue'=>'9AC83E', 'Wed'=>'FF8F29', 'Thu'=>'FA3D20', 'Fri'=>'2C363F', 'Sat'=>'F9BE27', 'Sun'=>'0391B4');
  return $colors[$day];
//Mon Tue Wed Thu Fri Sat Sun   
}
?>