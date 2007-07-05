<?php

class TestsControllerTest extends CakeTestCase { 
	
	function startCase() {
		echo '<h1>Starting Test Case</h1>';
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

	function testIndex() {
		$result = $this->testAction('/tests/index', array('return' => 'vars'));
		debug($result);
	}
	
	function testIndexRenderedHtml() {
		$result = $this->testAction('/tests/index', array('return' => 'render'));
		debug(htmlentities($result));
	} 
	
	function testIndexShowView() {
		$result = $this->testAction('/tests/index', array('return' => 'render'));
		e($result);
	}
	
	function testIndexSaveFixturized() {
		$data = array("Collection" => array(
										"create_rules" 	=> "test"
									)
				);
		$result = $this->testAction('/tests/index',	array('fixturize' => array('Collection'), 'data' => $data, 'method' => 'post'));
		debug($result);
		$this->assertEqual($result,true);
	} 

}

?>

