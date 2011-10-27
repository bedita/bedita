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
 * phMagick - Image decorations function
 *
 * @package    phMagick
 * @version    0.1.0
 * @author     Nuno Costa - sven@francodacosta.com
 * @copyright  Copyright (c) 2007
 * @license    http://www.francodacosta.com/phmagick/license/
 * @link       http://www.francodacosta.com/phmagick
 * @since      2008-03-13
 */
class phmagick_decorations{
    function roundCorners(phmagick $p,$i = 15){

        //original idea from Leif ��strand <leif@sitelogic.fi>
        $cmd = $p->getBinary('convert');
        $cmd .= ' "' . $p->getSource().'"'  ;
        $cmd .= ' ( +clone  -threshold -1 ' ;
        $cmd .= "-draw \"fill black polygon 0,0 0,$i $i,0 fill white circle $i,$i $i,0\" ";
        $cmd .= '( +clone -flip ) -compose Multiply -composite ';
        $cmd .= '( +clone -flop ) -compose Multiply -composite ';
        $cmd .= ') +matte -compose CopyOpacity -composite ' ;
        $cmd .= ' "' . $p->getDestination().'"'  ;


        $p->execute($cmd);
        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());
        return  $p ;
    }

    function dropShadow(phmagick $p,$color = '#000', $offset = 4, $transparency = 60, $top = 4, $left=4){

    	$top = $top > 0 ? '+' . $top : $top;
    	$left = $left > 0 ? '+' . $left : $left;

        $cmd = $p->getBinary('convert');
        $cmd .= ' -page '.$top.$left.' "' . $p->getSource().'"'  ;
        $cmd .= ' -matte ( +clone -background "'. $color .'" -shadow '. $transparency.'x4+'.$offset.'+'.$offset.' ) +swap ';
        $cmd .= ' -background none -mosaic ';
        $cmd .= ' "' . $p->getDestination().'"'  ;

        $p->execute($cmd);
        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());
        return  $p ;
    }

    function glow(phmagick $p, $color='#827f00',$offset = 10, $transparency=60){

    	$p->requirePlugin('info');
    	list ($w, $h) = $p->getInfo($p->getSource());


    	$cmd  = $p->getBinary('convert');

    	$cmd .= ' "' . $p->getSource() .'" ' ;
        $cmd .= '( +clone  -background "'.$color.'"  -shadow '.$transparency.'x'.$offset.'-'.($offset/4).'+'.($offset/4).' ) +swap -background none   -layers merge  +repage  ';

        $cmd .= ' "' . $p->getDestination().'"'  ;

        $p->execute($cmd);
        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());
        return  $p ;
    }

    /**
     * Fake polaroid effect (white border and rotation)
     *
     * @param $rotation Int - The imahe will be rotatex x degrees
     * @param $borderColor - Polaroid border (ussuay white)
     * @param $shaddowColor - drop shaddow color
     * @param $background - Image background color (use for jpegs or images that do not support transparency or you will end up with a black background)
     */
    function fakePolaroid(phmagick $p,$rotate = 6 , $borderColor = "#fff", $background ="none"){
        $cmd = $p->getBinary('convert');
        $cmd .= ' "' . $p->getSource().'"'  ;
        $cmd .= ' -bordercolor "'. $borderColor.'"  -border 6 -bordercolor grey60 -border 1 -background  "none"   -rotate '. $rotate .' -background  black  ( +clone -shadow 60x4+4+4 ) +swap -background  "'. $background.'"   -flatten';
        $cmd .= ' ' . $p->getDestination() ;

        //echo $cmd .'<br>';;
        $ret = $p->execute($cmd);
        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());
        return  $p ;
    }

    /**
     * Real polaroid efect, supports text
     *
     * @param $format phMagickTextObject - text format for image label
     * @param $rotation Int - The imahe will be rotatex x degrees
     * @param $borderColor - Polaroid border (ussuay white)
     * @param $shaddowColor - drop shaddow color
     * @param $background - Image background color (use for jpegs or images that do not support transparency or you will end up with a black background)
     */
    function polaroid(phmagick $p, $format = null, $rotation= 6, $borderColor="snow", $shaddowColor = "black", $background="none"){



        if (get_class($format) == 'phMagickTextObject' ){
            //
        }else{
            $tmp = new phMagickTextObject();
            $tmp->text($format);
            $format = $tmp ;
        }

        $cmd = $p->getBinary('convert');
        $cmd .= ' "' . $p->getSource() .'"' ;


        if ($format->background !== false)
            $cmd .= ' -background "' . $format->background . '"';

        if ($format->color !== false)
            $cmd .= ' -fill "' . $format->color . '"' ;

        if ($format->font !== false)
            $cmd .= ' -font ' . $format->font ;

        if ($format->fontSize !== false)
            $cmd .= ' -pointsize ' . $format->fontSize ;

        if ($format->pGravity !== false)
            $cmd .= ' -gravity ' . $format->pGravity ;

        if ($format->pText != '')
            $cmd .= ' -set caption "' . $format->pText .'"';

        $cmd .= ' -bordercolor "'. $borderColor.'" -background "'.$background.'" -polaroid ' . $rotation .' -background "'. $background.'" -flatten ';
        $cmd .= ' "' . $p->getDestination().'"'  ;

        //echo $cmd .'<br>';;
        $p->execute($cmd);
        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());
        return  $p ;
    }

    function border(phmagick $p,$borderColor = "#000", $borderSize ="1"){
        $cmd = $p->getBinary('convert');
        $cmd .= ' "' . $p->getSource() .'"';
        $cmd .= ' -bordercolor "'. $borderColor.'"  -border ' . $borderSize;
        $cmd .= ' "' . $p->getDestination() .'"';

        $ret = $p->execute($cmd);
        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());
        return  $p ;
    }

}
?>