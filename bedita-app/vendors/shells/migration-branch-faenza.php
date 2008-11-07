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
class Migration extends MigrationBase {
	
var $methodsQueries = array(
	
	"documents" => "select ob.*, cb.*, c.*, bd.*, d.* 
				from objects ob, content_bases cb, contents c, base_documents bd, documents d 
			where cb.id=ob.id AND c.id=ob.id 
			AND bd.id=ob.id  AND d.id=ob.id 
			AND ob.object_type_id=22",
	"shortNews" => "select ob.*, cb.*, s.* from objects ob, content_bases cb, short_news s 
			where cb.id=ob.id AND s.id=ob.id AND ob.object_type_id=18",
	"objectRelations" => "select cbo.* from content_bases_objects cbo",
	"categories" => "select oc.* from object_categories oc",
	"contentObjectCategories" => "select cboc.* from content_bases_object_categories cboc",
	"areas" => "select ob.*, co.*, a.* from objects ob, collections co, areas a 
			where co.id=ob.id AND a.id=ob.id AND ob.object_type_id=1",
	"sections" => "select ob.*, co.* from objects ob, collections co
			where co.id=ob.id  AND ob.object_type_id=3",
	"images" => "select ob.*, cb.*, st.*, im.* from objects ob, content_bases cb, streams st, images im 
			where cb.id=ob.id AND st.id=ob.id AND im.id=ob.id AND ob.object_type_id=12",
	"videos" => "select ob.*, cb.*, st.*, v.* from objects ob, content_bases cb, streams st, video v 
			where cb.id=ob.id AND st.id=ob.id AND v.id=ob.id AND ob.object_type_id=32",
	"files" => "select ob.*, cb.*, st.*, f.* from objects ob, content_bases cb, streams st, files f 
			where cb.id=ob.id AND st.id=ob.id AND f.id=ob.id AND ob.object_type_id=10",
	"modules" => "select mo.* from modules mo",
	"langTexts" => "select lt.* from lang_texts lt",
	"copy" => "event_logs groups groups_users lang_texts object_types permissions permission_modules question_types trees users"
);

	
	public function createExport() {
		$this->write("SET FOREIGN_KEY_CHECKS=0;\n");
		$this->createExportFromArray($this->methodsQueries);
		$this->close();
	}
	
	protected function documents($r) {
		unset($r['ob']['fundo']);		
		$this->write($this->createInsert($r['ob'], "objects"));
		$contents = array_merge($r['cb'], $r['c']);
		$contents = array_merge($contents, $r['bd']);
		$this->write($this->createInsert($contents, "contents"));
	}
	
	protected function shortNews($r) {
		unset($r['ob']['fundo']);		
		$this->write($this->createInsert($r['ob'], "objects"));
		$this->write($this->createInsert($r['cb'], "contents"));
	}

	protected function areas($r) {
		$r['ob']['creator'] = $r['a']['creator'];
		$r['ob']['publisher'] = $r['a']['publisher'];
		unset($r['ob']['fundo']);		
		$this->write($this->createInsert($r['ob'], "objects"));
		unset($r['a']['creator']);
		unset($r['a']['publisher']);
		$this->write($this->createInsert($r['a'], "areas"));
	}

	protected function sections($r) {
		unset($r['ob']['fundo']);		
		$this->write($this->createInsert($r['ob'], "objects"));
		$this->write($this->createInsert(array('id' => $r['co']['id']), "sections"));
	}

	protected function images($r) {
		unset($r['ob']['fundo']);		
		$this->write($this->createInsert($r['ob'], "objects"));
		$this->write($this->createInsert($r['cb'], "contents"));
		$r['st']['mime_type'] = $r['st']['type'];
		unset($r['st']['type']);		
		$this->write($this->createInsert($r['st'], "streams"));
		$this->write($this->createInsert($r['im'], "images"));
	}

	protected function videos($r) {
		unset($r['ob']['fundo']);		
		$this->write($this->createInsert($r['ob'], "objects"));
		$this->write($this->createInsert($r['cb'], "contents"));
		$r['st']['mime_type'] = $r['st']['type'];
		unset($r['st']['type']);		
		$this->write($this->createInsert($r['st'], "streams"));
		$this->write($this->createInsert($r['v'], "videos"));
	}

	protected function files($r) {
		unset($r['ob']['fundo']);		
		$this->write($this->createInsert($r['ob'], "objects"));
		$this->write($this->createInsert($r['cb'], "contents"));
		$r['st']['mime_type'] = $r['st']['type'];
		unset($r['st']['type']);		
		$this->write($this->createInsert($r['st'], "streams"));
	}
	
	protected function objectRelations($r) {
		$this->write($this->createInsert($r['cbo'], "object_relations"));
	}
	
	protected function categories($r) {
		$this->write($this->createInsert($r['oc'], "categories"));
	}
	
	protected function contentObjectCategories($r) {
		$r['cboc']['object_id'] = $r['cboc']['content_base_id'];
		unset($r['cboc']['content_base_id']);
		$r['cboc']['category_id'] = $r['cboc']['object_category_id'];
		unset($r['cboc']['object_category_id']);
		$this->write($this->createInsert($r['cboc'], "object_categories"));
	}
	
	protected function modules($r) {
		$r['mo']['name'] = $r['mo']['label'];
		unset($r['mo']['color']);
		$this->write($this->createInsert($r['mo'], "modules"));
	}

	protected function langTexts($r) {
		if(empty($r['lt']['text'])) {
			$r['lt']['text'] = $r['lt']['long_text'];
		}
		unset($r['lt']['long_text']);		
		$this->write($this->createInsert($r['lt'], "objects"));
	}
	
};

?>