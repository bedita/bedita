<?php

define('BASE_PATH', dirname(__FILE__));
/**
 * Include the PhImagick Class
 */
require_once BASE_PATH . '/phmagick/phmagick.php';

/**
 * 
 * @package PhpThumb
 * @subpackage Core
 */
class ImagickThumb extends ThumbBase
{
	/**
	 * The prior image (before manipulation)
	 * 
	 * @var resource
	 */
	protected $oldImage;
	/**
	 * The working image (used during manipulation)
	 * 
	 * @var resource
	 */
	protected $workingImage;
	/**
	 * The current dimensions of the image
	 * 
	 * @var array
	 */
	protected $currentDimensions;
	/**
	 * The new, calculated dimensions of the image
	 * 
	 * @var array
	 */
	protected $newDimensions;
	/**
	 * The options for this class
	 * 
	 * This array contains various options that determine the behavior in
	 * various functions throughout the class.  Functions note which specific 
	 * option key / values are used in their documentation
	 * 
	 * @var array
	 */
	protected $options;
	/**
	 * The maximum width an image can be after resizing (in pixels)
	 * 
	 * @var int
	 */
	protected $maxWidth;
	/**
	 * The maximum height an image can be after resizing (in pixels)
	 * 
	 * @var int
	 */
	protected $maxHeight;
	/**
	 * The percentage to resize the image by
	 * 
	 * @var int
	 */
	protected $percent;
	
	protected $targetFile;
	protected $targetType;
	
	/**
	 * Class Constructor
	 * 
	 * @return 
	 * @param string $fileName
	 */
	public function __construct ($fileName, $options = array(), $isDataStream = false)
	{

		parent::__construct($fileName, $isDataStream);
		$this->determineFormat();
		
		
		/* TODO Compatibility COntrol
		 * if ($this->isDataStream === false)
		{
			$this->verifyFormatCompatiblity();
		}*/
		
		$this->oldImage = new phMagick($fileName);
		$this->workingImage = new phMagick($fileName);
		
		list($w,$h) = $this->oldImage->getInfo($this->oldImage->getSource());
		$this->currentDimensions = array
		(
			'width' 	=>$w,
			'height'	=>$h
		);
		
		$this->setOptions($options);
			
	}
	
	/**
	 * Class Destructor
	 * 
	 */
	public function __destruct ()
	{
		if (is_resource($this->oldImage))
		{
			$this->oldImage->destroy();
		}
		
		if (is_resource($this->workingImage))
		{
			$this->workingImage->destroy();
		}
	}
	
	
	##############################
	# ----- API FUNCTIONS ------ #
	##############################
	
	/**
	 * Resizes an image to be no larger than $maxWidth or $maxHeight
	 * 
	 * If either param is set to zero, then that dimension will not be considered as a part of the resize.
	 * Additionally, if $this->options['resizeUp'] is set to true (false by default), then this function will
	 * also scale the image up to the maximum dimensions provided.
	 * 
	 * @param int $maxWidth The maximum width of the image in pixels
	 * @param int $maxHeight The maximum height of the image in pixels
	 * @return GdThumb
	 */
	public function resize ($maxWidth = 0, $maxHeight = 0)
	{
		
		// make sure our arguments are valid
		if (!is_numeric($maxWidth))
		{
			throw new InvalidArgumentException('$maxWidth must be numeric');
		}
		
		if (!is_numeric($maxHeight))
		{
			throw new InvalidArgumentException('$maxHeight must be numeric');
		}
		
		// make sure we're not exceeding our image size if we're not supposed to
		if ($this->options['resizeUp'] === false) {
			$this->maxHeight	= (intval($maxHeight) > $this->currentDimensions['height']) ? $this->currentDimensions['height'] : $maxHeight;
			$this->maxWidth		= (intval($maxWidth) > $this->currentDimensions['width']) ? $this->currentDimensions['width'] : $maxWidth;
		} else {
			$this->maxHeight	= intval($maxHeight);
			$this->maxWidth		= intval($maxWidth);
		}
		
		$this->calcImageSize($this->currentDimensions['width'], $this->currentDimensions['height']);
		
		$this->workingImage->resize($this->newDimensions['newWidth'], $this->newDimensions['newHeight']); 
		
		
		// update all the variables and resources to be correct
		$this->oldImage 			= $this->workingImage;
		$this->currentDimensions['width'] 	= $this->newDimensions['newWidth'];
		$this->currentDimensions['height'] 	= $this->newDimensions['newHeight'];
		
		return $this;
		
	}
	
