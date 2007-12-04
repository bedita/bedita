<?php
/**
  * @author ste@channelweb.it
 * 
 *  frontend.ini.php - constants and global variables for bedita frontend + backend overrides
 * 
 */
 
require_once(BEDITA_CORE_PATH . DS . "config" . DS . "bedita.ini.php") ;

//////////////////////////////////////////////////////
// EDITABLE STUFF         ///////////////
//////////////////////////////////////////////////////

$config['authorizedGroups'] = array('frontend');

$config['frontendUser'] =array("userid"=>"bedita");

?>