<?php

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
		$this->oldImage = new Imagick($fileName);
		
		//$this->setOptions($options);	
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
//echo imagick::INTERPOLATE_AVERAGE;
//exit;
		$this->oldImage->adaptiveResizeImage($maxWidth, $maxHeight);//, Imagick::FILTER_CATROM, 1);
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
	
}
