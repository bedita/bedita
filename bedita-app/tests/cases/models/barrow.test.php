<?php
/**
 *
 * @author xho@channelweb.it
 *
 * Barrow tests
 *
 */

loadComponent('Permission');
//loadModel('Document');
loadModel('Area');
//loadModel('Gallery');
//loadModel('Image');

class BarrowTestCase extends CakeTestCase {

	var $fixtures 	= array();
	var $user		= null;
	var $dataSource = 'default' ;
	var $data = array(
		'insert' => array(
			// AREE
			'area'	=> array(
				'barrowuomo'	=> array(
					'title' => 'Barrow Uomo',
					'shortDesc' => 'desc breve',
					'LangText'	=> array(
							array('lang' => 'en', 'name' => 'title', 'text' => 'Barrow Man'),
							array('lang' => 'fr', 'name' => 'title', 'text' => 'Barrow Homme'),
					)
				),
				'barrowdonna'	=> array(
					'title' => 'Barrow Donna',
					'shortDesc' => 'desc asdhaskj hds',
					'LangText'	=> array(
							array('lang' => 'en', 'name' => 'title', 'text' => 'Barrow Woman'),
							array('lang' => 'fr', 'name' => 'title', 'text' => 'Barrow Homme'),
					)
				),
				'bibasuomo'	=> array(
					'title' => 'Bibas Uomo',
					'shortDesc' => 'desc breve',
					'LangText'	=> array(
							array('lang' => 'en', 'name' => 'title', 'text' => 'Bibas Man'),
							array('lang' => 'fr', 'name' => 'title', 'text' => 'Barrow Homme'),
					)
				)
			)
		)
	);

	////////////////////////////////////////////////////////////////////

