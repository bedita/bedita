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
class Stream extends BEAppModel
{
	var $validate = array(
		'path' 		=> array(array('rule' => VALID_NOT_EMPTY, 	'required' => true)),
		'name' 		=> array(array('rule' => VALID_NOT_EMPTY, 	'required' => true)),
		'mime_type' => array(array('rule' => VALID_NOT_EMPTY, 	'required' => true)),
	) ;

	/**
	 * Get id from filename
	 * @param string $filename
	 */
	function getIdFromFilename($filename) {
		if(!isset($filename)) return false ;
		$rec = $this->recursive ;
		$this->recursive = -1 ;
		if(!($ret = $this->findByName($filename))) return false ;
		$this->recursive = $rec ;
		if(!isset($ret['Stream']['id'])) return false ;
		return $ret['Stream']['id'] ;
	}
	
	/**
	 * search filename and title in streams
	 *
	 * @param string $text, string to search
	 * @param array $ot stream object type to search
	 * @return unknown
	 */
	public function search($text, $ot, $excluded_ids=array()) {
		$streams = array(); 
		$this->bindModel( array('hasOne' => array('BEObject' => array(
																	'className'		=> 'BEObject',
																	'conditions'   => '',
																	'foreignKey'	=> 'id',
																	'dependent'		=> true
																)
														) ) );
		 $findedStreams = $this->find("all", array(
		 								"contain" => array("BEObject" => "ObjectType"),
										"conditions" => array(
														"title LIKE '%" .$text. "%'", 
														"object_type_id" => $ot,
		 												"NOT" => array("BEObject.id" => $excluded_ids)			
		 												)
												)
										)  ; 
		if (!empty($findedStreams)) {
			foreach ($findedStreams as $stream) {
				$stream["Stream"]["filename"] = substr($stream["Stream"]["path"],strripos($stream["Stream"]["path"],"/")+1);
				$streams[] = array_merge($stream["Stream"], $stream["BEObject"]);
			}
		}
		return $streams;
	}
}
?>