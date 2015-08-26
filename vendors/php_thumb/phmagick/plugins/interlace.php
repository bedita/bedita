<?php
/**
 * phMagick - Interlace function
 *
 * @package    phMagick
 * @version    0.1.0
 * @author     Channelweb - info@channelweb.it
 * @copyright  Copyright (c) 2015
 * @license LGPL
 */

class phMagick_interlace {

    /**
     *
     * @param $width Integer
     * @param $height Integer
     * @param $top Integer - The Y coordinate for the left corner of the crop rectangule
     * @param $left Integer - The X coordinate for the left corner of the crop rectangule
     * @param $gravity phMagickGravity - The initial placement of the crop rectangule
     * @return unknown_type
     */
    function interlace(phmagick $p, $type = 'Plane') {
        $cmd  = $p->getBinary('convert');
        $cmd .= ' ' . $p->getSource();
        $cmd .= ' -interlace ' . $type;
        $cmd .= ' ' . $p->getDestination();
        $p->execute($cmd);
        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());
        return  $p;
    }
}
