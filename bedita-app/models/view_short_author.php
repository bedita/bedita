<?php 
/**
 * Classe modello vista elenco breve autori.
 * 
 * PHP versions 4 
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * @filesource
 * @copyright		Copyright (c) 2006
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
*/

class ViewShortAuthor extends BEViewContentModel
{
	var $name 				= 'ViewShortAuthor';
	var $useTable 			= "view_short_authors" ;
	var $author_features 	= null ;

	function __construct() {
		parent::__construct() ;
		$this->author_features 	= new _emptyAfterFindView ;
	}

	protected function __setupJoinGroups() { 
		$this->__joinFragment = " INNER JOIN areas_contents_groups AS ACG ON ViewShortAuthor.id = ACG.content_id" ; 
	}

	protected function __setupJoinAreas() { 
		$this->__joinFragment = " INNER JOIN view_areas_contents AS ACG ON ViewShortAuthor.id = ACG.content_id" ; 
	}
}

?>
