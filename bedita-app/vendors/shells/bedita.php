<?php 

App::import('Model', 'DataSource');
App::import('Model', 'Stream');
App::import('Component', 'Transaction');
vendor('splitter_sql');
vendor('Tar');

class DataSourceTest extends DataSource {
	
	function executeQuery($db,$script) {
		$sql = file_get_contents($script);
		$queries = array();
		$SplitterSql = new SplitterSql() ;
		$SplitterSql->parse($queries, $sql) ;
		foreach($queries as $q) {	
			if(strlen($q)>1) {
				$res = $db->execute($q);
				if($res === false) {
					throw new Exception("Error executing query: ".$q."\n" . "db error msg: " . $db->error ."\n");
				}
			}
		}
	}
	
	function executeInsert($db,$script) {
		// split in blocks
		$blocks = $this->createChunks($script);

		// call query to avoid foreign key checks, on data insert
		$res = $db->execute("SET FOREIGN_KEY_CHECKS=0");
		
		// call parse on every block and populate $queries array
		$queries = array();
		$SplitterSql = new SplitterSql() ;
		foreach($blocks as $key => $block) {
			$SplitterSql->parse($queries, $block) ;
			// call queries (except for views creation)
			foreach($queries as $q) {	
				if(strlen($q)>1) {
					if(strpos($q,"CREATE ALGORITHM") === false) {
						//echo "executing query " . $q . "\n";
						$res = $db->execute($q);
						if($res === false) {
							throw new Exception("Error executing query: ".$q."\n");
						}
					}
				}
			}
		}
	}

	function simpleInsert($db, $sqlFileName) {
		$handle = fopen($sqlFileName, "r");
		if($handle === FALSE) 
			throw new Exception("Error opening file: ".$sqlFileName);
		$q = "";
		while(!feof($handle)) {
			$line = fgets($handle);
			if($line === FALSE && !feof($handle)) {
				throw new Exception("Error reading file line");
			}
			if(strncmp($line, "INSERT INTO ", 12) == 0) {
				if(strlen($q) > 0) {
					$res = $db->execute($q);
					if($res === false) {
						throw new Exception("Error executing query: ".$q."\n");
					}
				}
				$q="";
			}
			$q .= $line;
		}
		// last query...
		if(strlen($q) > 0) {
			$res = $db->execute($q);
			if($res === false) {
				throw new Exception("Error executing query: ".$q."\n");
			}
		}
	}
	
	function createChunks($script) {
		$chunks = array();
		$handle = fopen($script, "r");
		$data = "";
		$counter=0;$ccounter=0;
		$endchar = ");\n";
		while (!feof($handle)) {
		   $buffer = fgets($handle, 4096);
		   $data.=$buffer;
		   if($counter>500 && ( substr( $buffer, strlen( $buffer ) - strlen( $endchar ) ) == $endchar ) ) { // check if $counter > 500 and $buffer ends with );
		   		$counter=0;
				$chunks[$ccounter++]=$data;
				$data="";
		   } else {
				$counter++;
		   }
		}
		fclose($handle);
		if(empty($chunks)) {
			$chunks[0]=$data;
		}
		return $chunks;
	}
}

class DumpModel extends AppModel {
	var $useTable = "objects";
};

class DbDump {
	
	private $model = NULL;
	
	public function __construct() {
		$this->model = new DumpModel();
	}
	
	public function tableList() {
   		$tables = $this->model->query("show tables");
    	$res = array();
    	foreach ($tables as $k=>$v) {
    		$t1 = array_values($v);
    		$t2 = array_values($t1[0]);
    		if (strncasecmp($t2[0], 'view_', 5) !== 0 && 
    			strncasecmp($t2[0], 'cake_', 5) !== 0) // exclude views and cake_ util tables
    			$res[]=$t2[0] ;
    	}
    	return $res;
    }
    
    public function tableDetails($tables, $handle) {

    	fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");

    	foreach ($tables as $t) {
    		$this->model->setSource($t); 
    		$select = $this->model->find('all');
			foreach ($select as $sel) {
				$fields = "";
				$values = "";
				$count = 0;
				foreach ($sel['DumpModel'] as $k=>$v) {
					if($v !== NULL) {
						if($count > 0) {
							$fields .= ",";
							$values .= ",";
						}
						$fields .= "`$k`";
						$values .= "'".addslashes($v)."'";
						$count++;
					}
				}
				$res = "INSERT INTO $t (".$fields.") VALUES ($values);\n";
    			fwrite($handle, $res);
			}
    	}
    	return $res;
    }
	
}

class BeditaShell extends Shell {

