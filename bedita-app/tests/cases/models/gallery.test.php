<?php
/**
 *
 * @author d.didomenico@channelweb.it
 *
 * Gallery tests
 *
 */

include_once(dirname(__FILE__) . DS . 'gallery.data.php') ;
loadComponent('Permission');
loadModel('Gallery');

class GalleryTestCase extends CakeTestCase {

	var $fixtures 	= array();
	var $user		= null;
	var $dataSource = 'default' ;
	var $data = null ;

	////////////////////////////////////////////////////////////////////

	function testInsertGalleries() {

		$nGalleries = 30; // number of galleries to insert
		$perms = array(
			array('bedita', 'user', (BEDITA_PERMS_CREATE | BEDITA_PERMS_DELETE | BEDITA_PERMS_MODIFY | BEDITA_PERMS_READ) )
		);
		for($i=1;$i<$nGalleries+1;$i++) {
			$model =& new Gallery();
			$this->{'Gallery'} =& $model;
			$permission = new PermissionComponent();
			$this->data['gallery']['title'] = "Gallery $i";
			$result = $this->Gallery->save($this->data['gallery']);
			$ret = $permission->add($this->Gallery->getLastInsertId(), $perms);
		}
	}

	/////////////////////////////////////////////////
	/////////////////////////////////////////////////

	function startCase() {
		echo '<h1>Bedita Gallery Test</h1>';
	}

	function endCase() {
		echo '<h1>Ending Test Case</h1>';
	}

	function startTest($method) {
		echo '<h3>Starting method ' . $method . '</h3>';
	}

	function endTest($method) {
		echo '<hr />';
	}

	public   function __construct () {
		parent::__construct() ;
		$permission 	= &new PermissionComponent() ;
		$model =& new Gallery();
		$this->modelNames[] = 'Gallery';
		$this->{'Gallery'} =& $model;
		$GalleryData = &new GalleryData() ;
		$this->data = $GalleryData->getData() ;
	}
}
?>