<?php
/**
 * @filesource
 * @copyright		Copyright (c) 2007
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license
 * @author 		giangi giangi@qwerg.com, ste ste@channelweb.it
*/
class Section extends BeditaCollectionModel
{
	public $searchFields = array("title" => 10 , "description" => 6);

	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
										"UserCreated", 
										"UserModified", 
										"Permissions",
										"CustomProperties",
										"LangText")),

       			"default" => array("BEObject" => array("CustomProperties", 
									"LangText", "ObjectType")),

				"minimum" => array("BEObject" => array("ObjectType"))		
		);
	
	var $validate = array(
		'parent_id'	=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
	) ;
	

	function afterSave($created) {
		if (!$created) 
			return ;
		
		$tree = ClassRegistry::init('Tree');
		if($tree->appendChild($this->id, $this->data[$this->name]['parent_id'])===false)
			return false;
		return true;
	}

	//////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////
	/**
	 * Formatta i dati per la creazione di un clone, ogni tipo
	 * di oggetto esegue operazioni specifiche richiamando.
	 * Trova l'id del ramo in cui e' inserita
	 *
	 * @param array $data		Dati da formattare
	 * @param object $source	Oggetto sorgente
	 */
	protected function _formatDataForClone(&$data, $source = null) {
		if(!class_exists('Tree')) loadModel('Tree');

		$tree =& new Tree();
		
		$data['parent_id'] = $tree->getParent($data['id'])  ;		
		parent::_formatDataForClone($data);
	}	
	
	/**
	 * Esegue ricorsivamente solo la clonazione dei figli di tipo: Section e Community,
	 * gli altri reinscerisce un link
	 *
	 */
	protected function insertChildrenClone() {
		$conf  	= Configure::getInstance() ;
		$tree 	=& new Tree();
		
		// Preleva l'elenco dei figli
		$children = $tree->getChildren($this->oldID , null, null, false, 1, 10000000) ;
		
		// crea le nuove associazioni
		for ($i=0; $i < count($children["items"]) ; $i++) {
			$item = $children["items"][$i] ;
			
			switch($item['object_type_id']) {
				case $conf->objectTypes['section']["id"]:
				case $conf->objectTypes['community']["id"]: {
					$className	= $conf->objectTypes[$item['object_type_id']]["model"] ;
					
					$tmp = new $className() ;
					$tmp->id = $item['id'] ;
					
					$clone = clone $tmp ; 
					$tree->move($this->id, $this->oldID , $clone->id) ;
				}  break ;
				default: {
					$tree->appendChild($item['id'], $this->id) ;
				}
			}
		}
	}
    
	public function feedsAvailable() {
        $this->containLevel("minimum");
        $feeds = $this->find('all', array(
                'conditions' => array('Section.syndicate' => 'on', 'BEObject.status' => 'on'), 
                'fields' => array('BEObject.nickname')));
        $feedUrls = array();
        foreach ($feeds as $f) {
        	$feedUrls[] = "/rss/" . $f['BEObject']['nickname'];	
        }
        return $feedUrls;
    }
    
	
}
?>
