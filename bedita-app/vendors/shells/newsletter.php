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
 * 
 * @link			http://www.bedita.com
 * @version			$Revision:  $
 * @modifiedby 		$LastChangedBy:  $
 * @lastmodified	$LastChangedDate: $
 * 
 * $Id: bedita.php 2015 2009-05-29 14:18:06Z dante $
 */
class NewsletterShell extends Shell {

	function importUsersNewsletterFromPhplist() {
		$phplist_to_card = array(
			'Email' => 'email',
			'Last modified' => 'modified',
			'Additional data' => 'note',
			'Name' => 'name',
			'Surname' => 'surname',
			'Phone' => 'phone',
			'Address' => 'street_address',
			'CAP' => 'zipcode',
			'City' => 'city',
			'State' => 'state',
			'Country' => 'country',
			'Your birthday is' => 'birthdate',
			'Gender' => 'gender',
			'Country' => 'country'
		);
		$separator = (!empty($this->params['sep'])) ? $this->params['sep'] : "\t";
		$verbose_log = isset($this->params['v']);

		$this->out("Importing user data from file: '" . $this->params['f'] . "' | using separator: '" . $separator . "' | associating to mailgroup: '" . $this->params['mailgroup'] . "'");
		$lines = @file($this->params['f']);
		if(!$lines) {
			$this->out("INFO: File " . $this->params['f'] . " not found: import aborted");
			return false;
		}
		$this->out("INFO: File " . $this->params['f'] . " found OK");

		$mailgroup = ClassRegistry::init("MailGroup");
		$mail_group_name = $mailgroup->field("group_name", array("id" => $this->params['mailgroup']));
		if(empty($mail_group_name)) {
			$this->out("INFO: Mail group " . $this->params['mailgroup'] . " not found: import aborted");
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
		$handle_result = fopen('import-result.log',"x+");
		$handle_error = fopen('import-error.log',"x+");
		$this->out(".............................................");
		foreach ($lines as $line_num => $line) {
			if($line_num==0) {
				$attributes = explode($separator,$line);
			} else {
				$data = array("ip_created" => "127.0.0.1", "user_created" => 1, "user_modified" => 1);
				$content[$line_num] = explode($separator,$line);
				foreach($content[$line_num] as $key => $value) {
					if (!empty($phplist_to_card[$attributes[$key]])) {
						$data[$phplist_to_card[$attributes[$key]]] = $value;
					}
				}
				if ( !($card_id = $card->field("id", array("newsletter_email" => $data['email']))) ) {
					$data['title'] = (!empty($data['name'])) ? $data['name'] : $data['email'];
					$data['newsletter_email'] = $data['email'];
					$data["joinGroup"][0]["mail_group_id"] = $this->params['mailgroup'];
					$data["joinGroup"][0]["status"] = "confirmed";
					$card->create();
					if(!($card->save($data))) {
						if($verbose_log) {
							$this->out($line_num . ":ERROR: error saving card '" . $data['email'] . "'");
						}
						fwrite($handle_error,$line_num . ":ERROR: error saving card '" . $data['email'] . "'\n");
						fwrite($handle_error,implode(",",$data) . "'\n");
						$cards_not_saved_counter++;
					} else {
						if($verbose_log) {
							$this->out($line_num . ":INFO: saved card '" . $data['email'] . "'");
							$this->out($line_num . ":INFO: saved card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'");
						}
						fwrite($handle_result,$line_num . ":INFO: saved card '" . $data['email'] . "'\n");
						fwrite($handle_result,$line_num . ":INFO: saved card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'\n");
						$new_cards_counter++;
						$cards_group_saved_counter++;
					}
					$card_id = $card->id;
				} else {
					if($verbose_log) {
						$this->out($line_num . ":INFO: card '" . $data['email'] . "' already present (skip save)");
					}
					fwrite($handle_result,$line_num . ":INFO: card '" . $data['email'] . "' already present (skip save)\n");
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
								$this->out($line_num . ":ERROR: error saving card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'");
							}
							fwrite($handle_error,$line_num . ":ERROR: error saving card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'\n");
							fwrite($handle_error,implode(",",$data) . "'\n");
							$cards_group_not_saved_counter++;
						} else {
							if($verbose_log) {
								$this->out($line_num . ":INFO: saved card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'");
							}
							fwrite($handle_result,$line_num . ":INFO: saved card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'\n");
							$cards_group_saved_counter++;
						}
					} else {
						if($verbose_log) {
							$this->out($line_num . ":INFO: card '" . $data['email'] . "' / mailgroup '" . $mail_group_name . "' association already present (skip save)");
						}
						fwrite($handle_result,$line_num . ":INFO: saved card/mailgroup association for '" . $data['email'] . "' / '" . $mail_group_name . "'\n");
						$cards_group_already_present++;
					}
				}
			}
		}
		fwrite($handle_result,"INFO: " . $new_cards_counter . " new cards saved\n");
		fwrite($handle_result,"INFO: " . $cards_already_present . " cards were already present (not saved)\n");
		fwrite($handle_result,"INFO: " . $cards_group_already_present . " cards/mail group association were already present (not saved)\n");
		fwrite($handle_result,"INFO: " . $cards_not_saved_counter . " not saved (error on saving)\n");
		fwrite($handle_result,"INFO: " . $cards_group_saved_counter . " cards associated to mail group " . $mail_group_name . "\n");
		fwrite($handle_result,"INFO: " . $cards_group_not_saved_counter . " cards not associated to mail group " . $mail_group_name . " (error on saving)\n");
		$this->out(".............................................");
		$this->out("INFO: " . $new_cards_counter . " new cards saved");
		$this->out("INFO: " . $cards_already_present . " cards were already present (not saved)");
		$this->out("INFO: " . $cards_group_already_present . " cards/mail group association were already present (not saved)");
		$this->out("INFO: " . $cards_not_saved_counter . " not saved (error on saving)");
		$this->out("INFO: " . $cards_group_saved_counter . " cards associated to mail group " . $mail_group_name);
		$this->out("INFO: " . $cards_group_not_saved_counter . " cards not associated to mail group " . $mail_group_name . " (error on saving)");
		fclose($handle_result);
		fclose($handle_error);
	}

	function help() {
		$this->out('Available functions:');
		$this->out('1. importUsersNewsletterFromPhplist: import users from file csv to cards, associating them to a newsletter mailgroup');
		$this->out(' ');
		$this->out('   Usage 1: importUsersNewsletterFromPhplist -f <users-file> -sep <separator> -mailgroup <id mailgroup>');
		$this->out('   Usage 2 (verbose log): importUsersNewsletterFromPhplist -f <users-file> -sep <separator> -mailgroup <id mailgroup> -v');
		$this->out(' ');
		$this->out(' ');
	}
}

?>