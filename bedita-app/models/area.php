<?php
/**
 *
 * PHP versions 5 
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

class Area extends BEAppModel
{
	var $name = 'Area';
	
	/**
	 * costanti che definiscono il tipo di gruppi da scegliere
	 */
	const SUBJECT 	= 1 ;
	const TIPOLOGY 	= 2 ;
	const CATEGORY 	= 3 ;
	const SECTION 	= 4 ;
	
	
    var $hasAndBelongsToMany = array('Group' =>
                               array('className'    => 'Group',
                                     'foreignKey'	=> 'area_id',
									 'joinTable'	=> 'areas_contents_groups'
                               )
    );
    
    //var $hasMany = array('ViewSubject');
	
	
	function __construct() {
		parent::__construct() ;
	}

	/**
	 * Torna l'elenco delle aree con i gruppi connessi nell'array Groups utilizzando join con una vista
	 *
	 * @param mixed	$tree	Dove tornare il risultato
	 * @param integer $type	Tipologia del gruppo da selezionare
	 * @param integer $id	ID dell'area da espandere. 0: nessuna; FF: tutte;integer un'area
	 * 
	 * @todo TUTTO
	 */
	
	function tree($type = Area::SUBJECT, $id = 0) {
		
		switch ($type) {
			case Area::SUBJECT:
				$SQLview = 'ViewSubject';
				break;
			case Area::TIPOLOGY:
				$SQLview = 'ViewTipology';
				break;
			case Area::CATEGORY:
				$SQLview = 'ViewCategory';
				break;
			case Area::SECTION:
				$SQLview = 'ViewSection';
				break;
			default:
				$SQLview = 'ViewSubject';
				break;
		}
		
		$this->unbindModel(array('hasAndBelongsToMany' => array('Group')));
		$this->bindModel(array('hasMany' => array(
                							'Groups' => array('className' => $SQLview)
            							)
        				));
        				
        return $this->findAll();
		
	}


}
?>
