<?php  

class ThumbnailHelper extends AppHelper {  

	function render($image, $params = array())
	{  
		//Set defaults  
		$path    = 'thumbs/';  
		$width   = 50;
		$height  = 50;
		$quality = 75;

		//Extract Parameters
		if (isset($params['path'])) {
			$path = $params['path'].DS;
		}

		if (isset($params['width'])) {
			$width = $params['width'];
		}

		if (isset($params['height'])) {
			$height = $params['height'];
		}

		if (isset($params['quality'])) {
			$quality = $params['quality'];
		}


		//import phpThumb class
		app::import ('Vendor', 'phpthumb', array ('file' => 'phpThumb' . DS . 'phpthumb.class.php') );

		$thumbNail = new phpthumb;
		$thumbNail->src = WWW_ROOT . 'img' . DS . $path . $image;
		$thumbNail->w = $width;
		$thumbNail->h = $height;
		$thumbNail->q = $quality;
		$thumbNail->config_imagemagick_path = '/usr/bin/convert';
		$thumbNail->config_prefer_imagemagick = true;
		$thumbNail->config_output_format = 'jpg';
		$thumbNail->config_error_die_on_error = true;
		$thumbNail->config_document_root = '';
		$thumbNail->config_temp_directory = APP . 'tmp' . DS . 'thumbs';
		$thumbNail->config_cache_directory = WWW_ROOT . 'img' . DS . 'thumbscache' . DS;
		$thumbNail->config_cache_disable_warning = true;

		$cacheFilename = $image;

		$thumbNail->cache_filename = $thumbNail->config_cache_directory.$cacheFilename;


		if (!is_file($thumbNail->cache_filename)) {
			if ($thumbNail->GenerateThumbnail()) {
				$thumbNail->RenderToFile($thumbNail->cache_filename);
			}
		}

		if (is_file($thumbNail->cache_filename)) {
			return $cacheFilename;
		}
	}
}

?>