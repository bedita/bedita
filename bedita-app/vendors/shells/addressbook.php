<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2015 ChannelWeb Srl, Chialab Srl
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

require_once 'bedita_base.php';

/**
 * Shell script to import/export/manipulate cards.
 * 
 */
class AddressbookShell extends BeditaBaseShell {

    public function import() {
        if(!isset($this->params['f'])) {
            $this->out('Input file is mandatory');
            return;
        }

        $options = array('mailGroups' => array());
        $mail_group_id = null;
        $mailgroup = ClassRegistry::init('MailGroup');
        if (isset($this->params['m'])) {
            $mail_group_id = $mailgroup->field('id', array('group_name' => $this->params['m']));
            if (empty($mail_group_id)) {
                $this->out('Mail group ' . $this->params['m'] . ' not found: import aborted');
                return false;
            }
            $options['joinGroup'][0]['mail_group_id'] = $mail_group_id;
            $options['joinGroup'][0]['status'] = 'confirmed';
        } else {
            $allMailGroups = $mailgroup->find('all');
            if (!empty($allMailGroups)) {
                foreach ($allMailGroups as $mg) {
                    $options['mailGroups'][$mg['MailGroup']['group_name']] = $mg['MailGroup']['id'];
                }
            }
        }

        $cardFile = $this->params['f'];
        if(!file_exists($cardFile)) {
            $this->out("$cardFile not found, bye");
            return;
        }

        // categories
        if (!isset($this->params['c'])) {
            $this->out('No categories set');
        } else {
            $categories = trim($this->params['c']);
            $catTmp = split(',', $categories);
            $categoryModel = ClassRegistry::init('Category');
            $cardTypeId = Configure::read('objectTypes.card.id');
            $options['Category'] = $categoryModel->findCreateCategories($catTmp, $cardTypeId);
        }

        $ext = strtolower(substr($cardFile, strrpos($cardFile, ".")+1));
        $isCsv = ($ext == 'csv');
        $this->out("Importing file $cardFile using " . (($isCsv) ? 'CSV' : 'VCard') . ' format');
        
        if (isset($this->params['delimiter'])) {
            $options['delimiter'] = $this->params['delimiter'];
            $this->out('Using delimiter: ' . $options['delimiter']);
        }
        if (isset($this->params['overwritePolicy'])) {
            $options['overwritePolicy'] = $this->params['overwritePolicy'];
            $this->out('Using overwritePolicy: ' . $options['overwritePolicy']);
        }
        App::import('Component', 'Transaction');
        $transaction = new TransactionComponent();
        $cardModel = ClassRegistry::init('Card');
        if($isCsv) {
            $result = $cardModel->importCSVFile($cardFile, $options);
        } else {
            $result = $cardModel->importVCardFile($cardFile, $options);
        }
        $this->out('Done');
        $this->out('Result: ' . print_r($result, true));
        $transaction->commit();
    }

    public function export() {
        if (!isset($this->params['f'])) {
           $this->out('Output file is mandatory');
            return;
        }

        $cardFile = $this->params['f'];
        $this->checkExportFile($cardFile);

        $options = array();
        $type = 'vcard'; // default
        if (isset($this->params['t'])) {
            $type = strtolower($this->params['t']);
            if (!in_array($type, array('vcard', 'csv', 'custom'))) {
                $this->out("Unknown type $type");
                return;
            }
            if ($type == 'custom') {
                $options['custom'] = true;
            }
        } else {
            $ext = strtolower(substr($cardFile, strrpos($cardFile, '.')+1));
            if ($ext == 'csv') {
                $type = 'csv';
            }
        }
        $this->out("Exporting to $cardFile using '$type' format");
        if (isset($this->params['delimiter'])) {
            $options['delimiter'] = $this->params['delimiter'];
            $this->out('Using delimiter: ' . $options['delimiter']);
        }
        
        $cardModel = ClassRegistry::init('Card');
        $cardModel->contain();
        $res = $cardModel->find('all', array('fields' => array('id')));
        $handle = fopen($cardFile, 'w');
        if($type !== 'vcard') {
            fwrite($handle, $cardModel->headerCSV($options) . "\n");
        }
        foreach ($res as $r) {
            $cardModel->id = $r['id'];
            if($type !== 'vcard') {
                $str = $cardModel->exportCSV($options);
            } else {
                $str = $cardModel->exportVCard();
            }
            fwrite($handle, $str . "\n");
        }
        fclose($handle);
        $this->out("$cardFile created.");		
    }

	function help() {
        $this->out('Available functions:');
  		$this->out(' ');
        $this->out('1. import: import vcf/vcard or microsoft outlook csv file, or custom csv file');
  		$this->out(' ');
        $this->out('    Usage: import -f <csv-cardfile> [-c <categories>] [-m <mail-group-name>] [-delimiter <delimiter>] [-overwritePolicy <policy>]' );
  		$this->out(' ');
  		$this->out("    -f <csv-cardfile>\t vcf/vcard or csv file to import");
  		$this->out("    -c <categories> \t comma separated <categories> to use on import (created if not exist)");
  		$this->out("    -m <mail-group-name> \t name of mail group to associate with imported cards");
  		$this->out("    -delimiter <delimiter> \t CSV delimiter, default is ','");
  		$this->out("    -overwritePolicy <policy> \t 'skip'(default), 'overwrite', 'new'");
  		$this->out(' ');
        $this->out('2. export: export to vcf/vcard or microsoft outlook csv file');
  		$this->out(' ');
        $this->out('    Usage: export -f <csv-cardfile> [-t <type>] [-delimiter <delimiter>]');
  		$this->out(' ');
  		$this->out("    -f <csv-cardfile>\t vcf/vcard or csv file to export");
  		$this->out("    -t <type> \t 'vcard' (default), 'csv' or 'custom'");
  		$this->out("    -delimiter <delimiter> \t CSV delimiter, default is ','");
  		$this->out(' ');
    }

}
?>