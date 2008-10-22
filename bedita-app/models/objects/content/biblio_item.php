<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
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