	const DEFAULT_TAR_FILE 	= 'bedita-export.tar' ;
	const DEFAULT_ARCHIVE_FILE 	= 'bedita-export.tar.gz' ;
	
	function updateDb() {
        $dbCfg = 'default';
    	if (isset($this->params['db'])) {
            $dbCfg = $this->params['db'];
    	}
		
		if (!defined('SQL_SCRIPT_PATH')) { // cambiare opportunamente questo path
	        $this->out("SQL_SCRIPT_PATH has to be defined in ".APP_DIR."/config/database.php");
			return;
		}
    	$sqlDataDump = SQL_SCRIPT_PATH . 'bedita_init_data.sql';
    	if (isset($this->params['data'])) {
            if(file_exists(SQL_SCRIPT_PATH . $this->params['data'])) {
    			$sqlDataDump = SQL_SCRIPT_PATH .$this->params['data'];
            } else {
    			$sqlDataDump = $this->params['data'];
            	if(!file_exists($sqlDataDump)) {
	        		$this->out("data file $sqlDataDump not found");
					return;
            	}
            }
    	}
    	
    	$db = ConnectionManager::getDataSource($dbCfg);
    	$hostName = $db->config['host'];
    	$dbName = $db->config['database'];
		$this->out("Updating bedita db config: $dbCfg - [host=".$hostName.", database=".$dbName."]");
		$res = $this->in("ACHTUNG! Database $dbName will be replaced, proceed? [y/n]");
		if($res != "y") {
       		$this->out("Bye");
			return;
		}
		$this->hr();

        $transaction = new TransactionComponent($dbCfg);
		$transaction->begin();
        
        $this->DataSourceTest =& new DataSourceTest();
		$script = SQL_SCRIPT_PATH . "bedita_schema.sql";
		$this->out("Update schema from $script");
		$this->DataSourceTest->executeQuery($db,$script);

		$script = SQL_SCRIPT_PATH . "bedita_procedure.sql";
		$this->out("Create procedures from $script");
        $this->DataSourceTest->executeQuery($db,$script);
        
		if (isset($this->params['nodata'])) {
			$this->out("No data inserted");
		} else {
	        $this->out("Load data from $sqlDataDump");
			$this->DataSourceTest->executeInsert($db, $sqlDataDump);
		}
       	$this->out("$dbCfg database updated");
		$transaction->commit();
		
		if (isset($this->params['media'])) {
            $this->extractMediaZip($this->params['media']);
    	}

		$this->out("checking media files");
		$this->checkMedia();
		$this->out("bye");       
    }

