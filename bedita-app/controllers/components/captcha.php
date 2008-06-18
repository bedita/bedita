<?php

class CaptchaComponent extends Object {
	
	var $controller;
	private $fontColor = array("red" => 193, "green" => 0, "blue" => 118);
	private $background;
	private $fontType;
	
	function startup(&$controller) {
		$this->controller = &$controller;echo defined(BEDITA_CORE_PATH);
		// set default
		$appPath = (defined("BEDITA_CORE_PATH"))? BEDITA_CORE_PATH : APP;
		$this->background = $appPath . "/webroot/captcha/img/button.png";
		$this->fontType = $appPath . "/webroot/captcha/fonts/Vera.ttf";
	}
	
	public function image() {
		
		// Create a random string, leaving out 'o' to avoid confusion with '0'
		$char = strtoupper(substr(str_shuffle('abcdefghjkmnpqrstuvwxyz'), 0, 2));
		
		// Concatenate the random string onto the random numbers
		// '0' is left out to avoid confusion with 'O'
		$str = rand(2, 9) . rand(2, 9) . $char;
		
		// put captcha id in session
		$this->controller->Session->write("captcha_id", $str);
		
		// Set the content type
		header('Content-type: image/png');
		header('Cache-control: no-cache');
					
		// Create a background image
		if (is_dir(APP . "webroot/captcha/img/")) {
			foreach (glob(APP . "webroot/captcha/img/*.png") as $filename) {
			    $bgArr[] = $filename;
			}
		} else {
			foreach (glob(BEDITA_CORE_PATH . "webroot/captcha/img/*.png") as $filename) {
			    $bgArr[] = $filename;
			}
		}
		
		if (!empty($bgArr))
			$this->background = $bgArr[array_rand($bgArr)];
		
		$image = imagecreatefrompng($this->background);
		
		// Set the font colour
		$colour = imagecolorallocate($image, $this->fontColor["red"], $this->fontColor["green"], $this->fontColor["blue"]);
		
		// Set the font
		if (is_dir(APP . "webroot/captcha/fonts")) {
			foreach (glob(APP . "webroot/captcha/fonts/*.ttf") as $filename) {
			    $fontArr[] = $filename;
			}
		} else {
			foreach (glob(BEDITA_CORE_PATH . "webroot/captcha/fonts/*.ttf") as $filename) {
			    $fontArr[] = $filename;
			}
		}
		
		if (!empty($fontArr))
			$this->fontType = $fontArr[array_rand($fontArr)];
		
		// Set a random integer for the rotation between -15 and 15 degrees
		$rotate = rand(-15, 15);
		
		// Create an image using our original image and adding the detail
		imagettftext($image, 14, $rotate, 18, 30, $colour, $this->fontType, $str);
		
		// Output the image as a png
		imagepng($image);
	}
	
	public function checkCaptcha() {
		if (empty($this->controller->params["form"]["captcha"]))
			throw new BeditaException(__("Captcha image and text don't match", true));
		$captcha = $this->controller->params["form"]["captcha"];
		if( $this->controller->Session->valid() && (strtoupper($captcha) == $this->controller->Session->read('captcha_id')) )
			return true;
		else
			throw new BeditaException(__("Text doesn't match image", true));
	}
	
	public function setFontColor($red, $green, $blue) {
		$this->fontColor = array("red" => $red, "green" => $green, "blue" => $blue);
	}
	
}

?>