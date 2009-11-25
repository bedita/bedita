<?php 
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

App::import('Core', 'Controller');
App::import('Controller', 'App');
/**
 * Newsletter shell: methods to import/export newsletter data (for example phplist filters), 
 * other newsletter related utilities
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id: bedita.php 2015 2009-05-29 14:18:06Z dante $
 */
class NewsletterShell extends Shell {

	private $baseLogs;
	
	/**
	 * map phplist database fields to BEdita database fields
	 */
	private $phplist_to_card = array(
				'Email' => 'email',
				'Lastmodified' => 'modified',
				'SendthisuserHTMLemails' => 'mail_html',
				'Additionaldata' => 'note',
				'Name' => 'name',
				'Surname' => 'surname',
				'Phone' => 'phone',
				'Address' => 'street_address',
				'CAP' => 'zipcode',
				'City' => 'city',
				'State' => 'state',
				'Country' => 'country',
				'Yourbirthdayis' => 'birthdate',
				'Gender' => 'gender',
				'Country' => 'country',
				'ListMembership' => 'list_name'
			);
	
	public function startup() {
		$this->_welcome();
		$this->baseLogs =  APP . 'tmp' . DS . 'logs' . DS;
	}

	public function addCards() {
		
		if (empty($this->params['mailgroup'])) {
			$this->out("Missing mailgroup. Params -mailgroup is required.");
			return;
		}
		
		$mgParam = $this->params['mailgroup'];
		$mailgroup = ClassRegistry::init("MailGroup");
		$mailGroupId = $mailgroup->field("id", array("group_name" => $mgParam));
		if(empty($mailGroupId)) {
			$this->out("mailgroup not found.");
			return;
		}

		$mgc = ClassRegistry::init("MailGroupCard");
		
		$cardModel = ClassRegistry::init("Card");
		$condition = array("status" => "on");
		
		$categoryId = null;
		
		if(!empty($this->params['c'])) {
			$categoryModel = ClassRegistry::init("Category");
			$categoryModel->bviorCompactResults = false;
			$categoryId = $categoryModel->field("id", array("name" => $this->params['c']));
			if(empty($categoryId)) {
				$this->out("category not found.");
				return;
			}
		}

		$cardsId = $cardModel->find('all', array("condition" => $condition, 
			"fields" => array("Card.id"), "contain" => array("BEObject"=>array("Category"))));		
		$count = 0;
		foreach ($cardsId as $card) {
			$data = array("status" => "confirmed", "mail_group_id" => $mailGroupId,
				"card_id" => $card["id"]);

			$save = false;
			if($categoryId != null) {
				foreach($card["Category"] as $cat) {
					if($cat["id"] == $categoryId) {
						$save = true;
					}
				}
			} else {
				$save = true;
			}

			if($save) {
				$mgc->create();
				if(!$mgc->save($data)) {
					throw new BeditaException("Error adding card: " . $card["id"]);
				}
				$count++;
			}
		}
		$this->out("Added $count cards to mailgroup $mgParam - $mailGroupId");
	}