    function import() {
        $dbCfg = 'default';
    	if (isset($this->params['db'])) {
            $dbCfg = $this->params['db'];
    	}
		if (!defined('SQL_SCRIPT_PATH')) { // cambiare opportunamente questo path
	        $this->out("SQL_SCRIPT_PATH has to be defined in ".APP_DIR."/config/database.php");
			return;
		}
		$this->check_sys_get_temp_dir();
		$tmpBasePath = $this->setupTempDir();
       	$this->out("Using temp dir: $tmpBasePath");
		
		$archFile = self::DEFAULT_ARCHIVE_FILE;
    	if (isset($this->params['f'])) {
            $archFile = $this->params['f'];
    	}

    	if(!file_exists($archFile)) {
    		$this->out("$archFile not found, bye");
    		return;
    	}
    	$this->out("Importing file $archFile");

  		$compress = (substr($archFile, strlen($archFile)-3) == ".gz") ? "gz" : null;
  		$tar = new Archive_Tar($archFile, $compress);
       	if($tar === FALSE) {
       		$this->out("Error opening archive $archFile!!");
       	}
       	$tar->extract($tmpBasePath);
       	
		$sqlFileName = $tmpBasePath."bedita-data.sql";
		
        $this->hr();
		$db = ConnectionManager::getDataSource($dbCfg);
    	$hostName = $db->config['host'];
    	$dbName = $db->config['database'];
		$this->out("Importing data using bedita db config: $dbCfg - [host=".$hostName.", database=".$dbName."]");
		$res = $this->in("ACHTUNG! Database $dbName will be replaced, proceed? [y/n]");
		if($res != "y") {
       		$this->out("Bye");
			return;
		}
        $this->hr();
				
        $transaction = new TransactionComponent($dbCfg);
		$transaction->begin();
        
        $this->DataSourceTest =& new DataSourceTest();
		$script = SQL_SCRIPT_PATH . "bedita_schema.sql";
		$this->out("Update schema from $script");
		$this->DataSourceTest->executeQuery($db,$script);

		$script = SQL_SCRIPT_PATH . "bedita_procedure.sql";
		$this->out("Create procedures from $script");
        $this->DataSourceTest->executeQuery($db,$script);
        
		$this->out("Load data from $sqlFileName");
        $this->DataSourceTest->simpleInsert($db, $sqlFileName);
		unlink($sqlFileName);
		$this->out("$dbCfg database updated");

		$mediaRoot = Configure::read("mediaRoot");
		
		// update media root dir
		$folder = new Folder($mediaRoot);
		$ls = $folder->ls();
		if(count($ls[0]) > 0 || count($ls[1]) > 0) {
			$res = $this->in($mediaRoot. " is not empty, remove files and folders? [y/n]");
			if($res == "y") {
       			$this->removeMediaFiles();
			} else {
				$this->out($mediaRoot. " not clean!");
			}
		}
		
		// copy files from tmp dir to media_root
		$copts=array('to'=>$mediaRoot,'from'=>$tmpBasePath.'media','mode'=>0777);
		$this->out("copying from " . $copts['from'] . " to " . $copts['to']);
		$res = $folder->copy($copts);
		$this->out("Cleaning temp dir $tmpBasePath");
		$this->cleanTempDir();
		$this->out("done");
		
		$transaction->commit();		
		$this->out("bye");
    }

    
    public function export() {
        $expFile = self::DEFAULT_ARCHIVE_FILE;
    	if (isset($this->params['f'])) {
            $expFile = $this->params['f'];
    	} else if(isset($this->params['nocompress'])) {
        	$expFile = self::DEFAULT_TAR_FILE;
    	}
    	if(file_exists($expFile)) {
			$res = $this->in("$expFile exists, overwrite? [y/n]");
			if($res == "y") {
				if(!unlink($expFile)){
					throw new Exception("Error deleting $expFile");
				}
			} else {
				$this->out("Export aborted. Bye.");
				return;
			}
		}

		$dbDump = new DbDump();
		$tables = $dbDump->tableList();
		$this->check_sys_get_temp_dir();
		$tmpBasePath = $this->setupTempDir();
		$sqlFileName = $tmpBasePath."bedita-data.sql";
		
		$this->out("Creating SQL dump....");
		$handle = fopen($sqlFileName, "w");
		if($handle === FALSE) 
			throw new Exception("Error opening file: ".$sqlFileName);
		$dbDump->tableDetails($tables, $handle);
		fclose($handle);
       	$this->out("Exporting to $expFile");
       	
       	$compress = "gz";
    	if (isset($this->params['nocompress']) || (substr($expFile, strlen($expFile)-3) != ".gz")) {
            $compress = null;
    	}
       	$tar = new Archive_Tar($expFile, $compress);
       	if($tar === FALSE) {
			throw new Exception("Error opening archive $expFile");
       	}
       	
		$contents = file_get_contents($sqlFileName);
		if(!$tar->addString("bedita-data.sql", $contents))
			throw new Exception("Error adding SQL file to archive");
		unset($contents);
       	$this->out("SQL data exported");
       	$this->out("Exporting media files");
       	
		$mediaRoot = Configure::read("mediaRoot");
       	$folder=& new Folder($mediaRoot);
        $tree= $folder->tree($mediaRoot, false);
        foreach ($tree as $files) {
            foreach ($files as $file) {
                if (!is_dir($file)) {
     				$contents = file_get_contents($file);
        			if ( $contents === false ) {
						throw new Exception("Error reading file content: $file");
       				}
					$p = substr($file, strlen($mediaRoot));	
					if(!$tar->addString("media".$p, $contents)) {
						throw new Exception("Error adding $file to tar file");
					}
//					echo "before unset ". memory_get_usage()." RAM used.\n";
					unset($contents);
//					echo 'after unset  '. memory_get_usage()." RAM used.\n";
                }
            }
        }
		$this->cleanTempDir();
        $this->out("$expFile created");
    }

	private function check_sys_get_temp_dir() {
		if ( !function_exists('sys_get_temp_dir') ) {
		    // Based on http://www.phpit.net/
		    // article/creating-zip-tar-archives-dynamically-php/2/
		    function sys_get_temp_dir()
		    {
		        // Try to get from environment variable
		        if ( !empty($_ENV['TMP']) )
		        {
		            return realpath( $_ENV['TMP'] );
		        }
		        else if ( !empty($_ENV['TMPDIR']) )
		        {
		            return realpath( $_ENV['TMPDIR'] );
		        }
		        else if ( !empty($_ENV['TEMP']) )
		        {
		            return realpath( $_ENV['TEMP'] );
		        }
		
		        // Detect by creating a temporary file
		        else
		        {
		            // Try to use system's temporary directory
		            // as random name shouldn't exist
		            $temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
		            if ( $temp_file )
		            {
		                $temp_dir = realpath( dirname($temp_file) );
		                unlink( $temp_file );
		                return $temp_dir;
		            }
		            else
		            {
		                return FALSE;
		            }
		        }
		    }
		}
	}

