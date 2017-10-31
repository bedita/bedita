<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Shell\Task;

use BEdita\Core\Model\Table\ApplicationsTable;
use Cake\Console\Shell;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Task to setup API key.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\ApplicationsTable $Applications
 */
class CheckApiKeyTask extends Shell
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->setDescription([
                'Setup API key.',
            ]);

        return $parser;
    }

    /**
     * {@inheritDoc}
     */
    public function main()
    {
        $this->loadModel('Applications');

        try {
            $this->verbose('=====> Loading default application... ', 0);
            $application = $this->Applications->get(ApplicationsTable::DEFAULT_APPLICATION);
            $this->verbose('<info>DONE</info>');
        } catch (RecordNotFoundException $e) {
            $this->verbose('<error>FAIL</error>');
            $this->abort('Default application is missing, please check your installation');

            return false;
        }

        if (empty($application->api_key)) {
            $this->out('=====> <warning>Default application has no API key</warning>');

            return false;
        }

        $this->out(sprintf('=====> Default API key is: <info>%s</info>', $application->api_key));
        $this->out('=====> <success>API key is ok. You can now make your requests even more handsome with it!</success>');

        return true;
    }
}
