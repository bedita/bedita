<?php

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
	"copy" => "event_logs groups groups_users lang_texts modules object_types permissions permission_modules question_types search_texts trees users"
);

	
	public function createExport() {
		$this->write("SET FOREIGN_KEY_CHECKS=0;\n");
		$this->createExportFromArray($this->methodsQueries);
		$this->close();
	}
	
	protected function documents($r) {
			$this->write($this->createInsert($r['ob'], "objects"));
			$contents = array_merge($r['cb'], $r['c']);
			$contents = array_merge($contents, $r['bd']);
			$this->write($this->createInsert($contents, "contents"));
			$this->write($this->createInsert($r['d'], "documents"));
	}
	
	protected function shortNews($r) {
			$this->write($this->createInsert($r['ob'], "objects"));
			$this->write($this->createInsert($r['cb'], "contents"));
			$this->write($this->createInsert($r['s'], "short_news"));
	}

	protected function areas($r) {
		$r['ob']['creator'] = $r['a']['creator'];
		$r['ob']['publisher'] = $r['a']['publisher'];
		$this->write($this->createInsert($r['ob'], "objects"));
		$this->write($this->createInsert($r['co'], "collections"));
		unset($r['a']['creator']);
		unset($r['a']['publisher']);
		$this->write($this->createInsert($r['a'], "areas"));
	}

	protected function sections($r) {
			$this->write($this->createInsert($r['ob'], "objects"));
			$this->write($this->createInsert($r['co'], "collections"));
	}

	protected function images($r) {
			$this->write($this->createInsert($r['ob'], "objects"));
			$this->write($this->createInsert($r['cb'], "contents"));
			$this->write($this->createInsert($r['st'], "streams"));
			$this->write($this->createInsert($r['im'], "images"));
	}

	protected function videos($r) {
			$this->write($this->createInsert($r['ob'], "objects"));
			$this->write($this->createInsert($r['cb'], "contents"));
			$this->write($this->createInsert($r['st'], "streams"));
			$this->write($this->createInsert($r['v'], "videos"));
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
	
};

?>