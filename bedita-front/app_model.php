<?php
class AppModel extends Model{
	var $actsAs = array("Containable");
}

require_once(BEDITA_CORE_PATH . DS . 'models'. DS . 'BEAppModel.php') ;
require_once(BEDITA_CORE_PATH . DS . 'models'. DS . 'BEAppObjectModel.php') ;

?>