	/**
	 * Adaptively Resizes the Image
	 * 
	 * This function attempts to get the image to as close to the provided dimensions as possible, and then crops the 
	 * remaining overflow (from the center) to get the image to be the size specified
	 * 
	 * @param int $maxWidth
	 * @param int $maxHeight
	 * @return GdThumb
	 */
	public function adaptiveResize ($width, $height)
	{
		// make sure our arguments are valid
		if (!is_numeric($width) || $width  == 0)
		{
			throw new InvalidArgumentException('$width must be numeric and greater than zero');
		}
		
		if (!is_numeric($height) || $height == 0)
		{
			throw new InvalidArgumentException('$height must be numeric and greater than zero');
		}
		
		// make sure we're not exceeding our image size if we're not supposed to
		if ($this->options['resizeUp'] === false)
		{
			$this->maxHeight	= (intval($height) > $this->currentDimensions['height']) ? $this->currentDimensions['height'] : $height;
			$this->maxWidth		= (intval($width) > $this->currentDimensions['width']) ? $this->currentDimensions['width'] : $width;
		}
		else
		{
			$this->maxHeight	= intval($height);
			$this->maxWidth		= intval($width);
		}
		
		$this->calcImageSizeStrict($this->currentDimensions['width'], $this->currentDimensions['height']);
		
		$this->workingImage->resizeExactly($this->newDimensions['newWidth'], $this->newDimensions['newHeight']); 
		
		
		// update all the variables and resources to be correct
		$this->oldImage 					= $this->workingImage;
		$this->currentDimensions['width'] 	= $width;
		$this->currentDimensions['height'] 	= $height;
		
		return $this;
	}
	
    public function resizeFill($width, $height, $background ) {
		
    	// make sure our arguments are valid
		if (!is_numeric($width) )
		{
			throw new InvalidArgumentException('$width must be numeric and greater than zero');
		}
		
		if (!is_numeric($height) )
		{
			throw new InvalidArgumentException('$height must be numeric and greater than zero');
		}
		
    	// make sure we're not exceeding our image size if we're not supposed to
		if ($this->options['resizeUp'] === false) {
			$this->maxHeight	= (intval($height) > $this->currentDimensions['height']) ? $this->currentDimensions['height'] : $height;
			$this->maxWidth		= (intval($width) > $this->currentDimensions['width']) ? $this->currentDimensions['width'] : $width;
		} else {
			$this->maxHeight	= intval($height);
			$this->maxWidth		= intval($width);
		}
	
		//Correct background hex
		$background = '#'.$background;
		
		$this->workingImage->resizeExactlyNoCrop($this->maxWidth, $this->maxHeight, $background); 
		
		// update all the variables and resources to be correct
		$this->oldImage 					= $this->workingImage;
		$this->currentDimensions['width'] 	= $width;
		$this->currentDimensions['height'] 	= $height;
		
		return $this;

    }
    
	public function resizeStretch($width, $height) 
	{
		
	   	// make sure our arguments are valid
		if (!is_numeric($width) )
		{
			throw new InvalidArgumentException('$width must be numeric and greater than zero');
		}
		
		if (!is_numeric($height) )
		{
			throw new InvalidArgumentException('$height must be numeric and greater than zero');
		}
		
		// get the new dimensions...
		$this->newDimensions = array (
			'newWidth'	=> $width,
			'newHeight'	=> $height
		);
		
		$this->workingImage->resize($this->newDimensions['newWidth'], $this->newDimensions['newHeight'], TRUE); 
		
		
		// update all the variables and resources to be correct
		$this->oldImage 					= $this->workingImage;
		$this->currentDimensions['width'] 	= $width;
		$this->currentDimensions['height'] 	= $height;
		
		return $this;

	}
	