    private function setupTempDir() {
    	$basePath = sys_get_temp_dir().DS."bedita-export-tmp".DS;
		if(!is_dir($basePath)) {
			if(!mkdir($basePath))
				throw new Exception("Error creating temp dir: ".$basePath);
		} else {
    		$this->__clean($basePath);
		}
    	return $basePath;
    }

    private function cleanTempDir() {
    	$exportPath = sys_get_temp_dir().DS."export-tmp".DS;
    	$folder= new Folder();
    	if(!$folder->delete($exportPath)) {
			throw new Exception("Error deleting dir $exportPath");
        }
    }
	
    
    private function extractMediaZip($zipFile) {
		$zip = new ZipArchive;
		if ($zip->open($zipFile) === TRUE) {
			$zip->extractTo(Configure::read("mediaRoot"));
			$zip->close();
  			$this->out("Media files extracted");
		} else {
  			$this->out("Error media file $zipFile not found!!");
		}
    }
    
    function test() {
		pr($this->params);
		pr($this->args);
    }

	public function checkMedia() {

		$stream = new Stream();
        // check filesystem
		$this->out("checkMedia - checking filesystem");
		$mediaRoot = Configure::read("mediaRoot");
		$folder=& new Folder($mediaRoot);
        $tree= $folder->tree($mediaRoot, false);
		$mediaOk = true;
        foreach ($tree as $files) {
            foreach ($files as $file) {
                if (!is_dir($file)) {
                    $file=& new File($file);
					$p = substr($file->pwd(), strlen($mediaRoot));
					if(stripos($p, "/imgcache/") !== 0) {
						$f = $stream->findByPath($p);
						if($f === false) {
							$this->out("File $p not found on db!!");
							$mediaOk = false;
						}
					}
                }
            }
        }
        if($mediaOk) {
			$this->out("checkMedia - filesystem OK");
        }
        // check db
		$this->out("checkMedia - checking database");
        $allStream = $stream->findAll();
		$mediaOk = true;
        foreach ($allStream as $v) {
        	$p = $v['Stream']['path'];
        	// if $p is a local path check existence
        	if((stripos($p, "/") === 0) && !file_exists($mediaRoot.$p)) {
					$this->out("File $p not found on filesystem!!");
					$mediaOk = false;
        	}
        }
        if($mediaOk) {
			$this->out("checkMedia - database OK");
        }
	}    
    
    private function __clean($path) {
        
        $folder=& new Folder($path);
        $list = $folder->ls();

        foreach ($list[0] as $d) {
        	if($d[0] != '.') { // don't delete hidden dirs (.svn,...)
	        	if(!$folder->delete($folder->path.DS.$d)) {
	                throw new Exception("Error deleting dir $d");
	            }
        	}
        }
        foreach ($list[1] as $f) {
        	$file = new File($folder->path.DS.$f);
        	if(!$file->delete()) {
                throw new Exception("Error deleting file $f");
            }
        }
        return ;
    }    
        
    function cleanup() {
		$basePath = TMP;
    	if (isset($this->params['frontend'])) {
    		$basePath = $this->params['frontend'].DS."tmp".DS;
            $this->out('Cleaning dir: '.$basePath);
    		
    	}
        if (!isset($this->params['nologs'])) {
    	   $this->__clean($basePath . 'logs');
            $this->out('Logs cleaned.');
        }
        $this->__clean($basePath . 'cache' . DS . 'models');
        $this->__clean($basePath . 'cache' . DS . 'persistent');        
        $this->__clean($basePath . 'cache' . DS . 'views');        
        $this->out('Cache cleaned.');
        $this->__clean($basePath . 'smarty' . DS . 'compile');
        $this->__clean($basePath . 'smarty' . DS . 'cache');
        $this->out('Smarty compiled/cache cleaned.');

        if (isset($this->params['media'])) {
       		$this->removeMediaFiles();
        }
    }    

    private function removeMediaFiles() {
		$mediaRoot = Configure::read("mediaRoot");
    	$this->__clean($mediaRoot . DS. 'imgcache');
		$folder= new Folder($mediaRoot);
        $dirs = $folder->ls();
        foreach ($dirs[0] as $d) {
            if($d !== 'imgcache') {
            	$folder->delete($mediaRoot . DS. $d);
            }
        }
        $this->out('Media files cleaned.');
    	
    }

