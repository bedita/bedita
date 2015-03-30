<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2010-2015 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * Addressbook card model
 *
 */
class Card extends BEAppObjectModel {

	public $searchFields = array(
		"title" => 10,
		"nickname" => 8,
		"description" => 4,
		"website" => 6,
		"email" => 6,
		"email2" => 5,
		"street_address" => 3,
		"company_name" => 3,
		"city" => 4,
		"note" => 2
	);

	var $actsAs 	= array(
			'CompactResult' 		=> array("MailGroup", "GeoTag")
	); 
	
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permission",
															"ObjectProperty",
															"LangText",
															"RelatedObject",
															"Category",
															"Annotation",
															"User",
															"Alias",
															"Version" => array("User.realname", "User.userid"),
															"GeoTag"
														),
									"MailGroup"),

				"default" => array("BEObject" => array("ObjectProperty", 
									"LangText", "ObjectType", 
									"Category", "RelatedObject","Annotation" )),

				"minimum" => array("BEObject" => array("ObjectType")),
		
				"frontend" => array("BEObject" => array("LangText","RelatedObject", "GeoTag", "ObjectProperty", "Category"))
		);
		
	public $objectTypesGroups = array("leafs", "related");
	
	var $hasAndBelongsToMany = array(
			"MailGroup" => array(
							"joinTable"	=> "mail_group_cards",
							"with" => "MailGroupCard"
						)
		);
	
