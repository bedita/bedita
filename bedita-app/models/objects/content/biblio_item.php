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
 * 		
 * 						
*/
class BiblioItem extends BeditaContentModel
{
	var $validate = array(
		'bibliography_id'	=> array(array('rule' => VALID_NUMBER, 		'required' => true)),
	) ;

	/**
	 * Associa il commento creato/modificato all'oggetto commentato
	 */
	function afterSave($created) {
		if (!$created) return ;
		
		if(!class_exists('Bibliography')) loadModel('Bibliography') ;

		$biblio = new Bibliography() ;
		
		$biblio->appendChild($this->{$this->primaryKey} , $this->data[$this->name]['bibliography_id']) ;
	}
	
	/**
	 * Formatta i dati per la creazione di un clone, ogni tipo
	 * di oggetto esegue operazioni specifiche richiamando, sempre
	 * parent::_formatDataForClone.
	 *
	 * @param array $data		Dati da formattare
	 * @param object $source	Oggetto sorgente
	 */
	protected function _formatDataForClone(&$data, $source = null) {
		parent::_formatDataForClone($data);
		
		if(isset($source->bibliography_id)) {
			$data['bibliography_id'] = $source->bibliography_id ;
		}
	}	
}
?>