    public function checkApp() {
        $appPath = $this->params['app'];
        if (isset($this->params['frontend'])) {
        	$appPath = $this->params['frontend'];
        }
        $this->out('Checking cake app dir: '.$appPath);
        // config/core.php
        $this->checkAppFile($appPath.DS."config".DS."core.php");
        // config/database.php
        $this->checkAppFile($appPath.DS."config".DS."database.php");
        if (!isset($this->params['frontend'])) {
	        // config/bedita.cfg.php
	        $this->checkAppFile($appPath.DS."config".DS."bedita.cfg.php");
        }
        // index.php
        $this->checkAppFile($appPath.DS."index.php");
        // webroot/index.php
        $this->checkAppFile($appPath.DS."webroot".DS."index.php");
        if (!isset($this->params['frontend'])) {
	        // webroot/test.php
	        $this->checkAppFile($appPath.DS."webroot".DS."test.php");
        }
        // tmp/cache
        $this->checkAppDirPerms($appPath.DS."tmp".DS."cache");
        // tmp/smarty/compile
        $this->checkAppDirPerms($appPath.DS."tmp".DS."smarty".DS."compile");
        // tmp/logs
        $this->checkAppDirPerms($appPath.DS."tmp".DS."logs");
    }

    private function checkAppDirPerms($dirPath) {
       $this->out("$dirPath - perms: ".sprintf("%o",(fileperms($dirPath) & 511)));
    }

    private function checkAppFile($filePath) {
        if(!file_exists($filePath)) {
        	$this->out("$filePath: NOT FOUND!");
            $sampleFile = $filePath.".sample";
        	if(file_exists($sampleFile)) {
                $res = $this->in("$sampleFile found, create copy? [y/n]");
                if($res == "y") {
                    if(!copy($sampleFile, $filePath)) {
                        throw new Exception("Unable to copy $sampleFile to $filePath");
                    }                	
                }
        	}
        } else {
            $this->out("$filePath: ok.");
        }
    }
    
	function help() {
        $this->out('Available functions:');
        $this->out('1. updateDb: update database with bedita-db sql scripts');
  		$this->out(' ');
        $this->out('    Usage: updateDb [-db <dbname>] [-data <sql>] [-nodata] [-media <zipfile>]');
  		$this->out(' ');
  		$this->out("    -db <dbname>\t use db configuration <dbname> specified in config/database.php");
  		$this->out("    -nodata <sql>   \t don't insert data");
  		$this->out("    -data <sql>     \t use <sql> data dump, use absolute path if not in bedita-db/");
  		$this->out("    -media <zipfile> \t restore media files in <zipfile>");
  		$this->out(' ');
  		$this->out('2. cleanup: cleanup cahe, compile, log files');
        $this->out(' ');
        $this->out('    Usage: cleanup [-frontend <frontend path>] [-nologs] [-media]');
        $this->out(' ');
        $this->out("    -frontend \t clean files in <frontend path> [use frontend /app path]");
        $this->out("    -nologs \t don't clean log files");
        $this->out("    -media  \t clean media files in 'mediaRoot' (default no)");
        $this->out(' ');
        $this->out('3. checkMedia: check media files on db and filesystem');
        $this->out(' ');
        $this->out('4. export: export media files and data dump');
  		$this->out(' ');
        $this->out('    Usage: export [-f <tar-gz-filename>] [-nocompress]');
        $this->out(' ');
  		$this->out("    -f <tar-gz-filename>\t file to export, default ".self::DEFAULT_ARCHIVE_FILE);
        $this->out("    -nocompress \t don't compress, plain tar");
  		$this->out(' ');
        $this->out('5. import: import media files and data dump');
  		$this->out(' ');
  		$this->out('    Usage: import [-f <tar-gz-filename>] [-db <dbname>]');
        $this->out(' ');
  		$this->out("    -f <tar-gz-filename>\t file to import, default ".self::DEFAULT_ARCHIVE_FILE);
        $this->out("    -db <dbname>\t use db configuration <dbname> specified in config/database.php");
        $this->out(' ');
        $this->out('6. checkApp: check app files ... (core.php/database.php/index.php...)');
        $this->out(' ');
        $this->out('    Usage: checkApp [-frontend <app-path>]');
        $this->out(' ');
        $this->out("    -frontend \t check files in <frontend path> [use frontend /app path]");
        $this->out(' ');
	}
}

?>