	public function merge() {
		if (empty($this->params['from'])) {
			$this->out("Missing 'from' parameter, -from is required.");
			return;
		}
		$from = $this->params['from'];
		$mailGroup = ClassRegistry::init("MailGroup");
		$idFrom = $mailGroup->field("id", array("group_name" => $from));
		if(empty ($idFrom)) {
			$this->out("Mail group '$from' not found");
			return;
		}

		if (empty($this->params['to'])) {
			$this->out("Missing 'to' parameter, -to is required.");
			return;
		}
		$to = $this->params['to'];
		if ($from == $to) {
			$this->out("Mail groups have to be different.");
			return;
		}
		
		$idTo = $mailGroup->field("id", array("group_name" => $to));
		if(empty ($idTo)) {
			$this->out("Mail group '$to'' not found");
			return;
		}

		$mgc = ClassRegistry::init("MailGroupCard");
		$mgcId = $mgc->find('all', array("condition" => array("mail_group_id" => $idFrom)));		
	
		$count = 0;	
		foreach ($mgcId as $mId) {
			$idFound = $mgc->field("id",
				array("mail_group_id" => $idTo, 
				"card_id" => $mId["MailGroupCard"]["card_id"]));
			if(empty($idFound)) {
				$mgc->id = $mId['MailGroupCard']['id'];
				if(!$mgc->saveField("mail_group_id", $idTo)) {
					throw new BeditaException(__("Error saving mail group card",true));
				}
				$count++;
			}
		}
		$this->out("Moved $count cards from mailgroup '$from' to '$to'");
		$res = $this->in("Do you want to remove mailgroup '$from'? [y/n]");
		if($res != "y") {
			$this->out("Bye");
			return;
		} else {
			if(!$mailGroup->del($idFrom)) {
			 	throw new BeditaException("Error removing mail group $mailGroup");
			}
			$this->out("Done");
		}
		
	}
	
	
	public function import() {
		$from = (!empty($this->params['from']))? $this->params['from'] : "phplist";
		$methodName = "importFrom" . Inflector::camelize($from);
		if (method_exists($this, $methodName)) {
			$this->{$methodName}();
		} else {
			$this->out("Import from " . $from . " is not supported");
		}
	}  
	
