<?php
	
/**
 * BEdita Thumbnail helper
 * ------------------------------------------------------------------------------------
 * Name:     be_embed_media
 * Version:  1.0
 * Author:   Christiano Presutti - aka xho - ChannelWeb srl - Bedita staff
 * Purpose:  Insert BEdita multimedia objects in HTML page
 * 
 * Public methods:  image()
 *                  Returns: HTML <img> tag pointing to URI of cached image file
 * 
 * ------------------------------------------------------------------------------------
 */


class BeEmbedMediaHelper extends AppHelper {

	private $_helpername = "BeEmbedMedia Helper";



	// output template for sprintf non funziona e vai a fare in culo lo vedo dopo
	// private $_html	= '<img src="%s" width="%d" height="%d" alt="%s" />';



	// public

	// private
	private $_arguments  = array();
	private $_objects = array ("image", "audio", "video", "flash"); // supported
    private $_output  = false;
	private $_conf    = array ();




	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html', 'BeThumb', 'MediaProvider');
	
	
	function __construct()
	{
		// get configuration parameters
		$this->_conf = Configure::read('media') ;
		$this->_conf['root']  = Configure::read('mediaRoot');
		$this->_conf['url']   = Configure::read('mediaUrl');
		$this->_conf['cache'] = Configure::read('imgCache');
		$this->_conf['tmp']   = Configure::read('tmp');
		$this->_conf['imgMissingFile'] = Configure::read('imgMissingFile');
	}





	/*
	 * object public method: embed a generic bedita multimedia object
	 * 
	 * params: be_obj, required, object, BEdita Multimedia Object
	 *         params, optional, parameters used by external helpers such as BeThumb->image
	 *         
	 * return: output complete html tag
	 * 
	 */
	public function object ( $obj, $params = null )
	{
		// merge with call params
		// $this->_attributes = array_merge($this->_conf, $attributes) ;




		// get object type
		$this->_type = $this->_getType ($obj);


		// clean up onjects
		$this->_resetObjects();
		

		// call the right method
		switch ($this->_type)
		{
			// image
			case 'image':
				// read params as an associative array or multiple variable
				$expectedArgs = array ('width', 'height', 'longside', 'mode', 'modeparam', 'type', 'upscale');
				if ( func_num_args() > 1 && !is_array( func_get_arg(1) ) )
				{
					$argList = func_get_args() ;
					array_shift($argList);
				    for ($i = 0; $i < sizeof($expectedArgs); $i++)
					{
				        if ( isset ($argList[$i]) && $argList[$i] !== null )
							$this->_arguments[$expectedArgs[$i]] = $argList[$i];
				    }
				}
				else $this->_arguments = $params;
				unset ($params);
				$this->_output = $this->_image ($obj, $this->_arguments);
				break;



			// video
			case 'video':
				$this->_output = $this->MediaProvider->thumbnail($obj);
				break;



			// audio
			case 'audio':
				return "known type: " . $this->_type;
				break;



			// unknown
			default:
				return "unknown type: " . $this->_type; //$this->_conf['imgMissingFile']
				exit;
		}
		
		// output HTML
		return $this->_output;
	}



	/******************************
	 * private functions
	 *****************************/


	/*
	 * return object type
	 */
	private function _getType ($obj)
	{
		return strtolower ( Configure::read("objectTypes." . $obj['object_type_id'] . ".name") );
	}



	/*
	 * produce html <img> tag
	 */
	private function _image ( $obj, $params = null )
	{
		if (strpos($obj['path'], "/") === 0)
			$src = $this->BeThumb->image ($obj, $params);
		else
			$src = $obj['path'];
		$html  = "<img src='" . $src;
		$html .= "' title='" . $obj['name'];
		$html .= "' />";

		return $html;
	}



	/*
	 * reset internal objects to empty defaults
	 */
	private function _resetObjects()
	{
		$this->_output = "";
	}

}
?>
