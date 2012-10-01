<?php

require_once APP. DS. 'vendors' . DS . 'shells'. DS . 'bedita_base.php';

class TestShell extends BeditaBaseShell {

	function help() {
		$this->out('Available functions:');
        $this->out('1. test [-test <test>]');
  		$this->out(' ');
  		$this->out("    -test \t test test test");
  		$this->out(' ');
	}
	
}

?>