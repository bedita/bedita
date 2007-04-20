<?php 
/**
 * Classe modello vista elenco breve bibliografie.
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

class ViewShortBibliography extends BEViewContentModel
{
	var $name 				= 'ViewShortBibliography';

	protected function __setupJoinGroups() { 
		$this->__joinFragment = " INNER JOIN areas_contents_groups AS ACG ON `ViewShortBibliography`.id = ACG.content_id" ; 
	}

	protected function __setupJoinAreas() { 
		$this->__joinFragment = " INNER JOIN view_areas_contents AS ACG ON `ViewShortBibliography`.id = ACG.content_id" ; 
	}
}

?>
