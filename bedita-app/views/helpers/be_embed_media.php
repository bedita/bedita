<?php
/**
 *
 * Insert multimedia objects through specific BEdita methods.
 *
 * @package
 * @subpackage
 * @author  ChannelWeb srl - Bedita stuff
 */
class BeEmbedMediaHelper extends AppHelper {

	// vars
	// protected $link;
    private $_attributes = array();
    private $_type;
	private $_conf;

	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html', 'MediaProvider');
	
	
	function __construct()
	{
		// get configuration parameters
		$this->_conf = Configure::read('media') ;
		$this->_conf['root']  = Configure::read('mediaRoot');
		$this->_conf['url']   = Configure::read('mediaUrl');
		$this->_conf['cache'] = Configure::read('imgCache');
	}





	// embed a generic object
	public function object ( $obj, $attributes = array() )
	{
		// merge with call params
		$this->_attributes = array_merge($this->_conf, $attributes) ;


		// get object type
		$this->_type = $this->_getType ($obj);
		

		// call the right method
		switch ($this->_type)
		{
			case 'image':
				$this->image ($obj);
				break;
			
			case 'audio':
				return "known type: " . $this->_type;
				break;
			
			case 'video':
				return "known type: " . $this->_type;
				break;
			
			default:
				return "unknown type: " . $this->_type;
				exit;
		}
	}



	// embed an image
	public function image ( $obj, $attributes = array () )
	{
		// merge with call params
		$this->_attributes = array_merge($this->_conf, $attributes) ;

		//pr($this->_attributes);
		$_params['w'] = $this->_attributes['image']['thumbWidth'];
		$_params['h'] = $this->_attributes['image']['thumbHeight'];
		$_params['filePath']  =  $obj['path'];
		$_params['filename']  =  $obj['filename'];
		$_params['file']      =  $this->_attributes['root'] . $obj['path'];
		$_params['link']      =  false;
		$_params['linkurl']   =  $this->Html->url('/multimedia/view/') . $obj['id'];
		$_params['cache']     =  $this->_attributes['url'] . "/" . $this->_attributes['cache'] . "/";
		$_params['cachePATH'] =  $this->_attributes['root'] . DS . $this->_attributes['cache'] . DS;
		/*
		echo "<br />hint = "      .  false;
		echo "<br />html = "      .  "alt='" . $obj['title'] . "'";
		echo "<br />window = "    .  false;

		// $_params['thumbWidth'] =  $this->_attributes;

					pr($cake->view['_smarty']);
				
						width			= $thumbWidth
						height			= $thumbHeight
						file			= $mediaPath$filePath
						link = "false"
						linkurl			= $linkUrl
						cache			= $mediaCacheBaseURL
						cachePATH		= $mediaCachePATH
						hint			= "false"
						html			= $imageAltAttribute
						frame			= ""
						window			= "false"
					}
					*/
	}
/*
					{assign var="thumbWidth" 		value = 130}
					{assign var="thumbHeight" 		value = 85}
					{assign var="filePath"			value = $item.path}
					{assign var="fileName"			value = $item.filename|default:$item.name}
					{assign var="fileTitle"			value = $item.title}
					{assign var="newPriority"		value = $item.priority+1|default:$priority}
					{assign var="mediaPath"         value = $conf->mediaRoot}
					{assign var="mediaUrl"          value = $conf->mediaUrl}
					{assign_concat var="linkUrl"            0=$html->url('/multimedia/view/') 1=$item.id}
					{assign_concat var="imageAltAttribute"	0="alt='"  1=$item.title 2="'"}
					{assign_concat var="mediaCacheBaseURL"	0=$conf->mediaUrl  1="/" 2=$conf->imgCache 3="/"}
					{assign_concat var="mediaCachePATH"		0=$conf->mediaRoot 1=$conf->DS 2=$conf->imgCache 3=$conf->DS}
					
							{if !empty($fileName) }
								{thumb
									width			= $thumbWidth
									height			= $thumbHeight
									file			= $mediaPath$filePath
									link = "false"
									linkurl			= $linkUrl
									cache			= $mediaCacheBaseURL
									cachePATH		= $mediaCachePATH
									hint			= "false"
									html			= $imageAltAttribute
									frame			= ""
									window			= "false"
								}
							{else}
								<img src="{$session->webroot}img/image-missing.jpg" width="{$thumbWidth}" />
							{/if}
					
						{elseif ($item.provider|default:false)}
*/



	// helper functions
	private function _getType ($obj)
	{
		return strtolower ( $obj['ObjectType']['name'] );
	}

}
?>
