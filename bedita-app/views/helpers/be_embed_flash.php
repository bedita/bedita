<?php

class BeEmbedFlashHelper extends AppHelper {
	
	public $helpers = array("Javascript");
	private $heightDef = ""; 
	private $widthDef = "";
	private $playerDefault = "";
	private $appVerDef = "9.0.0";
	
	public function embedSwf ($swfUrl, $attributes = array(), $flashvars = array(), $params = array()) {
		$width = (!empty($attributes['width'])) ? $attributes['width'] : $this->widthDef;
		$height = (!empty($attributes['height'])) ? $attributes['height'] : $this->heightDef;
		$id = (!empty($attributes['id']))? $attributes['id'] : "be_id_" . microtime();
		$app_ver = (!empty($attributes['application_version']))? $attributes['application_version'] : $this->appVerDef;
		
		if (!empty($attributes['src'])) {
			unset($attributes['src']);
		}
		
		$fv  = json_encode($flashvars);
		$par = json_encode($params);
		$att = json_encode($attributes);
		
		if ( defined("BEDITA_CORE_PATH") && !file_exists(APP . "webroot/js/swfobject.js")) {
			$output = $this->Javascript->link(Configure::read('beditaUrl') . "/js/swfobject.js",false);
		} else {
			$output = $this->Javascript->link("swfobject",false);
		}
		$output .= '<script type="text/javascript">swfobject.embedSWF("'.$swfUrl.'","'.$id.'","'.$width.'","'.$height.'","'.$app_ver.'","expressInstall.swf",'.$fv.','.$par.','.$att.');</script><div id="'.$id.'"></div>';
		return $output;
	}
	
	
	public function embedFlv ( $flvUrl, $attributes = array(), $flashvars = array(), $params = array(), $provider = array()) {
		$appPath = (defined("BEDITA_CORE_PATH"))? BEDITA_CORE_PATH . DS : APP;
		$beditaUrl = Configure::read('beditaUrl');
		$this->playerDefault = $beditaUrl."/swf/flowplayer.swf";
		$swfUrl = empty($attributes['src']) ? $this->playerDefault : $beditaUrl."/swf/".$attributes['src'] ;

		$pathParts = pathinfo($swfUrl);
		$methodName = "embed".Inflector::camelize($pathParts['filename']);
		
		if (method_exists($this, $methodName ) ) {
			return $this->$methodName($swfUrl, $flvUrl, $attributes, $flashvars, $params);
		} 		
		return $this->embedSwf( $swfUrl , $attributes, $flashvars, $params );		
	}
	
	
	
	public function embed ($obj , $params, $htmlAttributes ) {
		
		$flashvars = empty($params['flashvars']) ? array() : $params['flashvars'];	
		$flashParams = empty($params['params']) ? array() : $params['params'];	
		
		//if ($obj['provider'] =='youtube')
		//	$provider = array('provider'=>$obj['provider'], 'videoId'=>obj['uid']);

		$path_parts = pathinfo($obj['path']);
		if (empty($path_parts['extension']))
			return false;
			
		if ($path_parts['extension'] == 'flv') {			
			return $this->embedFlv($obj['path'], $htmlAttributes, $flashvars, $flashParams /*, $provider*/);	
		} else if ($path_parts['extension'] == 'swf') {
			 return $this->embedSwf($obj['path'], $htmlAttributes, $flashvars, $flashParams);
		} else {
			return false;
		}
		
	} 
	
	private function embedFlowplayer($swfUrl, $flvUrl, $attributes, $flashvars, $params, $provider = array()) {
		$flashvars['config'] = array();
		$stringPlugins='';
		
		if (empty($flashvars['clip'])) {
			$flashvars['config'] = "{'clip':{'url':'".$flvUrl."'}";
		}else{
			$stringClip = json_encode($flashvars['clip']);
			$flashvars['config'] = "{'clip':".$stringClip;
		}
		
		if (!empty($flashvars['plugins'])) {
			$stringPlugins = json_encode($flashvars['plugins']);
			$flashvars['config'] = $flashvars['config'].", 'plugins':".$stringPlugins;
		}
		
		$flashvars['config'] = $flashvars['config']."}";
		unset($flashvars['clip']);
		unset($flashvars['plugins']);
		return $this->embedSwf( $swfUrl , $attributes, $flashvars, $params );
	}
	
}













?>