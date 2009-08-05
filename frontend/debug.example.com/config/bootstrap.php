<?php

// @todo: SmartyView only....
$viewPaths=array(BEDITA_CORE_PATH . DS . 'views' . DS);

$componentPaths = array(BEDITA_CORE_PATH . DS . 'controllers' . DS . 'components' . DS);
$behaviorPaths = array(BEDITA_CORE_PATH . DS . 'models' . DS . 'behaviors' . DS);
$helperPaths = array(BEDITA_CORE_PATH . DS . 'views' . DS . 'helpers' . DS);

// all core models
$modelPaths = array();

function enableSubFoldersOn($baseDir, &$var) {         
  $cwd =getcwd();
  chdir($baseDir);
  $dirs = glob("*", GLOB_ONLYDIR);  
  if(sizeof($dirs) > 0) { 
    foreach($dirs as $dir) { 
      $var[] = $baseDir.DS.$dir.DS;
       enableSubFoldersOn($baseDir.DS.$dir, $var) ;
    }
  }
  chdir($cwd);
}


enableSubFoldersOn(BEDITA_CORE_PATH . DS . 'models', $modelPaths); 
$modelPaths[]=BEDITA_CORE_PATH . DS . 'models' . DS;

Configure::load("frontend.ini") ;

?>