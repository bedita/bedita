<?php
/*
    +--------------------------------------------------------------------------------------------+
    |   DISCLAIMER - LEGAL NOTICE -                                                              |
    +--------------------------------------------------------------------------------------------+
    |                                                                                            |
    |  This program is free for non comercial use, see the license terms available at            |
    |  http://www.francodacosta.com/licencing/ for more information                              |
    |                                                                                            |    
    |  This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; |
    |  without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. |
    |                                                                                            |
    |  USE IT AT YOUR OWN RISK                                                                   |
    |                                                                                            |
    |                                                                                            |
    +--------------------------------------------------------------------------------------------+

*/
/**
 * phMagick - get image info
 *
 * @package    phMagick
 * @version    0.1.0
 * @author     Nuno Costa - sven@francodacosta.com
 * @copyright  Copyright (c) 2007
 * @license    http://www.francodacosta.com/phmagick/license/
 * @link       http://www.francodacosta.com/phmagick
 * @since      2008-03-13
 */
class phMagick_info{
	function getInfo(phmagick $p, $file=''){
		if ($file == '') $file = $p->getSource();
		return getimagesize  ($file);
	}
	
	function getWidth(phmagick $p, $file=''){
		list($width, $height, $type, $attr) = $this->getInfo($p, $file);
		return $width;
	}
	
	function getHeight(phmagick $p, $file=''){
	   list($width, $height, $type, $attr)	 = $this->getInfo($p, $file);
	   return $height;
	}
	
	
	function getBits(phmagick $p, $file=''){
	   if ($file == '') $file = $p->getSource();
	   $info =  getimagesize  ($file);
	   return $info["bits"];
	}
	
	function getMime(phmagick $p, $file=''){
		if ($file == '') $file = $p->getSource();
       $info =  getimagesize  ($file);
       return $info["mime"];
	}
}
?>