<?php

class GdCustomBEdita extends GdThumb{
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
    protected $maxHeight;
    protected $maxWidth;

	//do not remove, need to avoid to call parent constructor 
	public function __construct (){}

    /**
     * Set image to be interlaced.
     *
     * @param bool $switch
     * @param GdThumb $that
     * @return GdThumb
     **/
    public function interlace($switch, &$that) {
        $this->parentInstance = $that;
        $this->oldImage = $this->parentInstance->getOldImage();
        $this->currentDimensions = $this->parentInstance->getCurrentDimensions();
        $this->workingImage = $this->parentInstance->getWorkingImage();
        $this->options = $this->parentInstance->getOptions();

        imageinterlace($this->oldImage, (bool) $switch);

        $this->parentInstance->setOldImage($this->oldImage);

        return $this->parentInstance;
    }
    
	
    public function resizeFill($width, $height, $background , &$that) {
       
		// bring stuff from the parent class into this class...
        $this->parentInstance       = $that;
        $this->oldImage             = $this->parentInstance->getOldImage();
        $this->currentDimensions    = $this->parentInstance->getCurrentDimensions();
        $this->workingImage         = $this->parentInstance->getWorkingImage();
        $this->options              = $this->parentInstance->getOptions();
		
		// make sure our arguments are valid
		if (!is_numeric($width)) {
			throw new InvalidArgumentException('$maxWidth must be numeric');
		}
		
		if (!is_numeric($height)) {
			throw new InvalidArgumentException('$maxHeight must be numeric');
		}
		
		// make sure we're not exceeding our image size if we're not supposed to
		if ($this->options['resizeUp'] === false) {
			$this->maxHeight	= (intval($height) > $this->currentDimensions['height']) ? $this->currentDimensions['height'] : $height;
			$this->maxWidth		= (intval($width) > $this->currentDimensions['width']) ? $this->currentDimensions['width'] : $width;
		} else {
			$this->maxHeight	= intval($height);
			$this->maxWidth		= intval($width);
		}
		// get the new dimensions...
		$this->calcImageSize($this->currentDimensions['width'], $this->currentDimensions['height']);
		
		// create the working image
		if (function_exists('imagecreatetruecolor')) {
			$this->workingImage = imagecreatetruecolor($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
		} else {
			$this->workingImage = imagecreate($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
		}
		
		//fill the background
		$hex1 = "0x".$background{0}.$background{1};
		$hex2 = "0x".$background{2}.$background{3};
		$hex3 = "0x".$background{4}.$background{5};
		$bkgd = ImageColorAllocate($this->workingImage, hexdec($hex1), hexdec($hex2), hexdec($hex3));
		imagefill ( $this->workingImage , 0 , 0, $bkgd );
		
		//$this->preserveAlpha();		
		
		// and create the newly sized image
		imagecopyresampled (
			$this->workingImage,
			$this->oldImage,
			0,
			0,
			0,
			0,
			$this->newDimensions['newWidth'],
			$this->newDimensions['newHeight'],
			$this->currentDimensions['width'],
			$this->currentDimensions['height']
		);
		
		//$im = ImageCreate($width, $height);
		//$red = ImageColorAllocate($im, 255, 0, 0);
		//imagefill ( $this->workingImage , $width , $height , $red );
		
	
		// update all the variables and resources to be correct
		$this->parentInstance->setOldImage($this->workingImage);
		//	$this->parentInstance->setCurrentDimensions($this->newDimensions);
		
		return $that;

    }



	public function resizeStretch($width, $height, &$that) 
	{
		// bring stuff from the parent class into this class...
		$this->parentInstance       = $that;
		$this->oldImage             = $this->parentInstance->getOldImage();
		$this->currentDimensions    = $this->parentInstance->getCurrentDimensions();
		$this->workingImage         = $this->parentInstance->getWorkingImage();
		$this->options              = $this->parentInstance->getOptions();


		// make sure our arguments are valid
		if (!is_numeric($width)) {
			throw new InvalidArgumentException('$maxWidth must be numeric');
		}

		if (!is_numeric($height)) {
			throw new InvalidArgumentException('$maxHeight must be numeric');
		}


		// get the new dimensions...
		$this->newDimensions = array (
			'newWidth'	=> $width,
			'newHeight'	=> $height
		);

		// create the working image
		if (function_exists('imagecreatetruecolor')) {
			$this->workingImage = imagecreatetruecolor($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
		} else {
			$this->workingImage = imagecreate($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
		}

		//$this->parentInstance->preserveAlpha();		

		// and create the newly sized image
		imagecopyresampled (
			$this->workingImage,
			$this->oldImage,
			0,
			0,
			0,
			0,
			$this->newDimensions['newWidth'],
			$this->newDimensions['newHeight'],
			$this->currentDimensions['width'],
			$this->currentDimensions['height']
		);

		// update all the variables and resources to be correct
		$this->parentInstance->setOldImage($this->workingImage);
		$this->parentInstance->setCurrentDimensions($this->newDimensions);

		return $that;

	}


}
$pt = PhpThumb::getInstance();
$pt->registerPlugin('GdCustomBEdita', 'gd');


?>
