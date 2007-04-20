<?php 
/**
 * Classe modello vista elenco breve eventi.
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

class ViewShortEvent extends BEViewContentModel
{
	var $name = 'ViewShortEvent';

	protected function __setupJoinGroups() { 
		$this->__joinFragment = " INNER JOIN areas_contents_groups AS ACG ON ViewShortEvent.id = ACG.content_id" ; 
	}

	protected function __setupJoinAreas() { 
//		$this->__joinFragment = "INNER JOIN (areas_contents_groups AS ACG INNER JOIN areas_contents_groups AS ACG2 ON ACG.area_id = ACG2.area_id AND ACG2.group_id IS NOT NULL) ON ViewShortEvents.id = ACG.content_id OR ViewShortEvents.id = ACG2.content_id" ; 
		$this->__joinFragment = " INNER JOIN view_areas_contents AS ACG ON ViewShortEvent.id = ACG.content_id" ; 
	}
}

?>