	public function crop ($startX, $startY, $cropWidth, $cropHeight)
	{
		
		
		// validate input
		if (!is_numeric($startX))
		{
			throw new InvalidArgumentException('$startX must be numeric');
		}
		
		if (!is_numeric($startY))
		{
			throw new InvalidArgumentException('$startY must be numeric');
		}
		
		if (!is_numeric($cropWidth))
		{
			throw new InvalidArgumentException('$cropWidth must be numeric');
		}
		
		if (!is_numeric($cropHeight))
		{
			throw new InvalidArgumentException('$cropHeight must be numeric');
		}
		
		// do some calculations
		$cropWidth	= ($this->currentDimensions['width'] < $cropWidth) ? $this->currentDimensions['width'] : $cropWidth;
		$cropHeight = ($this->currentDimensions['height'] < $cropHeight) ? $this->currentDimensions['height'] : $cropHeight;
		
		// ensure everything's in bounds
		if (($startX + $cropWidth) > $this->currentDimensions['width'])
		{
			$startX = ($this->currentDimensions['width'] - $cropWidth);
			
		}
		
		if (($startY + $cropHeight) > $this->currentDimensions['height'])
		{
			$startY = ($this->currentDimensions['height'] - $cropHeight);
		}
		
		if ($startX < 0) 
		{
			$startX = 0;
		}
		
	    if ($startY < 0) 
		{
			$startY = 0;
		}
		
		$this->workingImage->crop($cropWidth, $cropHeight, $startY, $startX, phMagickGravity::None); 
		
		$this->oldImage 					= $this->workingImage;
		$this->currentDimensions['width'] 	= $cropWidth;
		$this->currentDimensions['height'] 	= $cropHeight;
		
		return $this;
	}
	
	
	/**
	 * Shows an image
	 * 
	 * This function will show the current image by first sending the appropriate header
	 * for the format, and then outputting the image data. If headers have already been sent, 
	 * a runtime exception will be thrown 
	 * 
	 * @param bool $rawData Whether or not the raw image stream should be output
	 * @return GdThumb
	 */
	public function show ($rawData = false) 
	{
		if (headers_sent())
		{
			throw new RuntimeException('Cannot show image, headers have already been sent');
		}
		
		switch ($this->format) 
		{
			case 'GIF':
				if ($rawData === false) 
				{ 
					header('Content-type: image/gif'); 
				}

				break;
			case 'JPG':
				if ($rawData === false) 
				{ 
					header('Content-type: image/jpeg'); 
				}

				break;
			case 'PNG':
			case 'STRING':
				if ($rawData === false) 
				{ 
					header('Content-type: image/png'); 
				}
				break;
		}
		echo $this->oldImage;		
		return $this;
	}	


	/**
	 * Determines the file format by mime-type
	 * 
	 * This function will throw exceptions for invalid images / mime-types
	 * 
	 */
	protected function determineFormat ()
	{
		if ($this->isDataStream === true)
		{
			$this->format = 'STRING';
			return;
		}
		
		$formatInfo = getimagesize($this->fileName);
		
		// non-image files will return false
		if ($formatInfo === false)
		{
			if ($this->remoteImage)
			{
				$this->triggerError('Could not determine format of remote image: ' . $this->fileName);
			}
			else
			{
				$this->triggerError('File is not a valid image: ' . $this->fileName);
			}
			
			// make sure we really stop execution
			return;
		}
		
		$mimeType = isset($formatInfo['mime']) ? $formatInfo['mime'] : null;
		
		switch ($mimeType)
		{
			case 'image/gif':
				$this->format = 'GIF';
				break;
			case 'image/jpeg':
				$this->format = 'JPG';
				break;
			case 'image/png':
				$this->format = 'PNG';
				break;
			default:
				$this->triggerError('Image format not supported: ' . $mimeType);
		}
	}