	private function importFromPhplist() {
		if (empty($this->params['mailgroup'])) {
			$this->out("Missing mailgroup id. Params -mailgroup is required importing from PHPLIST.");
			exit;
		}
		
		$separator = (!empty($this->params['sep'])) ? $this->params['sep'] : "\t";
		$verbose_log = isset($this->params['v']);
		$force_import = isset($this->params['force']);

		$this->out("Importing user data from file: '" . $this->params['f'] . "' | using separator: '" . $separator . "' | associating to mailgroup: '" . $this->params['mailgroup'] . "'");
		$lines = @file($this->params['f']);
		if(!$lines) {
			$this->out("[" . date('Y-m-d H:i:s') . "] INFO: File " . $this->params['f'] . " not found: import aborted");
			return false;
		}
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO: File " . $this->params['f'] . " found OK");

		$mailgroup = ClassRegistry::init("MailGroup");
		$mail_group_name = $mailgroup->field("group_name", array("id" => $this->params['mailgroup']));
		if(empty($mail_group_name)) {
			$this->out("[" . date('Y-m-d H:i:s') . "] INFO: Mail group " . $this->params['mailgroup'] . " not found: import aborted");
			return false;
		}

		$card = ClassRegistry::init("Card");
		$mailgroupcard = ClassRegistry::init("MailGroupCard");
		$content = array();
		$new_cards_counter = 0;
		$cards_not_saved_counter = 0;
		$cards_group_saved_counter = 0;
		$cards_group_not_saved_counter = 0;
		$cards_already_present = 0;
		$cards_group_already_present = 0;
		$cards_save_forced = 0;
		
		if(file_exists($this->baseLogs.'newsletter-import-result.log')) {
			unlink($this->baseLogs.'newsletter-import-result.log');
		}
		if(file_exists($this->baseLogs.'newsletter-import-error.log')) {
			unlink($this->baseLogs.'newsletter-import-error.log');
		}
		if(file_exists($this->baseLogs.'newsletter-import-error-cards.txt')) {
			unlink($this->baseLogs.'newsletter-import-error-cards.txt');
		}
		$handle_result = fopen($this->baseLogs.'newsletter-import-result.log',"a+");
		$handle_error = fopen($this->baseLogs.'newsletter-import-error.log',"a+");
		$handle_error_2 = fopen($this->baseLogs.'newsletter-import-error-cards.txt',"a+");
		$this->out(".............................................");
		foreach ($lines as $line_num => $line) {
			fflush($handle_result);
			fflush($handle_error);
			if($line_num==0) {
				$attributes = explode($separator,$line);
				foreach($attributes as $k => $v) {
					$attributes[$k] = preg_replace('/\s*/m','',$v);
				}
			} else {
				if(($line_num>1) && ($line_num%100 == 1)) { // every 100 lines, print a summary...
					$this->out("[" . date('Y-m-d H:i:s') . "] INFO: " .
					($line_num-1) . " lines processed: " . 
					$new_cards_counter . " new cards saved, " . 
					$cards_already_present . " cards already present (not saved), " . 
					$cards_not_saved_counter . " cards with error/s (not saved), " .
					$cards_save_forced . " cards forced to be saved (skip validation)");
				}
				$data = array("ip_created" => "127.0.0.1", "user_created" => 1, "user_modified" => 1, "status" => "on");
				$content[$line_num] = explode($separator,$line);
				foreach($content[$line_num] as $key => $value) {
					if (!empty($this->phplist_to_card[$attributes[$key]])) {
						$data[$this->phplist_to_card[$attributes[$key]]] = trim($value);
					}
				}
				if ( !($card_id = $card->field("id", array("newsletter_email" => $data['email']))) ) {
					if (empty($data['name'])) {
						$data['title'] = $data['name'] = $data['email'];
					} else {
						$data['title'] = $data['name'];
					}
					if(strpos($data['title'],'Utente Newsletter Goodwill') !== false) {
						$data['title'] = $data['email'];
					}
					$data['newsletter_email'] = $data['email'];
					$data["joinGroup"][0]["mail_group_id"] = $this->params['mailgroup'];
					$data["joinGroup"][0]["status"] = "confirmed";
					$card->create();
					if(!($card->save($data))) {
						if($verbose_log) {
							$this->out("[" . date('Y-m-d H:i:s') . "] - line $line_num - ERROR error saving card '" . $data['email'] . "'");
							$this->out($card->validationErrors);
							if($force_import) {
								$this->out("[" . date('Y-m-d H:i:s') . "] - line $line_num - INFO forcing to save card '" . $data['email'] . "'");
							}
						}
						if($force_import) {
							$card->create();
							if(!($card->save($data,false))) {
								if($verbose_log) {
									$this->out("[" . date('Y-m-d H:i:s') . "] - line $line_num - ERROR error forcing to save card '" . $data['email'] . "'");
								}
								fwrite($handle_error,"[" . date('Y-m-d H:i:s') . "] - line $line_num - ERROR error forcing to save card '" . $data['email'] . "'\n");
							} else {
								$cards_save_forced++;
								$cards_not_saved_counter--;
							}
						}
						fwrite($handle_error,"[" . date('Y-m-d H:i:s') . "] - line $line_num - ERROR error saving card '" . $data['email'] . "'\n");
						fwrite($handle_error,$line . "'\n");
						fwrite($handle_error_2,$line . "'\n");
						$cards_not_saved_counter++;
					} else {
						if($verbose_log) {
							$this->out("[" . date('Y-m-d H:i:s') . "] - line $line_num - INFO saved card '" . $data['email'] . "'");
							$this->out("[" . date('Y-m-d H:i:s') . "] - line $line_num - INFO saved card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'");
						}
						fwrite($handle_result,"[" . date('Y-m-d H:i:s') . "] - line $line_num - INFO saved card '" . $data['email'] . "'\n");
						fwrite($handle_result,"[" . date('Y-m-d H:i:s') . "] - line $line_num - INFO saved card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'\n");
						$new_cards_counter++;
						$cards_group_saved_counter++;
					}
					$card_id = $card->id;
				} else {
					if($verbose_log) {
						$this->out("[" . date('Y-m-d H:i:s') . "] - line $line_num - INFO card '" . $data['email'] . "' already present (skip save)");
					}
					fwrite($handle_result,"[" . date('Y-m-d H:i:s') . "] - line $line_num - INFO card '" . $data['email'] . "' already present (skip save)\n");
					$cards_already_present++;
					if (!$mailgroupcard->field("id", array("mail_group_id" => $this->params['mailgroup'], "card_id" => $card_id)) ) {
						$dataJoin = array();
						$dataJoin["MailGroupCard"]["mail_group_id"] = $this->params['mailgroup'];
						$dataJoin["MailGroupCard"]["card_id"] = $card_id;
						$dataJoin["MailGroupCard"]["status"] = "confirmed";
						$dataJoin["MailGroupCard"]["hash"] = md5($card_id . microtime() . $mail_group_name);
						$mailgroupcard->create();
						if (!$mailgroupcard->save($dataJoin)) {
							if($verbose_log) {
								$this->out("[" . date('Y-m-d H:i:s') . "] - line $line_num - ERROR error saving card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'");
							}
							fwrite($handle_error,"[" . date('Y-m-d H:i:s') . "] - line $line_num - ERROR error saving card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'\n");
							fwrite($handle_error,$content[$line_num] . "'\n");
							$cards_group_not_saved_counter++;
						} else {
							if($verbose_log) {
								$this->out("[" . date('Y-m-d H:i:s') . "] - line $line_num - INFO saved card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'");
							}
							fwrite($handle_result,"[" . date('Y-m-d H:i:s') . "] - line $line_num - INFO saved card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'\n");
							$cards_group_saved_counter++;
						}
						unset($dataJoin);
					} else {
						if($verbose_log) {
							$this->out("[" . date('Y-m-d H:i:s') . "] - line $line_num - INFO card '" . $data['email'] . "' / mailgroup '" . $mail_group_name . "' association already present (skip save)");
						}
						fwrite($handle_result,"[" . date('Y-m-d H:i:s') . "] - line $line_num - INFO saved card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'\n");
						$cards_group_already_present++;
					}
				}
				unset($data);
			}
		}
		fwrite($handle_result,"[" . date('Y-m-d H:i:s') . "] INFO: " . $new_cards_counter . " new cards saved\n");
		fwrite($handle_result,"[" . date('Y-m-d H:i:s') . "] INFO: " . $cards_already_present . " cards were already present (not saved)\n");
		fwrite($handle_result,"[" . date('Y-m-d H:i:s') . "] INFO: " . $cards_group_already_present . " cards/mail group association were already present (not saved)\n");
		fwrite($handle_result,"[" . date('Y-m-d H:i:s') . "] INFO: " . $cards_not_saved_counter . " cards not saved (error on saving)\n");
		fwrite($handle_result,"[" . date('Y-m-d H:i:s') . "] INFO: " . $cards_save_forced . " forced to be saved (skip validation)\n");
		fwrite($handle_result,"[" . date('Y-m-d H:i:s') . "] INFO: " . $cards_group_saved_counter . " cards associated to mail group " . $mail_group_name . "\n");
		fwrite($handle_result,"[" . date('Y-m-d H:i:s') . "] INFO: " . $cards_group_not_saved_counter . " cards not associated to mail group " . $mail_group_name . " (error on saving)\n");
		
		$this->out(".............................................");
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO: " . $new_cards_counter . " new cards saved");
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO: " . $cards_already_present . " cards were already present (not saved)");
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO: " . $cards_group_already_present . " cards/mail group association were already present (not saved)");
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO: " . $cards_not_saved_counter . " cards not saved (error on saving)");
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO: " . $cards_save_forced . " forced to be saved (skip validation)");
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO: " . $cards_group_saved_counter . " cards associated to mail group " . $mail_group_name);
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO: " . $cards_group_not_saved_counter . " cards not associated to mail group " . $mail_group_name . " (error on saving)");
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO:  created/modified file " . $this->baseLogs . "import-result.log");
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO:  created/modified file " . $this->baseLogs . "import-error.log");
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO:  created/modified file " . $this->baseLogs . "import-error-cards.txt");
		fclose($handle_result);
		fclose($handle_error);
		fclose($handle_error_2);
	}

