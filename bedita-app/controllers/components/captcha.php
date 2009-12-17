<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * Captcha creation and management
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class CaptchaComponent extends Object {
	
	var $controller;
	private $fontColor = array("red" => 193, "green" => 0, "blue" => 118);
	private $background;
	private $fontType;
	
	function startup(&$controller) {
		$this->controller = &$controller;
		// set default
		$this->background = BEDITA_CORE_PATH . DS . "webroot".DS."captcha".DS."img".DS."button.png";
		$this->fontType = BEDITA_CORE_PATH . DS . "webroot".DS."captcha".DS."fonts".DS."Vera.ttf";
	}
	
	public function image($options=array()) {
		$length = (!empty($options["length"]))? $options["length"] : 4;
		if (!empty($options["color"]))
			$this->fontColor = $options["color"];
		 
		// Create a random string, leaving out 'o' to avoid confusion with '0'
		$str = strtoupper(substr(str_shuffle('123456789abcdefghjkmnpqrstuvwxyz'), 0, $length));
		
		// put captcha id in session
		$this->controller->Session->write("captcha_id", $str);
		
		// send several headers to make sure the image is not cached
		// taken directly from the PHP Manual

		// Expire in the past, always modified
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
		// HTTP/1.1
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false); 
		// HTTP/1.0
		header('Pragma: no-cache');
		//content type
		header('Content-type: image/png'); 
					
		// Create a background image
		if (is_dir(APP . "webroot".DS."captcha".DS."img")) {
			foreach (glob(APP . "webroot".DS."captcha".DS."img".DS."*.png") as $filename) {
			    $bgArr[] = $filename;
			}
		} else {
			foreach (glob(BEDITA_CORE_PATH . "webroot".DS."captcha".DS."img".DS."*.png") as $filename) {
			    $bgArr[] = $filename;
			}
		}
		
		if (!empty($bgArr))
			$this->background = $bgArr[array_rand($bgArr)];
		
		$image = imagecreatefrompng($this->background);
		
		// Set the font colour
		$colour = imagecolorallocate($image, $this->fontColor["red"], $this->fontColor["green"], $this->fontColor["blue"]);
		
		// Set the font
		if (is_dir(APP . "webroot".DS."captcha".DS."fonts")) {
			foreach (glob(APP . "webroot".DS."captcha".DS."fonts".DS."*.ttf") as $filename) {
			    $fontArr[] = $filename;
			}
		} else {
			foreach (glob(BEDITA_CORE_PATH . "webroot".DS."captcha".DS."fonts".DS."*.ttf") as $filename) {
			    $fontArr[] = $filename;
			}
		}
		
		if (!empty($fontArr))
			$this->fontType = $fontArr[array_rand($fontArr)];
		
		// get image size
		$imgsize = getimagesize($this->background);
		$width = $imgsize[0];
		$height = $imgsize[1];
		$charSpace = $width / (strlen($str)+1);
		
		// Create an image using our original image and adding the detail
		for ($i=0; $i< strlen($str); $i++)	{
			imagettftext($image, 14+mt_rand(0,8), -20+mt_rand(0,40), ($i+0.3)*$charSpace, ($height/2)+mt_rand(0,10), $colour, $this->fontType, $str{$i});
		}
		
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