    public function wmark ($fileName, $params) {

        $p = new phMagick('', 'test.png');

        //Text watermark
        if (!empty($params['text'])) {
            $format = new phMagickTextObject();
            $format->fontSize($params['fontSize'])
                ->font($params['font'])
                ->color($params['textColor'])
                ->background($params['background']);

            $p->fromString(html_entity_decode($params['text'] ,null, 'utf-8'), $format);
        }

        //watermark images on else{}
        //
        //if (!empty($params['file'])) {
        //    $p->watermark( $params['file'], "SouthWest", 90);
        //}

        $p->setSource($this->workingImage->getDestination());
        $p->setDestination($this->workingImage->getDestination());

        $p->watermark( 'test.png', $params['align'], $params['opacity']);
        $this->oldImage = $this->workingImage;
        return $this;

    }


	/**
	 * Saves an image
	 * for imagemagick is useless?
	 * 
	 */
	public function save ($fileName, $format = null)
	{
		$validFormats = array('GIF', 'JPG', 'PNG');
		$format = ($format !== null) ? strtoupper($format) : $this->format;
		
		if (!in_array($format, $validFormats))
		{
			throw new InvalidArgumentException ('Invalid format type specified in save function: ' . $format);
		}
		
		// make sure the directory is writeable
		if (!is_writeable(dirname($fileName)))
		{
			// try to correct the permissions
			if ($this->options['correctPermissions'] === true)
			{
				@chmod(dirname($fileName), 0777);
				
				// throw an exception if not writeable
				if (!is_writeable(dirname($fileName)))
				{
					throw new RuntimeException ('File is not writeable, and could not correct permissions: ' . $fileName);
				}
			}
			// throw an exception if not writeable
			else
			{
				throw new RuntimeException ('File not writeable: ' . $fileName);
			}
		}
		
		//Controllare l'estensione e nel caso convertire il file
		
		return $this;
	}
	
	
	
	public function setDestination ($fileName, $format = null) {
		$this->targetFile = $fileName;
		$this->workingImage->setDestination($this->targetFile);
		$this->targetType = $format;
	}
	
	/**
	 * Sets $this->options to $options
	 * 
	 * @param array $options
	 */
	public function setOptions ($options = array())
	{
		// make sure we've got an array for $this->options (could be null)
		if (!is_array($this->options))
		{
			$this->options = array();
		}
		
		// make sure we've gotten a proper argument
		if (!is_array($options))
		{
			throw new InvalidArgumentException ('setOptions requires an array');
		}
		
		// we've yet to init the default options, so create them here
		if (sizeof($this->options) == 0)
		{
			$defaultOptions = array 
			(
				'resizeUp'			=> false,
				'jpegQuality'			=> 100,
				'correctPermissions'		=> false,
				'preserveAlpha'			=> true,
				'alphaMaskColor'		=> array (255, 255, 255),
				'preserveTransparency'		=> true,
				'transparencyMaskColor'		=> array (0, 0, 0)
			);
		}
		// otherwise, let's use what we've got already
		else
		{
			$defaultOptions = $this->options;
		}
		
		$this->options = array_merge($defaultOptions, $options);
		$this->workingImage->setImageQuality($this->options['jpegQuality']);
	}
	
	
	#################################
	# ----- UTILITY FUNCTIONS ----- #
	#################################
	
	/**
	 * Calculates a new width and height for the image based on $this->maxWidth and the provided dimensions
	 * 
	 * @return array 
	 * @param int $width
	 * @param int $height
	 */
	protected function calcWidth ($width, $height)
	{
	
		$newWidthPercentage	= (100 * $this->maxWidth) / $width;
		$newHeight			= ($height * $newWidthPercentage) / 100;
		
		return array
		(
			'newWidth'	=> intval($this->maxWidth),
			'newHeight'	=> intval($newHeight)
		);
	}
	
