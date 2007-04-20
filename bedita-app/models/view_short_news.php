<?php 
/**
 * Classe modello vista elenco breve dei documenti.
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

class ViewShortNews extends BEViewContentModel
{
	var $name 				= 'ViewShortNews';

	protected function __setupJoinGroups() { 
		$this->__joinFragment = "  " ; 
	}

	protected function __setupJoinAreas() { 
		$this->__joinFragment = " INNER JOIN view_areas_contents AS ACG ON ViewShortNews.id = ACG.content_id" ; 
	}
}

?>
