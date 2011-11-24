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
 * phMagick - Resising functions
 *
 * @package    phMagick
 * @version    0.1.1
 * @author     Nuno Costa - sven@francodacosta.com
 * @copyright  Copyright (c) 2007
 * @license    GPL v3
 * @link       http://www.francodacosta.com/phmagick
 * @since      2008-03-13
 */
class phMagick_resize{
    function resize(phmagick $p , $width, $height = 0, $exactDimentions = false){
        $modifier = $exactDimentions ? '!' : '>';

        //if $width or $height == 0 then we want to resize to fit one measure
        //if any of them is sent as 0 resize will fail because we are trying to resize to 0 px
        $width  = $width  == 0 ? '' : $width ;
        $height = $height == 0 ? '' : $height ;

        $cmd = $p->getBinary('convert');
        $cmd .=  ' -scale "'. $width .'x'. $height . $modifier ;
        $cmd .= '" -quality '. $p->getImageQuality() ;
        $cmd .=  ' -strip ';
        $cmd .= ' "' . $p->getSource() .'" "'. $p->getDestination().'"';


        $p->execute($cmd);
        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());
        return  $p ;
    }

    /**
     * tries to resize an image to the exact size wile mantaining aspect ratio,
     * the image will be croped to fit the measures
     * @param $width
     * @param $height
     */
    function resizeExactly(phmagick $p , $width, $height){
    	//requires Crop plugin
    	//requires dimensions plugin

    	$p->requirePlugin('crop');
    	$p->requirePlugin('info');

    	list($w,$h) = $p->getInfo($p->getSource());

    	if($w > $h){
		    $h = $height;
		    $w = 0;
		}else{
		    $h = 0;
		    $w = $width;
		}

		$p->resize($w, $h)->crop($width, $height);

    }
    
	/**
	* tries to resize an image to the exact size wile mantaining aspect ratio,
	* the image will be padded with background color to fit the measures
	* @param $width
	* @param $height
	* @param $background
	*/
	function resizeExactlyNoCrop(phmagick $p, $width, $height, $background='#FFFFFF'){
		
		$cmd = $p->getBinary('montage');
		$cmd .= ' -background ' . '"'. $background .'"';
		$cmd .= ' -geometry '. $width .'x'. $height;
		$cmd .= ' "' . $p->getSource() .'" "'. $p->getDestination().'"';
		$p->execute($cmd);
		$p->setSource($p->getDestination());
		$p->setHistory($p->getDestination());
		return $p;
	
	}
	

 /**
     * Creates a thumbnail of an image, if it doesn't exits
     *
     *
     * @param String $imageUrl - The image Url
     * @param Mixed $width - String / Integer
     * @param Mixed $height - String / Integer
     * @param boolean: False: resizes the image to the exact porportions (aspect ratio not preserved). True: preserves aspect ratio, only resises if image is bigger than specified measures
     *
     * @return String - the thumbnail URL
     */
    function onTheFly(phmagick $p,$imageUrl, $width, $height, $exactDimentions = false, $webPath = '', $physicalPath=''){
        //convert web path to physical
        $basePath = str_replace($webPath,$physicalPath, dirname($imageUrl) );
        $sourceFile = $basePath .'/'. basename($imageUrl); ;

        //naming the new thumbnail
        $thumbnailFile = $basePath . '/'.$width . '_' . $height . '_' . basename($imageUrl) ;

        $P->setSource($sourceFile);
        $p->setDestination($thumbnailFile);

        if (! file_exists($thumbnailFile)){
            $p->resize($p,$width, $height, $exactDimentions);
        }

        if (! file_exists($thumbnailFile)){
            //if there was an error, just use original file
            $thumbnailFile = $sourceFile;
        }

        //returning the thumbnail url
        return str_replace($physicalPath, $webPath, $thumbnailFile );

    }
}
?>