	/**
	 * Calculates a new width and height for the image based on $this->maxWidth and the provided dimensions
	 * 
	 * @return array 
	 * @param int $width
	 * @param int $height
	 */
	protected function calcHeight ($width, $height)
	{
		
		$newHeightPercentage	= (100 * $this->maxHeight) / $height;
		$newWidth 				= ($width * $newHeightPercentage) / 100;
		
		return array
		(
			'newWidth'	=> ceil($newWidth),
			'newHeight'	=> ceil($this->maxHeight)
		);
	}
	
	/**
	 * Calculates a new width and height for the image based on $this->percent and the provided dimensions
	 * 
	 * @return array 
	 * @param int $width
	 * @param int $height
	 */
	protected function calcPercent ($width, $height)
	{
		$newWidth	= ($width * $this->percent) / 100;
		$newHeight	= ($height * $this->percent) / 100;
		
		return array 
		(
			'newWidth'	=> ceil($newWidth),
			'newHeight'	=> ceil($newHeight)
		);
	}
	
	/**
	 * Calculates the new image dimensions
	 * 
	 * These calculations are based on both the provided dimensions and $this->maxWidth and $this->maxHeight
	 * 
	 * @param int $width
	 * @param int $height
	 */
	protected function calcImageSize ($width, $height)
	{
		$newSize = array
		(
			'newWidth'	=> $width,
			'newHeight'	=> $height
		);
		
		if ($this->maxWidth > 0)
		{
			$newSize = $this->calcWidth($width, $height);
			
			if ($this->maxHeight > 0 && $newSize['newHeight'] > $this->maxHeight)
			{
				$newSize = $this->calcHeight($newSize['newWidth'], $newSize['newHeight']);
			}
		}
		
		if ($this->maxHeight > 0)
		{
			$newSize = $this->calcHeight($width, $height);
			
			if ($this->maxWidth > 0 && $newSize['newWidth'] > $this->maxWidth)
			{
				$newSize = $this->calcWidth($newSize['newWidth'], $newSize['newHeight']);
			}
		}
		
		$this->newDimensions = $newSize;
	}
	
	/**
	 * Calculates new image dimensions, not allowing the width and height to be less than either the max width or height 
	 * 
	 * @param int $width
	 * @param int $height
	 */
	protected function calcImageSizeStrict ($width, $height)
	{
		// first, we need to determine what the longest resize dimension is..
		if ($this->maxWidth >= $this->maxHeight)
		{
			// and determine the longest original dimension
			if ($width > $height)
			{
				$newDimensions = $this->calcHeight($width, $height);
				
				if ($newDimensions['newWidth'] < $this->maxWidth)
				{
					$newDimensions = $this->calcWidth($width, $height);
				}
			}
			elseif ($height >= $width)
			{
				$newDimensions = $this->calcWidth($width, $height);
				
				if ($newDimensions['newHeight'] < $this->maxHeight)
				{
					$newDimensions = $this->calcHeight($width, $height);
				}
			}
		}
		elseif ($this->maxHeight > $this->maxWidth)
		{
			if ($width >= $height)
			{
				$newDimensions = $this->calcWidth($width, $height);
				
				if ($newDimensions['newHeight'] < $this->maxHeight)
				{
					$newDimensions = $this->calcHeight($width, $height);
				}
			}
			elseif ($height > $width)
			{
				$newDimensions = $this->calcHeight($width, $height);
				
				if ($newDimensions['newWidth'] < $this->maxWidth)
				{
					$newDimensions = $this->calcWidth($width, $height);
				}
			}
		}
		
		$this->newDimensions = $newDimensions;
	}
	
	/**
	 * Calculates new dimensions based on $this->percent and the provided dimensions
	 * 
	 * @param int $width
	 * @param int $height
	 */
	protected function calcImageSizePercent ($width, $height)
	{
		if ($this->percent > 0)
		{
			$this->newDimensions = $this->calcPercent($width, $height);
		}
	}
	
	
	
	
}
