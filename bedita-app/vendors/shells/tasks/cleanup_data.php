<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2022 Atlas Srl, Chialab Srl
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
 * Cleanup data task
 * 
 * @property \BEobject $BEobject
 */
class CleanupDataTask extends BeditaBaseShell {
    
    /**
     * @inheritDoc
     */
    public $uses = ['BEObject'];

    protected $truncateTables = ['event_logs', 'mail_jobs', 'mail_logs'];

    protected $cleanupTables = ['history', 'versions'];

    public function help() {
        $this->hr();
        $this->out('cleanup data script shell usage:');
        $this->out('');
        $this->out('./cake.sh cleanupData // truncate event_logs, mail_jobs, mail_logs, clean old data from history and versions');
        $this->out('./cake.sh cleanupData -ld <limit date yyyy-MM-dd> // use custom limit date for clean');
        $this->out('./cake.sh cleanupData -ni // no interactive mode');
        $this->out('./cake.sh cleanupData -tt <comma separated table names> // truncate event_logs, mail_jobs, mail_logs + more tables');
        $this->out('./cake.sh cleanupData help // show this help');
        $this->out('');
    }

    /**
     * Tables to clean (truncate or delete data):
     * - event_logs (truncate)
     * - mail_jobs (truncate, if no messages pending)
     * - mail_logs (truncate)
     * - history (cleanup: mantain only "CY-1 / CY" data (CY is the Current Year))
     * - versions (cleanup: mantain only "CY-1 / CY" data (CY is the Current Year))
     *
     * @return void
     */
    public function execute()
    {
        $this->hr();
        $this->out('BEdita Data Cleanup');
        $this->out('--- count data before cleaning ---');
        $tables = $this->truncateTables;
        if (isset($this->params['tt'])) {
            $tables = array_merge($tables, explode(',', $this->params['tt']));
        }
        $countTables = array_merge($this->cleanupTables, $tables);
        sort($countTables);
        $limitDate = date('Y')-2 . '-12-31 23:59:59';
		if (isset($this->params['ld'])) {
			$limitDate = $this->params['ld'] . ' 23:59:59';
        }
        $limitWhere = sprintf('WHERE created < \'%s\'', $limitDate);
        foreach ($countTables as $tableName) {
            $this->countRecords($tableName);
            $this->countRecords($tableName, $limitWhere);
        }
        // check if mail_jobs can be truncated
        $query = 'SELECT COUNT(*) AS n FROM mail_jobs WHERE status NOT IN (\'sent\', \'failed\')';
        $result = $this->BEObject->query($query);
        $pending = $result[0][0]['n'];
        $this->out(sprintf('%s => %s', $query, $pending));
        if ($pending > 0) {
            $this->out('mail_jobs won\'t be truncated, because there are pending jobs. cleanup instead');
            if (($key = array_search('mail_jobs', $tables)) !== false) {
                unset($tables[$key]);
            }
            $this->cleanupTables[] = 'mail_jobs';
        }

        $this->out(sprintf('Truncate tables "%s"', implode(',', $tables)));
        $this->out(sprintf('Remove from tables "%s" records created before date limit: %s', implode(',', $this->cleanupTables), $limitDate));
        if (!isset($this->params['ni'])) { // no interactive
            $res = $this->in('Continue? [y/n]');
            if ($res != 'y') {
                $this->out('Bye');

                return;
            }    
        }
        $this->out('--- clean data ---');
        foreach ($this->cleanupTables as $tableName) {
            $this->cleanupTable($tableName, $limitDate);
        }
        foreach ($tables as $tableName) {
            $this->truncateTable($tableName);
        }
        $this->hr();
        $this->out('Done');        
    }

    protected function countRecords($tableName, $where = '')
    {
        $query = trim(sprintf('SELECT COUNT(*) AS n FROM %s %s', $tableName, $where));
        $result = $this->BEObject->query($query);
        $this->out(sprintf('%s => %s', $query, $result[0][0]['n']));
    }

    protected function truncateTable($tableName)
    {
        $query = sprintf('TRUNCATE table %s', $tableName);
        $this->out($query);
        $this->BEObject->query($query);
    }

    protected function cleanupTable($tableName, $limitDate)
    {
        $query = sprintf('DELETE FROM %s WHERE created < \'%s\'', $tableName, $limitDate);
        $this->out($query);
        $this->BEObject->query($query);
    }
}