	function createFilesListsFromPhplist() {		
		$separator = (!empty($this->params['sep'])) ? $this->params['sep'] : "\t";
		$lines = @file($this->params['f']);
		if(!$lines) {
			$this->out("[" . date('Y-m-d H:i:s') . "] INFO: File " . $this->params['f'] . " not found: operation aborted");
			return false;
		}
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO: File " . $this->params['f'] . " found OK");

		$data = array();
		$content = array();
		$files = array(); // 'lista' => 'nome file'
		$handle = array();
		$code_entities_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','=');
		$code_entities_replace = array('','','','','','','','','','','','','','','','','','','','.','','','','','','');
		$this->out(".............................................");
		foreach ($lines as $line_num => $line) {
			if($line_num==0) {
				$attributes = explode($separator,$line);
				foreach($attributes as $k => $v) {
					$attributes[$k] = preg_replace('/\s*/m','',$v);
				}
			} else {
				if(($line_num>1) && ($line_num%100 == 1)) { // every 100 lines, print a summary...
					$this->out("[" . date('Y-m-d H:i:s') . "] INFO: " . ($line_num-1) . " lines processed");
				}
				$content[$line_num] = explode($separator,$line);
				foreach($content[$line_num] as $key => $value) {
					if (!empty($this->phplist_to_card[$attributes[$key]])) {
						$data[$this->phplist_to_card[$attributes[$key]]] = trim($value);
					}
				}
				if(!empty($data['list_name'])) {
					if(!array_key_exists($data['list_name'],$files)) {
						$files[$data['list_name']] = preg_replace('/\s*/m','',$data['list_name']);
						$files[$data['list_name']] = str_replace($code_entities_match, $code_entities_replace, $files[$data['list_name']]) . ".csv"; 
						if(file_exists($files[$data['list_name']])) {
							unlink($files[$data['list_name']]);
						}
						$handle[$data['list_name']] = fopen($files[$data['list_name']],"a+");
						fwrite($handle[$data['list_name']],$lines[0]);
					}
					// write row on file
					fwrite($handle[$data['list_name']],$line);
				}
			}
		}
		foreach($files as $l => $f) {
			fclose($handle[$l]);
			$this->out("[" . date('Y-m-d H:i:s') . "] INFO: created file '$f' for list '$l'");
		}
		$this->out(".............................................");
		$this->out("[" . date('Y-m-d H:i:s') . "] INFO: end");
	}

