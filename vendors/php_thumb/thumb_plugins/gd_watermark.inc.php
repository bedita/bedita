<?php
/**
 * Add logo plugin
 * 
 * This plugin allows you to add plugin to your image
 * 
 * @package PhpThumb
 * @subpackage Plugins
 * @original-author Sergey "hssergey"
 * @author Sebastian "DNightmare"
 */
class GdAddLogo {
    /**
     * Instance of GdThumb passed to this class
     * 
     * @var GdThumb
     */
    protected $parentInstance;
    protected $currentDimensions;
    protected $workingImage;
    protected $newImage;
    protected $options;

    /**
     * Add logo to image
     * @param logoFileName - file name of logo image in jpg or png format
     * @param positionX - Position of logo image on X-axis ('left', 'center', 'right' or plain number)
     * @param positionY - Position of logo image on X-axis ('top', 'center', 'bottom' or plain number)
     * @param alpha - alpha value for logo merging in percent
     */
    public function addLogo($logoFileName, $positionX, $positionY, $alpha, &$that) {
        $logo_size                  = getimagesize($logoFileName);
        
	// bring stuff from the parent class into this class...
        $this->parentInstance       = $that;
        $this->oldImage             = $this->parentInstance->getOldImage();
        $this->parentInstance->setWorkingImage($this->oldImage);
        $this->currentDimensions    = $this->parentInstance->getCurrentDimensions();
        $this->workingImage         = $this->parentInstance->getWorkingImage();
        $this->options              = $this->parentInstance->getOptions();

        $src_dimension              = array(
                                        "x" => $this->currentDimensions['width'], 
                                        "y" => $this->currentDimensions['height']);
        $logo_dimension             = array(
                                        "x" => $logo_size[0],
                                        "y" => $logo_size[1]);

        $center                     = array(
                                        "x" => (($src_dimension["x"] / 2) - ($logo_dimension["x"]/2)),
                                        "y" => (($src_dimension["y"] / 2) - ($logo_dimension["y"]/2)));

        $logo_positionX["left"]      = 0;
        $logo_positionX["center"]    = $center["x"];
        $logo_positionX["right"]     = $src_dimension["x"] - $logo_dimension["x"];
        
	$logo_positionY["top"]       = 0;
        $logo_positionY["center"]    = $center["y"];
        $logo_positionY["bottom"]    = $src_dimension["y"] - $logo_dimension["y"];
	
	
        if(is_numeric($positionX)){ $logo_position["x"] = $positionX; } else { $logo_position["x"] = $logo_positionX[$positionX]; }
        if(is_numeric($positionY)){ $logo_position["y"] = $positionY; } else { $logo_position["y"] = $logo_positionY[$positionY]; }

        switch(exif_imagetype($logoFileName)){
            case IMAGETYPE_JPEG:
                $logo = imagecreatefromjpeg($logoFileName);
                break;
            case IMAGETYPE_PNG:
                $logo = imagecreatefrompng($logoFileName);
                break;
        }

        imagecopymerge($this->workingImage, $logo, $logo_position["x"], $logo_position["y"], 0, 0, $logo_dimension["x"], $logo_dimension["y"], $alpha);
        return $that;
    }
}
$pt = PhpThumb::getInstance();
$pt->registerPlugin('GdAddLogo', 'gd');
