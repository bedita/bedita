<?php

/* -----8<--------------------------------------------------------------------
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
 * ------------------------------------------------------------------->8-----
 */

/**
 * CardsImportFilter: class to import card objects from CSV/vCard file
 *
 */
class CardsImportFilter extends BeditaImportFilter 
{

    protected $typeName = 'csv-vcard';
    protected $mimeTypes = array('text/csv', 'text/vcard');
    public $label = 'CSV or vCard';

    public $options = array(
        'overwritePolicy' => array(
            'label' => 'If a card with the same email already exists',
            'dataType' => 'options', // number|date|text|options
            'values' => array(
                'overwrite' => 'overwrite the card',
                'new' => 'create a new card',
                'skip' => 'skip'
            ),
            'defaultValue' => 'skip', // can be 'overwrite', 'new', 'skip'
            'mandatory' => true,
            'multipleChoice' => false
        )
        /*,
        'invalidEmail' => array(
            'label' => 'If an email is not valid',
            'dataType' => 'options', // number|date|text|options
            'values' => array(
                'import' => 'import',
                'skip' => 'skip'
            ),
            'defaultValue' => 'skip', // can be 'import', 'skip'
            'mandatory' => true,
            'multipleChoice' => false
        )
        */
    );

    /**
     * Import cards from CSV or vCard file
     * 
     * @param string $source, CSV / vCard file path
     * @param array $options, import options: 
     * @return array , result array containing 
     * 	'objects' => number of imported objects
     *  'message' => generic message (optional)
     *  'error' => error message (optional)
     * @throws BeditaException
     */
    public function import($source, array $options = array()) {

        $mailgroup = ClassRegistry::init('MailGroup');
        $allMailGroups = $mailgroup->find('all');
        if (!empty($allMailGroups)) {
            foreach ($allMailGroups as $mg) {
                $options['mailGroups'][$mg['MailGroup']['group_name']] = $mg['MailGroup']['id'];
            }
        }
        $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        $isCsv = ($ext == 'csv');
        if (Configure::read('csvFields.card')) {
            $options['delimiter'] = ';';
        }
        App::import('Component', 'Transaction');
        $transaction = new TransactionComponent();
        $cardModel = ClassRegistry::init('Card');
        if($isCsv) {
            $result = $cardModel->importCSVFile($source, $options);
        } else {
            $result = $cardModel->importVCardFile($source, $options);
        }
        $transaction->commit();
        return $result;
    }
};
