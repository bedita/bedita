<?
/**
 * @author giangi@qwerg.com
 * 
 * Translation properties manipulation
 * 
 */
class BeLangTextComponent extends Object {

	var $controller = null ;
	var $uses = array('LangText');

	function __construct() {
		foreach ($this->uses as $model) {
			if(!class_exists($model))
				App::import('Model', $model) ;
			$this->{$model} = new $model() ;
		}
	} 

	function startup(&$controller) {
		$this->controller = $controller;
	}

	function setupForSave(&$data) {
		$result = array() ;
		if(!@count($data)) return ;
		foreach($data as $lang => $attributes) {
			foreach($attributes as $attribute => $value) {
				if($attribute != 'type' && $value != '') {
					$formatted = array() ;
					$formatted['lang'] = $lang ;
					$formatted['name'] = $attribute ;
					if(strlen($value) <= 255)
						$formatted['text'] = $value ;
					else
						$formatted['long_text'] = $value ;
					$translation[]=$formatted;
				}
			}
		}
		$data = $translation ;
	}

	function setupForView(&$data) {
		$tmp = array() ;
		for($i=0; $i < count($data) ; $i++) {
			$item = &$data[$i] ;
			if(!isset($tmp[$item["name"]]))	$tmp[$item["name"]] = array() ;
			$tmp[$item["name"]][$item["lang"]] = (!@empty($item["text"])) ? @$item["text"] : @$item["long_text"] ;
		}
		$data = $tmp ;
	}
	
	function objectForLang($id,$lang,&$object) {
		$result=array();
		$tmpobj = $this->LangText->find('all',
			array(
				'fields'=>array('name','text','long_text'),
				'conditions'=>array("LangText.object_id = '$id'","LangText.lang = '$lang'")
			)
		);
		foreach($tmpobj as $k => $v) {
			$key = $v['LangText']['name'];
			$value= (!empty($v['LangText']['text']))?$v['LangText']['text']:$v['LangText']['long_text'];
			if(!empty($value)) $object[$key]=$value;
		}
	}
}
?>