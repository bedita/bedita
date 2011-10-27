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
 * phMagick - Image transformation functions
 *
 * @package    phMagick
 * @version    0.1.0
 * @author     Nuno Costa - sven@francodacosta.com
 * @copyright  Copyright (c) 2007
 * @license    http://www.francodacosta.com/licencing/
 * @link       http://www.francodacosta.com/phmagick
 * @since      2008-03-13
 */
class phMagick_transform{
    function rotate (phmagick $p,$degrees=45){
        $cmd   = $p->getBinary('convert');
        $cmd .= ' -background "transparent" -rotate ' . $degrees ;
        $cmd .= '  "' . $p->getSource().'"' ;
        $cmd .= ' "' . $p->getDestination().'"' ;

        $p->execute($cmd);
        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());
        return  $p ;
    }

    /**
     * Flips the image vericaly
     * @return unknown_type
     */
    function flipVertical(phmagick $p){
        $cmd  = $p->getBinary('convert');
        $cmd .= ' -flip ' ;
        $cmd .= ' "' . $p->getSource() .'"';
        $cmd .= ' "' . $p->getDestination() .'"';

        $p->execute($cmd);
        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());
        return  $p ;
    }

    /**
     * Flips the image horizonaly
     * @return unknown_type
     */
    function flipHorizontal(phmagick $p){
        $cmd  = $p->getBinary('convert');
        $cmd .= ' -flop ' ;
        $cmd .= ' "' . $p->getSource() .'"';
        $cmd .= ' "' . $p->getDestination().'"' ;

        $p->execute($cmd);
        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());
        return  $p ;
    }

/**
     * Flips the image horizonaly and verticaly
     * @return unknown_type
     */
    function reflection(phmagick $p, $size = 60, $transparency = 50){
    	$p->requirePlugin('info');

    	$source = $p->getSource();

    	//invert image
    	$this->flipVertical($p);

    	//crop it to $size%
        list($w, $h) = $p->getInfo($p->getDestination());
        $p->crop($w, $h * ($size/100),0,0,phMagickGravity::None);

        //make a image fade to transparent
        $cmd  = $p->getBinary('convert');
        $cmd .= ' "' . $p->getSource() .'"';
        $cmd .= ' ( -size ' . $w.'x'. ( $h * ($size/100)) .' gradient: ) ';
        $cmd .= ' +matte -compose copy_opacity -composite ';
        $cmd .= ' "' . $p->getDestination().'"' ;

        $p->execute($cmd);

        //apply desired transparency, by creating a transparent image and merge the mirros image on to it with the desired transparency
        $file = dirname($p->getDestination()) . '/'. uniqid() . '.png';

        $cmd  = $p->getBinary('convert');
        $cmd .= '  -size ' . $w.'x'. ( $h * ($size/100)) .' xc:none  ';
        $cmd .= ' "' . $file .'"' ;

        $p->execute($cmd);

        $cmd   = $p->getBinary('composite');
        $cmd .= ' -dissolve ' . $transparency ;
        $cmd .= ' "' . $p->getDestination() .'"' ;
        $cmd .= ' ' . $file ;
        $cmd .= ' "' . $p->getDestination() .'"' ;

        $p->execute($cmd);

        unlink($file);

        //append the source and the relfex
        $cmd  = $p->getBinary('convert');
        $cmd .= ' "' . $source .'"' ;
        $cmd .= ' "' . $p->getDestination().'"' ;
        $cmd .= ' -append ';
        $cmd .= ' "' . $p->getDestination().'"' ;

        $p->execute($cmd);

        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());
        return  $p ;
    }



}
?>