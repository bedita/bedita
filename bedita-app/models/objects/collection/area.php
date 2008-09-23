<?php
/**
 *
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
class Area extends BeditaCollectionModel
{

		public $searchFields = array("title" => 10 , "description" => 6, 
			"public_name" => 10, "public_url" => 8);
	
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
	
	function afterSave($created) {
		if (!$created) 
			return ;
		
		$tree = ClassRegistry::init('Tree', 'Model');
		$tree->appendChild($this->id, null) ;		
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
	

}
?>
