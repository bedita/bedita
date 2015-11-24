<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
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
 * Convert Date Items to another format.
 */
class MigrationDateItemsShell extends BeditaBaseShell
{
    /**
     * Models used by this shell.
     *
     * @var array
     */
    public $uses = array('DateItem');

    /**
     * Columns in the SQL table.
     *
     * @var array
     */
    protected $columns = array();

    /**
     * Conversion(s) to perform.
     *
     * @var array
     */
    protected $conversion = array();

    public function help() {
        $this->out('Conversion script to handle conversion of Date Items from DATETIME format');
        $this->out('to BIGINT and vice versa.');
        $this->out();

        $this->out('First, you must run the first part of the appropriate conversion SQL.');
        $this->out('You can find them in the bedita-app/config/sql/ directory.');
        $this->out();

        $this->out('CAREFUL! Do NOT blindly launch this SQL, or you might lose data!');
        $this->out('PLEASE READ the instructions therein!');
        $this->out();

        $this->out('Then, run this script to convert not-yet-converted items (or all items');
        $this->out('if you pass the --all flag). Finally, run the last two queries in the');
        $this->out('same SQL conversion script as before.');
        $this->out();

        $this->out('  Usage: migration_date_items [-a|--all]');
        $this->out("    --all, -a\tAlways convert ALL date items in database");
    }

    /**
     * Load informations about SQL table columns.
     */
    public function startup() {
        parent::startup();

        $keys = array('start_date', 'end_date', 'start_date_tmp', 'end_date_tmp');

        $this->columns = array_intersect_key($this->DateItem->getColumnTypes(), array_flip($keys));
        $this->columns = array_map('strtolower', $this->columns);

        $this->conversion = array();
        foreach (array('start', 'end') as $startEnd) {
            if (empty($this->columns[$startEnd . '_date'])) {
                $this->error("Missing required column `{$startEnd}_date`! How is it possible that BEdita is running???");
            }
            if (!empty($this->columns[$startEnd . '_date_tmp'])) {
                array_push($this->conversion, $startEnd);
            }
        }
    }

    protected function load() {
        if (array_key_exists('-all', $this->params) || array_key_exists('a', $this->params)) {
            return $this->DateItem->find('list');
        }

        $conditions = array();

        if (count($this->conversion) == 1) {
            $startEnd = reset($this->conversion);
            $conditions['AND'] = array(
                $startEnd . '_date <>' => null,
                $startEnd . '_date_tmp' => null,
            );
        } else {
            $conditions['OR'] = array(
                'AND' => array(
                    'start_date <>' => null,
                    'start_date_tmp' => null,
                ),
                'OR' => array(
                    '1 <> 1',
                    'AND' => array(
                        'end_date <>' => null,
                        'end_date_tmp' => null,
                    )
                ),
            );
        }

        return $this->DateItem->find('list', compact('conditions'));
    }

    /**
     * Convert a single value.
     *
     * @param mixed $value Value to be converted.
     * @param string $startEnd Either `start` or `end`.
     * @return mixed Converted value.
     */
    protected function convert($value, $startEnd) {
        if (!in_array($startEnd, array('start', 'end'))) {
            return false;
        }

        if ($this->columns[$startEnd . '_date'] == $this->columns[$startEnd . '_date_tmp']) {
            return $value;
        }

        if (is_null($value)) {
            return null;
        }

        if ($this->columns[$startEnd . '_date'] == 'integer') {
            if (!is_numeric($value)) {
                $value = strtotime($value) ?: null;
            }
            return date('Y-m-d H:i:s', $value);
        }

        if ($this->columns[$startEnd . '_date'] == 'datetime') {
            return strtotime($value) ?: null;
        }

        return $value;
    }

    /**
     * Convert all date items in the database.
     */
    public function main() {
        if (empty($this->conversion)) {
            $this->error('No conversion to be performed!');
        }

        $this->out(' - Loading data... ', 0);
        $dateItems = $this->load();
        $this->out('DONE');

        if (empty($dateItems)) {
            $this->out(' * No items to be converted');
            return;
        }

        $this->out(' - Converting...');
        $count = 0;
        $time = microtime(true);
        foreach ($dateItems as $id) {
            $ok = true;
            $this->DateItem->id = $id;
            foreach ($this->conversion as $startEnd) {
                $value = $this->DateItem->read($startEnd . '_date', $id);
                $value = $value['DateItem'][$startEnd . '_date'];
                $value = $this->convert($value, $startEnd);
                $ok = $this->DateItem->saveField($startEnd . '_date_tmp', $value) && $ok;
            }

            $count++;
            $this->out($ok ? '.' : 'x', ($count % 80 == 0) ? 1 : 0);
        }
        $this->out();
        $time = microtime(true) - $time;

        $this->out(' - Converted ' . count($dateItems) . ' items in ' . sprintf('%.3f', $time) . ' seconds');
    }
}