//	var $hasMany = array() ;

	var $validate = array(
		'email' => array(
			'rule' => 'email',
			'allowEmpty' => true,
			'message' => 'Please supply a valid email address.'
 		),
 		'email2' => array(
			'rule' => 'email',
			'allowEmpty' => true,
			'message' => 'Please supply a valid email address.'
 		),
 		'newsletter_email' => array(
			'rule' => 'email',
			'allowEmpty' => true,
			'message' => 'Please supply a valid email address.'
 		)
 	);

 	private $csvFields = array (
			"Title" => "person_title", "First Name" => "name", "Middle Name", "Last Name" => "surname",
			"Suffix", "E-mail Address" => "email", "E-mail 2 Address" => "email2", "E-mail 3 Address",
			"Business Street","Business Street 2","Business Street 3","Business City",
			"Business State" => "state_name", "Business Postal Code" => "zipcode", "Business Country" => "country",
			"Home Street" => "street_address",
			"Home Street 2","Home Street 3","Home City","Home State","Home Postal Code","Home Country",
			"Other Street","Other Street 2","Other Street 3","Other City","Other State",
			"Other Postal Code","Other Country","Company" => "company_name","Department",
			"Job Title","Assistant's Phone","Business Fax" => "fax","Business Phone" => "phone",
			"Business Phone 2" => "phone2" ,"Callback",
			"Car Phone","Company Main Phone","Home Fax","Home Phone","Home Phone 2","ISDN","Mobile Phone",
			"Other Fax","Other Phone","Pager","Primary Phone","Radio Phone","TTY/TDD Phone","Telex",
			"Assistant's Name","Birthday" => "birthdate","Manager's Name","Notes","Other Address PO Box","Spouse",
			"Web Page" => "website", "Personal Web Page" => "website",
 	        'Mail Group' => 'mail_group'
		);
 	
 	// default CSV delimiter is ',' 
 	const DEFAULT_CSV_DELIMITER = ',';
 	private $csvDelimiter = self::DEFAULT_CSV_DELIMITER;
 	
	function beforeValidate() {
		
		$this->checkDate('birthdate');
		$this->checkDate('deathdate');

		return true;
	}
	
	function beforeSave() {	
		if(empty($this->data["Card"]["email"]) && empty($this->data["Card"]["newsletter_email"]) ) {
			unset($this->data["Card"]["joinGroup"]);
		}
		if(empty($this->data["Card"]["newsletter_email"]) && !empty($this->data["Card"]["email"])) {
			$this->data["Card"]["newsletter_email"] = $this->data["Card"]["email"];
		}
		return true;
	}
	
	function afterSave($created) {
		// save join with mail groups
		if (!empty($this->data["Card"]["joinGroup"])) {
		
			if (empty($this->id))
				throw new BeditaException(__("Error saving card", true), "Missing model id in afterSave.");
			
			$mailGroupCard = ClassRegistry::init("MailGroupCard");
			
			$mailGroupCard->deleteAll(array("card_id" => $this->id));
				
			foreach ($this->data["Card"]["joinGroup"] as $joinData) {
				
				// rebuild active join
				if (!empty($joinData["mail_group_id"])) {
					$joinData["card_id"] = $this->id;
					$mailGroupCard->create();
					if (!$mailGroupCard->save($joinData))
						throw new BeditaException(__("Error creating join between card and groups", true), "Saving error");

				}
			}
			
		}

		return true;
	}
	
	
    private function setupCsvDelimiter($options) {
        if (!empty($options['delimiter'])) {
            $this->csvDelimiter = $options['delimiter'];
        } else {
            $this->csvDelimiter = self::DEFAULT_CSV_DELIMITER;
        }
    }

    /**
     * Import a Microsoft Outlook/Outlook Express CSV file
     *
     * @param string $csvFile,
     *            path to csv file file
     * @param array $options,
     *            default attributes array to use,
     *            e.g. categories, "Category" => array (1,3,4..) - array of id-categories
     * @return results array, num of objects saved ('numSaved') and other data
     */
    public function importCSVFile($csvFile, array $options = null) {
        $defaults = array(
            'status' => 'on',
            'user_created' => '1',
            'user_modified' => '1',
            'lang' => Configure::read('defaultLang'),
            'ip_created' => '127.0.0.1'
        );
        $this->setupCsvDelimiter($options);
        if (! empty($options)) {
            unset($options['delimiter']);
            $defaults = array_merge($defaults, $options);
        }
        $row = 1;
        $res = array(
            'numSaved' => 0
        );
        $handle = fopen($csvFile, 'r');
        // read header
        $csvKeys = fgetcsv($handle, 1000, $this->csvDelimiter);
        $numKeys = count($csvKeys);
        $keys = array();
        $beFields = array_values($this->csvFields);
        $customCsvFields = Configure::read('csvFields.card');
        if (empty($customCsvFields)) {
            $customCsvFields = array();
        }
        $customFieldNames = array_values($customCsvFields);
        $fieldFound = false;
        foreach ($csvKeys as $f) {
            $f = trim($f);
            $fieldNameLow = strtolower($f);
            $k = null;
            if (! empty($this->csvFields[$f])) {
                $k = $this->csvFields[$f];
            } else 
                if (in_array($fieldNameLow, $beFields)) {
                    $k = $fieldNameLow;
                } else 
                    if (! empty($customCsvFields[$fieldNameLow])) {
                        $k = $customCsvFields[$fieldNameLow];
                    }
            if (empty($k)) {
                $this->log('import CSV: field name not found ' . $f, 'warn');
            } else {
                $fieldFound = true;
            }
            $keys[] = $k;
        }
        if (!$fieldFound) {
            fclose($handle);
            $line = fgets(fopen($csvFile, 'r'));
            throw new BeditaException('Bad CSV header in file ' . $csvFile, $line);
        }

        while (($fields = fgetcsv($handle, 1000, $this->csvDelimiter)) !== false) {
            $data = array();
            $row ++;
            for ($c = 0; $c < $numKeys; $c ++) {
                if (! empty($keys[$c]) && ! empty($fields[$c])) {
                    $data[$keys[$c]] = trim($fields[$c]);
                }
            }
            $this->create();
            $d = array_merge($defaults, $data);
            if (! empty($d['surname']) || ! empty($d['name'])) {
                $d['title'] = ((! empty($d['name'])) ? $d['name'] : '') . ' ' . ((! empty($d['surname'])) ? $d['surname'] : '');
            }
            // check mail_groups field
            if (! empty($d['mail_group'])) {
                $mailGroups = explode(',', $d['mail_group']);
                $count = 0;
                foreach ($mailGroups as $mg) {
                    $mg = trim($mg);
                    if (! empty($options['mailGroups'][$mg])) {
                        $mailGroupId = $options['mailGroups'][$mg];
                        $d['joinGroup'][$count]['mail_group_id'] = $mailGroupId;
                        $d['joinGroup'][$count]['status'] = 'confirmed';
                        $count ++;
                    } else {
                        $this->log('import CSV: mail group not found ' . $mg, 'warn');
                    }
                }
            }
            
            if (! $this->save($d)) {
                throw new BeditaException(__('Error saving card'), print_r($data, true) . 
                    " \nrow: $row \nvalidation: " . print_r($this->validationErrors, true));
            }
        }
        fclose($handle);
        $res['numSaved'] = $row - 1;
        return $res;
    }

    /**
     * Get Microsoft Outlook CSV header or custom CSV header
     * 
     * @param array $options, may contain 
     *      - 'delimiter' => '<char delimiter to use>' default is ','
     *      - 'custom' => true (use custom format) default false
     * @return string, single line as string
     */
    public function headerCSV(array $options = null) {
        $res = '';
        $this->setupCsvDelimiter($options);
        $fields = $this->csvFields;
        $custom = !empty($options['custom']);
        if ($custom) {
            $fields = Configure::read('csvFields.card');
            if (empty($fields)) {
                $fields = array();
            }
        }
        $first = true;
        foreach ($fields as $k => $v) {
            if (!$first) {
                $res .= $this->csvDelimiter;
            }
            if ($v === 'mail_group') {
                continue;
            }
            if (!$custom && is_numeric($k)) {
                 $res .= "\"$v\"";
            } else {
                $res .= "\"$k\"";
            }
            $first = false;
        }
        return $res;
    }

    /**
     * Export model data to Microsoft Outlook CSV format or custom CSV format
     *
     * @param array $options, may contain 
     *      - 'delimiter' => '<char delimiter to use>' default is ','
     *      - 'custom' => true (use custom format) default false
     * @param array $data, object data to export
     * @return string, single line as string
     */
    public function exportCSV(array $options = null, array $data = array()) {
        $res = '';
        if (empty ($data)) {
            $data = $this->findById($this->id);
        }
        $this->setupCsvDelimiter($options);
        $fields = $this->csvFields;
        $custom = !empty($options['custom']);
        if ($custom) {
            $fields = Configure::read('csvFields.card');
            if (empty($fields)) {
                $fields = array();
            }
        }
        $first = true;
        foreach ($fields as $k => $v) {
            if (!$first) {
                $res .= $this->csvDelimiter;
            }
            if ($v === 'mail_group') {
                continue;
            }
            if ($custom) {
                $res .= (empty($data[$v]) ? '' : '"'. $data[$v] . '"');
            } else {
                $res .= (empty($v) || is_numeric($k)) ? '' : '"'. $data[$v] . '"';
            }
            $first = false;
        }
        return $res;
    }

    /**
     * Generate CSV file content as string from cards data array
     *
     * @param array $options, may contain 
     *      - 'delimiter' => '<char delimiter to use>' default is ','
     *      - 'custom' => true (use custom format) default false
     * @param array $data, object data to export
     * @return string, requested CSV content
     */
    public function createCsvAsString(array $data, array $options = null) {
        $str = $this->headerCSV($options) . "\n";
        foreach ($data as $d) {
            $str .= $this->exportCSV($options, $d) . "\n";
        }
        return $str;
    }

	/**
	 * Import a vCard/vcf file
	 *
	 * @param string $cardFile, path to vcard file
	 * @param array $options, default attributes array to use, 
	 *  e.g. categories, "Category" => array (1,3,4..) - array of id-categories
	 * @return results array, num of objects saved ("numSaved") and other data
	 */
	public function importVCardFile($cardFile, array $options = null) {
		$lines = file($cardFile);
		if (!$lines) {
			throw new BeditaException(__("Error reading vCard file") . ": " . $cardFile);
		}
		$defaults = array( 
			"status" => "on",
			"user_created" => "1",
			"user_modified" => "1",
			"lang" => Configure::read("defaultLang"),
			"ip_created" => "127.0.0.1",
		);
		
		if(!empty($options)) {
			$defaults = array_merge($defaults, $options);
		}
		
		$numSaved = 0;
		$cards = $this->parseVCards($lines);
		foreach ($cards as $c) {
			$this->create();
			$data = array_merge($defaults, $c);
			// check if card is present
			$currdata = $this->find("first", array(
						"conditions" => array("title" => $data["title"]),
						"contain" => array("BEObject")));
			if($currdata != false) {
				if(!empty($data["modified"]) && $data["modified"] < $currdata["modified"]) {
					$data = array_merge($data, $currdata);					
				} else {
					$data = array_merge($currdata, $data);					
				}
			}
			
			if(!$this->save($data)) {
				throw new BeditaException(__("Error saving card"), print_r($c, true) . " \nvalidation: " . print_r($this->validationErrors, true));
			}
			$numSaved++;
		}

		return array("numSaved" => $numSaved);		
	}

	/**
	 * Export model data to VCard format 
	 *
	 */
	public function exportVCard() {
		$data = $this->findById($this->id);
		$res = "\nBEGIN:VCARD\nVERSION:3.0\n";
		$data["vname"] = (empty($data["surname"]) ? "" : $data["surname"] . ";") .
			(empty($data["name"]) ? "" : $data["name"]);
		$res .= $this->vcardLine("N:$$\n", "vname", $data);
		unset($data["vname"]);
		$res .= $this->vcardLine("FN:$$\n", "title", $data);
		$res .= $this->vcardLine("TITLE:$$\n", "person_title", $data);
		$res .= $this->vcardLine("ORG:$$\n", "company_name", $data);
		$res .= $this->vcardLine("TEL;TYPE=PREF:$$\n", "phone", $data);
		$res .= $this->vcardLine("TEL:$$\n", "phone2", $data);
		$res .= $this->vcardLine("TEL;TYPE=FAX:$$\n", "fax", $data);
		$res .= $this->vcardLine("EMAIL;TYPE=PREF:$$\n", "email", $data);
		$res .= $this->vcardLine("EMAIL:$$\n", "email2", $data);
		$data["vaddr"] = (empty($data["street_address"]) ? "" : $data["street_address"] . ";") .
			(empty($data["city"]) ? "" : $data["city"] . ";") . (empty($data["zipcode"]) ? "" : $data["zipcode"] . ";")
			. (empty($data["country"]) ? "" : $data["country"] . ";");
		$res .= $this->vcardLine("ADR:$$\n", "vaddr", $data);
		unset($data["vaddr"]);
		$res .= $this->vcardLine("BDAY:$$\n", "birthdate", $data);
		$t = strtotime($data["modified"]);
		$res .= "REV:" . date("Ymd", $t) . "T" . date("His", $t) . "Z\n";
		$res .= "END:VCARD\n";
		return $res;
	}
	
	private function vcardLine($vline, $field, array& $data) {
		$res = "";
		if(!empty($data[$field])) {
			$res = str_replace("$$", $data[$field], $vline);
		}
		return $res;
	}

	private function parseVCards(&$lines) {
		App::import('vendor', "VCard", true, array(), "vcard.php");		
		$cards = array();
		$done = false;
		while (!$done) {
			$card = new VCard();
			if(!$card->parse($lines)) {
				$done = true;
			} else {
			
				$nProp = $card->getProperty('N');
				if (!empty($nProp)) {
					$n = $nProp->getComponents();
				}
				$item["name"] = !empty($n[1]) ? trim($n[1]) : null;
				$item["surname"] = !empty($n[0]) ? trim($n[0]) : null;
				
				$fnProp = $card->getProperty('FN');
				
				$nameProp = $card->getProperty('NAME');
				$emailProp = $card->getProperties('EMAIL');
				$telProp = $card->getProperties('TEL');
				$orgProp = $card->getProperty('ORG');
				
				$item = array();
				$item["title"] = !empty($nameProp->value) ? trim($nameProp->value) : (!empty($fnProp->value) ? trim($fnProp->value) : null );
				

				$item["email"] = !empty($emailProp[0]->value) ? trim($emailProp[0]->value) : null;
				$item["phone"] = !empty($telProp[0]->value) ? trim($telProp[0]->value) : null;
				$item["email2"] = !empty($emailProp[1]->value) ? trim($emailProp[1]->value) : null;
				$item["phone2"] = !empty($telProp[1]->value) ? trim($telProp[1]->value) : null;
							
				if(empty($item["title"])) {
					$item["title"] = (!empty($item["name"]) ? $item["name"] : "") . 
						(!empty($item["surname"]) ? " " . $item["surname"] : "");
				}
				$item["company_name"] = !empty($orgProp->value) ? trim($orgProp->value) : null;
				// load revision time info if available
				$revProp = $card->getProperty('REV');
				if(!empty($revProp)) {
					$d = date_create($revProp->value);
					if($d !== false) {
						$item["modified"] = $d->format("Y-m-d H:i");
					}
				}		
				$cards[] = $item;
			}
		}
		return $cards;
	}

}
?>