	function testInserisciAree() {
		$perms = array(
			array('bedita', 'user', (BEDITA_PERMS_CREATE | BEDITA_PERMS_DELETE | BEDITA_PERMS_MODIFY | BEDITA_PERMS_READ) )
		);
		$model =& new Area();
		$this->{'Area'} =& $model;
		$result = $this->Area->save($this->data['insert']['area']['barrowuomo']) ;
		$permission = new PermissionComponent();
		$ret = $permission->add($this->Area->getLastInsertId(), $perms);
		
		$model =& new Area();
		$this->{'Area'} =& $model;
		$result = $this->Area->save($this->data['insert']['area']['barrowdonna']) ;
		$ret = $permission->add($this->Area->getLastInsertId(), $perms);
		
		$model =& new Area();
		$this->{'Area'} =& $model;
		$result = $this->Area->save($this->data['insert']['area']['bibasuomo']) ;
		$ret = $permission->add($this->Area->getLastInsertId(), $perms);
	}
	
//	function testInsertGalleries() {
//
//		$nGalleries = 0; // number of galleries to insert
//		$perms = array(
//			array('bedita', 'user', (BEDITA_PERMS_CREATE | BEDITA_PERMS_DELETE | BEDITA_PERMS_MODIFY | BEDITA_PERMS_READ) )
//		);
//		for($i=1;$i<$nGalleries+1;$i++) {
//			$model =& new Gallery();
//			$this->{'Gallery'} =& $model;
//			$permission = new PermissionComponent();
//			$this->data['gallery']['title'] = "Gallery $i";
//			$result = $this->Gallery->save($this->data['gallery']);
//			$ret = $permission->add($this->Gallery->getLastInsertId(), $perms);
//		}
//	}
//
//	function testInsertImagesForGallery() {
//		$perms = array(
//			array('bedita', 'user', (BEDITA_PERMS_CREATE | BEDITA_PERMS_DELETE | BEDITA_PERMS_MODIFY | BEDITA_PERMS_READ) )
//		);
//
//		$model =& new Gallery();
//		$this->{'Gallery'} =& $model;
//		$permission = new PermissionComponent();
//		$this->data['gallery']['title'] = "Il Circolo Tognolo";
//		$result = $this->Gallery->save($this->data['gallery']);
//		$ret = $permission->add($this->Gallery->getLastInsertId(), $perms);
//		
//		$image =& new Image();
//		$this->{'Image'} =& $image;
//		$permission = new PermissionComponent();
//		$this->data['file']= array(
//			'title' 	=> 'Lo scimparpente',
//			'path'		=> 'img/scimparpente.jpg',
//			'shortDesc' => 'Lo scimparpente e\' davvero tremendo',
//			'long_desc'  => 'Lo scimparpente e\' davvero tremendo tremendissimo, ne fa di tutti i colori porca troia... minchia lo scimparpente!',
//			'width'     => '350',
//			'height'    => '235',
//			'name'		=> 'scimparpente',
//			'type'		=> 'ascii/img',
//			'size'		=> 75
//		);
//		$result = $this->Image->save($this->data['file']);
//		$ret = $permission->add($this->Image->getLastInsertId(), $perms);
//		$this->Gallery->appendChild($this->Image->getLastInsertId(),null,1);
//		
//		$image =& new Image();
//		$this->{'Image'} =& $image;
//		$permission = new PermissionComponent();
//		$this->data['file']= array(
//			'title' 	=> 'La mosca cavallina',
//			'path'		=> 'img/moscacavallina.jpg',
//			'shortDesc' => 'La mosca cavallina ronza dappertutto al galoppo',
//			'long_desc'  => 'Ronza dappertutto al galoppo, e non si ferma mai, proprio per questo \'e tanto pericolosa!',
//			'width'     => '800',
//			'height'    => '600',
//			'name'		=> 'moscacavallina',
//			'type'		=> 'ascii/img',
//			'size'		=> 205
//		);
//		$result = $this->Image->save($this->data['file']);
//		$ret = $permission->add($this->Image->getLastInsertId(), $perms);
//		$this->Gallery->appendChild($this->Image->getLastInsertId(),null,2);
//
//		$image =& new Image();
//		$this->{'Image'} =& $image;
//		$permission = new PermissionComponent();
//		$this->data['file']= array(
//			'title' 	=> 'Il leorpente',
//			'path'		=> 'img/leorpente.jpg',
//			'shortDesc' => 'Il leorpente e\' il serpente piu\' velenoso tra i felini...',
//			'long_desc'  => 'Velenoso re della foresta! Il leorpente caccia le sue prede nella savana, un po\' strisciando, un po\' correndo.',
//			'width'     => '969',
//			'height'    => '673',
//			'name'		=> 'leorpente',
//			'type'		=> 'ascii/img',
//			'size'		=> 572
//		);
//		$result = $this->Image->save($this->data['file']);
//		$ret = $permission->add($this->Image->getLastInsertId(), $perms);
//		$this->Gallery->appendChild($this->Image->getLastInsertId(),null,3);
//		
//		$image =& new Image();
//		$this->{'Image'} =& $image;
//		$permission = new PermissionComponent();
//		$this->data['file']= array(
//			'title' 	=> 'L\'ippotigre',
//			'path'		=> 'img/ippotigre.jpg',
//			'shortDesc' => 'L\'ippotigre - uno su un milione di abitanti',
//			'long_desc'  => 'Uno su un milione di abitanti!',
//			'width'     => '800',
//			'height'    => '554',
//			'name'		=> 'ippotigre',
//			'type'		=> 'ascii/img',
//			'size'		=> 329
//		);
//		$result = $this->Image->save($this->data['file']);
//		$ret = $permission->add($this->Image->getLastInsertId(), $perms);
//		$this->Gallery->appendChild($this->Image->getLastInsertId(),null,4);
//	}

	/////////////////////////////////////////////////
	/////////////////////////////////////////////////

//	function startCase() {
//		echo '<h1>Bedita Gallery Test</h1>';
//	}
//
//	function endCase() {
//		echo '<h1>Ending Test Case</h1>';
//	}
//
//	function startTest($method) {
//		echo '<h3>Starting method ' . $method . '</h3>';
//	}
//
//	function endTest($method) {
//		echo '<hr />';
//	}
//
//	public   function __construct () {
//		parent::__construct() ;
//		$permission 	= &new PermissionComponent() ;
//		$model =& new Area();
//		$this->modelNames[] = 'Area';
//		$this->{'Area'} =& $model;
//	}
}
?>