	function help() {
		$this->out('Available functions:');
		$this->out('1. import: import data from specific source (default phplist)');
		$this->out(' ');
		$this->out('	Usage: import -f <import-file-name> [-mailgroup <mailgroup id>] [-from <importsource>] [-sep <separator>] [-v] [-force]');
		$this->out(' ');
		$this->out("	-f <import-file-name>\t file to import");
		$this->out("	-mailgroup <mailgroup id>\t mailgroup id to associating imported users");
		$this->out("	-from <importsource>\t import source type for example from phplist csv export");
		$this->out("	-sep <separator>\t separator (if it\'s needed, for example if you import from csv)");
		$this->out("	-v verbose mode");
		$this->out("	-force try to save with validation errors too");
		$this->out(' ');
		$this->out('2. createFilesListsFromPhplist: create files for each list found in the phplist export file ');
		$this->out(' ');
		$this->out('3. addCards: add cards to mailgroup, all or by category');
		$this->out(' ');
		$this->out('	Usage: addCards -mailgroup <mailgroup name> [-c <category name>]');
		$this->out(' ');
		$this->out("	-mailgroup <mailgroup name>\t name of mail group / list");
		$this->out("	-c <category name>\t select cards by category (default: all)");
		$this->out(' ');
		$this->out('4. merge: merge a mailgroup into another');
		$this->out(' ');
		$this->out('	Usage: merge -from <mailgroup-name> -to <mailgroup-name>');
		$this->out(' ');
		$this->out("	-from <mailgroup-name>\t name of source mail group / list");
		$this->out("	-to <mailgroup-name>\t name of destination mail group / list");
		$this->out(' ');
	}
}

?>