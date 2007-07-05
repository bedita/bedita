<?php
/**
 * Modulo Test.
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
 * 
 * @author giangi@qwerg.com
 * 
 */
class TestsController extends AppController {
	var $helpers 	= array('Bevalidation');
	var $uses	 	= array('BEObject', 'ContentBase', 'ViewImage', 'Content', 'BaseDocument', 'Document');
//	var $uses	 	= array('BEObject', 'Collection', 'Area', 'Newsletter', 'Section', 'Community');	
	var $name 		= 'Tests' ;


	function index() {
		$this->Area->bviorHideFields 		= array('Index', 'ObjectType', 'Version') ;
		$this->Area->bviorCompactResults 	= true ;
		$result = $this->Area->findById(2);
/*
foreach ($result as $k => $v) {
	unset($v['id']);
	$result[$k] = $v ;
}
*/
//unset($result['id']) ;
pr(serialize($result));
		pr(($result));
		
exit;
	}
	
	function add() {		
		$docMultipleArr = unserialize('a:5:{s:8:"Document";a:5:{s:6:"ptrURL";N;s:6:"ptrObj";N;s:7:"ptrFile";N;s:7:"ptrRule";N;s:6:"switch";s:3:"doc";}s:6:"Object";a:15:{s:14:"object_type_id";s:2:"22";s:6:"status";s:2:"on";s:7:"created";s:19:"2007-06-01 15:43:52";s:8:"modified";s:19:"2007-06-01 15:43:52";s:5:"title";s:23:"Primo Documento di Test";s:8:"nickname";s:20:"PrimoDocumentoDiTest";s:7:"current";s:1:"1";s:4:"lang";s:2:"it";s:10:"IP_created";s:11:"192.168.0.1";s:10:"ObjectType";a:2:{s:2:"id";s:2:"22";s:4:"name";s:8:"document";}s:10:"Permission";a:0:{}s:7:"Version";a:0:{}s:16:"CustomProperties";a:2:{s:4:"test";i:10;s:8:"testBool";b:1;}s:5:"Index";a:0:{}s:8:"LangText";a:0:{}}s:11:"ContentBase";a:9:{s:5:"start";s:19:"2007-05-22 10:59:28";s:3:"end";s:19:"2008-05-22 10:59:34";s:8:"subtitle";s:11:"sottotitolo";s:10:"testobreve";s:23:"Questo ? il testo breve";s:7:"formato";s:3:"txt";s:8:"langObjs";a:2:{i:0;a:4:{s:2:"id";s:1:"6";s:6:"status";s:2:"on";s:4:"lang";s:2:"en";s:5:"title";s:23:"Primo Documento di Test";}i:1;a:4:{s:2:"id";s:2:"12";s:6:"status";s:2:"on";s:4:"lang";s:2:"fr";s:5:"title";s:23:"Primo Documento di Test";}}s:6:"images";a:1:{i:0;a:7:{s:2:"id";s:1:"7";s:4:"path";s:14:"/test/test.jpg";s:4:"name";s:8:"test.jpg";s:4:"type";s:10:"image/jpeg";s:4:"size";s:5:"34564";s:5:"title";s:16:"Immagine di test";s:6:"status";s:2:"on";}}s:10:"multimedia";a:0:{}s:11:"attachments";a:0:{}}s:7:"Content";a:6:{s:14:"audio_video_id";N;s:8:"image_id";s:1:"7";s:10:"testoLungo";s:11:"testo lungo";s:5:"Image";a:7:{s:2:"id";s:1:"7";s:4:"path";s:14:"/test/test.jpg";s:4:"name";s:8:"test.jpg";s:4:"type";s:10:"image/jpeg";s:4:"size";s:5:"34564";s:5:"title";s:16:"Immagine di test";s:6:"status";s:2:"on";}s:10:"AudioVideo";a:0:{}s:10:"categories";a:2:{i:0;a:2:{s:2:"id";s:1:"1";s:5:"label";s:17:"Categoria Doc # 1";}i:1;a:2:{s:2:"id";s:1:"2";s:5:"label";s:17:"Categoria Doc # 2";}}}s:12:"BaseDocument";a:5:{s:11:"desc_author";s:18:"descrizione autore";s:12:"flagComments";s:1:"1";s:7:"credits";s:7:"credits";s:5:"links";a:2:{i:0;a:11:{s:2:"id";s:1:"3";s:9:"object_id";s:1:"5";s:6:"switch";s:3:"url";s:3:"url";s:19:"htt://www.qwerg.com";s:7:"coord_1";N;s:7:"coord_2";N;s:7:"coord_3";N;s:10:"strCoord_1";N;s:10:"strCoord_2";N;s:10:"strCoord_3";N;s:9:"googleRef";N;}i:1;a:11:{s:2:"id";s:1:"4";s:9:"object_id";s:1:"5";s:6:"switch";s:3:"url";s:3:"url";s:20:"htt://www.chialab.it";s:7:"coord_1";N;s:7:"coord_2";N;s:7:"coord_3";N;s:10:"strCoord_1";N;s:10:"strCoord_2";N;s:10:"strCoord_3";N;s:9:"googleRef";N;}}s:8:"comments";a:2:{i:0;a:2:{s:2:"id";s:2:"13";s:6:"status";s:2:"on";}i:1;a:2:{s:2:"id";s:2:"14";s:6:"status";s:2:"on";}}}}') ;
		$docSingleArr	= unserialize('a:40:{s:6:"ptrURL";N;s:6:"ptrObj";N;s:7:"ptrFile";N;s:7:"ptrRule";N;s:6:"switch";s:3:"doc";s:14:"object_type_id";s:2:"22";s:6:"status";s:2:"on";s:7:"created";s:19:"2007-06-01 15:43:52";s:8:"modified";s:19:"2007-06-01 15:43:52";s:5:"title";s:23:"Primo Documento di Test";s:8:"nickname";s:20:"PrimoDocumentoDiTest";s:7:"current";s:1:"1";s:4:"lang";s:2:"it";s:10:"IP_created";s:11:"192.168.0.1";s:10:"ObjectType";a:2:{s:2:"id";s:2:"22";s:4:"name";s:8:"document";}s:10:"Permission";a:0:{}s:7:"Version";a:0:{}s:16:"CustomProperties";a:2:{s:4:"test";i:10;s:8:"testBool";b:1;}s:5:"Index";a:0:{}s:8:"LangText";a:0:{}s:5:"start";s:19:"2007-05-22 10:59:28";s:3:"end";s:19:"2008-05-22 10:59:34";s:8:"subtitle";s:11:"sottotitolo";s:10:"testobreve";s:23:"Questo ? il testo breve";s:7:"formato";s:3:"txt";s:8:"langObjs";a:2:{i:0;a:4:{s:2:"id";s:1:"6";s:6:"status";s:2:"on";s:4:"lang";s:2:"en";s:5:"title";s:23:"Primo Documento di Test";}i:1;a:4:{s:2:"id";s:2:"12";s:6:"status";s:2:"on";s:4:"lang";s:2:"fr";s:5:"title";s:23:"Primo Documento di Test";}}s:6:"images";a:1:{i:0;a:7:{s:2:"id";s:1:"7";s:4:"path";s:14:"/test/test.jpg";s:4:"name";s:8:"test.jpg";s:4:"type";s:10:"image/jpeg";s:4:"size";s:5:"34564";s:5:"title";s:16:"Immagine di test";s:6:"status";s:2:"on";}}s:10:"multimedia";a:0:{}s:11:"attachments";a:0:{}s:14:"audio_video_id";N;s:8:"image_id";s:1:"7";s:10:"testoLungo";s:11:"testo lungo";s:5:"Image";a:7:{s:2:"id";s:1:"7";s:4:"path";s:14:"/test/test.jpg";s:4:"name";s:8:"test.jpg";s:4:"type";s:10:"image/jpeg";s:4:"size";s:5:"34564";s:5:"title";s:16:"Immagine di test";s:6:"status";s:2:"on";}s:10:"AudioVideo";a:0:{}s:10:"categories";a:2:{i:0;a:2:{s:2:"id";s:1:"1";s:5:"label";s:17:"Categoria Doc # 1";}i:1;a:2:{s:2:"id";s:1:"2";s:5:"label";s:17:"Categoria Doc # 2";}}s:11:"desc_author";s:18:"descrizione autore";s:12:"flagComments";s:1:"1";s:7:"credits";s:7:"credits";s:5:"links";a:0:{}s:8:"comments";a:2:{i:0;a:2:{s:2:"id";s:2:"13";s:6:"status";s:2:"on";}i:1;a:2:{s:2:"id";s:2:"14";s:6:"status";s:2:"on";}}}') ;
		
		$this->data = $docMultipleArr ;
//pr($docMultipleArr);
		if(!$this->Document->save($this->data)) {
			$this->Session->setFlash("Errore creazione/salvataggio");
		}
		
		// Carica l'oggetto creato
		$this->Document->bviorCompactResults = true ;
		$result = $this->Document->findById($this->Document->id);
pr($result);
exit;
	
	}

	function delete($id) {
		// salva i dati
		if(!$this->Document->del($id)) {
			$this->Session->setFlash("Errore nella cancellazione dell'utente: $id");
			$this->redirect($this->data["back"]["ERROR"]) ;
			
			return ;
		}	
		
		exit ;	
	}
	
}

?>