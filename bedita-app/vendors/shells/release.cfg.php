<?php

$rel["releaseBaseName"]="BEdita";
$rel["releaseCodeName"] = "popolus";

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
	"bedita-app".DS."webroot".DS."js".DS."tiny_mce",
	"bedita-app".DS."webroot".DS."js".DS."ckeditor".DS."_source",
	"bedita-app".DS."webroot".DS."js".DS."ckeditor".DS."_samples",
	"bedita-db",
	"cake".DS."tests".DS."cases",
	"cake".DS."tests".DS."fixtures",
	"cake".DS."tests".DS."groups",
	"cake".DS."tests".DS."test_app",
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
	"addons" . DS . "components",
	"addons" . DS . "components" . DS . "enabled",
	"addons" . DS . "config",
	"addons" . DS . "helpers",
	"addons" . DS . "helpers" . DS . "enabled",
	"addons" . DS . "models",
	"addons" . DS . "models" . DS . "enabled",
	"addons" . DS . "models" . DS . "behaviors",
	"addons" . DS . "models" . DS . "behaviors" . DS . "enabled",
	"addons" . DS . "vendors",
);

$rel["renameFiles"]= array(
	"bedita-app".DS."webroot".DS."index.php.sample" => "bedita-app".DS."webroot".DS."index.php",
	"examples".DS."debug.example.com".DS."config".DS."core.php.sample" => "examples".DS."debug.example.com".DS."config".DS."core.php",
	"examples".DS."site.example.com".DS."config".DS."core.php.sample" => "examples".DS."site.example.com".DS."config".DS."core.php",
	"examples".DS."dummy.example.com".DS."config".DS."core.php.sample" => "examples".DS."dummy.example.com".DS."config".DS."core.php",
	"examples".DS."html5.example.com".DS."config".DS."core.php.sample" => "examples".DS."html5.example.com".DS."config".DS."core.php",
	"examples".DS."wp.example.com".DS."config".DS."core.php.sample" => "examples".DS."wp.example.com".DS."config".DS."core.php"
);

$rel["createFiles"] = array();

$rel["moveDirs"]= array(
	"examples" => "frontends",
	"bedita-app". DS . "plugins" . DS . "sample_module" => "modules" . DS . "sample_module",
	"bedita-app". DS . "plugins" . DS . "addons" => "addons",
);

$rel["versionFileName"]="bedita-app".DS."config".DS."bedita.version.php";

?>