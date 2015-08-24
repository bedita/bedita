<?php

$rel["releaseBaseName"]="BEdita";
$rel["releaseCodeName"] = "corylus";

$rel["removeFiles"]= array(
    "bedita-app".DS."vendors".DS."shells".DS."deploy.php",
    "bedita-app".DS."vendors".DS."shells".DS."release.cfg.php",
    'frontends' . DS . 'empty',
);

$rel["removeDirs"]= array(
    "bedita-deploy",
    "bedita-doc".DS."api".DS."html",
    "bedita-doc".DS."model",
    "bedita-doc".DS."view",
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

$rel['renameFiles'] = array(
    'frontends' . DS . 'boilerplate' . DS . 'config' . DS . 'core.php.sample' => 'frontends' . DS . 'boilerplate' . DS . 'config' . DS . 'core.php',
    'frontends' . DS . 'boilerplate' . DS . 'config' . DS . 'frontend.cfg.php.sample' => 'frontends' . DS . 'boilerplate' . DS . 'config' . DS . 'frontend.cfg.php',
    'frontends' . DS . 'boilerplate' . DS . 'config' . DS . 'paths.php.sample' => 'frontends' . DS . 'boilerplate' . DS . 'config' . DS . 'paths.php',

    'frontends' . DS . 'bootstrap' . DS . 'config' . DS . 'core.php.sample' => 'frontends' . DS . 'bootstrap' . DS . 'config' . DS . 'core.php',
    'frontends' . DS . 'bootstrap' . DS . 'config' . DS . 'frontend.cfg.php.sample' => 'frontends' . DS . 'bootstrap' . DS . 'config' . DS . 'frontend.cfg.php',
    'frontends' . DS . 'bootstrap' . DS . 'config' . DS . 'paths.php.sample' => 'frontends' . DS . 'bootstrap' . DS . 'config' . DS . 'paths.php',

    'frontends' . DS . 'responsive' . DS . 'config' . DS . 'core.php.sample' => 'frontends' . DS . 'responsive' . DS . 'config' . DS . 'core.php',
    'frontends' . DS . 'responsive' . DS . 'config' . DS . 'frontend.cfg.php.sample' => 'frontends' . DS . 'responsive' . DS . 'config' . DS . 'frontend.cfg.php',
    'frontends' . DS . 'responsive' . DS . 'config' . DS . 'paths.php.sample' => 'frontends' . DS . 'responsive' . DS . 'config' . DS . 'paths.php',

    'frontends' . DS . 'web-starter-kit' . DS . 'config' . DS . 'core.php.sample' => 'frontends' . DS . 'web-starter-kit' . DS . 'config' . DS . 'core.php',
    'frontends' . DS . 'web-starter-kit' . DS . 'config' . DS . 'frontend.cfg.php.sample' => 'frontends' . DS . 'web-starter-kit' . DS . 'config' . DS . 'frontend.cfg.php',
    'frontends' . DS . 'web-starter-kit' . DS . 'config' . DS . 'paths.php.sample' => 'frontends' . DS . 'web-starter-kit' . DS . 'config' . DS . 'paths.php',
);

$rel["createFiles"] = array();

$rel["moveDirs"] = array();

$rel['frontends'] = array(
    'boilerplate' => 'https://github.com/bedita/boilerplate.git',
    'bootstrap' => 'https://github.com/bedita/bootstrap.git',
    'responsive' => 'https://github.com/bedita/responsive.git',
    'web-starter-kit' => 'https://github.com/bedita/web-starter-kit.git',
);

$rel["versionFileName"] = "bedita-app" . DS . "config" . DS . "bedita.version.php";
