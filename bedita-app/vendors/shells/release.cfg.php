<?php

$rel["releaseBaseName"]="BEdita-agpl";
$rel["releaseCodeName"] = "ulmus";

$rel["removeFiles"]= array(
	"bedita-app".DS."vendors".DS."shells".DS."deploy.php",
	"bedita-app".DS."vendors".DS."shells".DS."release.cfg.php",
);

$rel["removeDirs"]= array(
	"bedita-deploy",
	"bedita-doc".DS."api".DS."html",
	"bedita-doc".DS."model",
	"bedita-doc".DS."view",
	"bedita-app".DS."webroot".DS."js".DS."wymeditor",
	"bedita-db",
	"cake".DS."tests",
);

$rel["createDirs"] = array(
	"bedita-app".DS."tmp".DS."cache".DS."models",
	"bedita-app".DS."tmp".DS."cache".DS."persistent",
	"bedita-app".DS."tmp".DS."cache".DS."views",
	"bedita-app".DS."tmp".DS."logs",
	"bedita-app".DS."tmp".DS."sessions",
	"bedita-app".DS."tmp".DS."smarty".DS."cache",
	"bedita-app".DS."tmp".DS."smarty".DS."compile",
	"modules",
	"addons",
);

$rel["renameFiles"]= array(
	"bedita-app".DS."config".DS."core.php.sample" => "bedita-app".DS."config".DS."core.php",
	"bedita-app".DS."config".DS."database.php.sample" => "bedita-app".DS."config".DS."database.php",
	"bedita-app".DS."config".DS."bedita.cfg.php.sample" => "bedita-app".DS."config".DS."bedita.cfg.php",
	"bedita-app".DS."config".DS."bedita.sys.php.sample" => "bedita-app".DS."config".DS."bedita.sys.php",
	"bedita-app".DS."webroot".DS."index.php.sample" => "bedita-app".DS."webroot".DS."index.php",
	"examples".DS."debug.example.com".DS."config".DS."core.php.sample" => "examples".DS."debug.example.com".DS."config".DS."core.php",
	"examples".DS."site.example.com".DS."config".DS."core.php.sample" => "examples".DS."site.example.com".DS."config".DS."core.php",
	"examples".DS."dummy.example.com".DS."config".DS."core.php.sample" => "examples".DS."dummy.example.com".DS."config".DS."core.php",
	"examples".DS."wp.example.com".DS."config".DS."core.php.sample" => "examples".DS."wp.example.com".DS."config".DS."core.php"
);

$rel["createFiles"] = array("bedita-app".DS."webroot".DS."test.php");

$rel["moveDirs"]= array(
	"examples" => "frontends",
	"bedita-app". DS . "plugins" . DS . "sample_module" => "modules" . DS . "sample_module",
	"bedita-app". DS . "plugins" . DS . "addons" => "addons",
);

$rel["versionFileName"]="bedita-app".DS."config".DS."bedita.version.php";

?>