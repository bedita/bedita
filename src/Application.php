<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\App;

use BEdita\API\App\BaseApplication;
use Cake\Core\Configure;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
{
    /**
     * @inheritDoc
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        // Load more plugins here
        $this->addPlugin('BEdita/Core');
        $this->addPlugin('BEdita/API');
        $this->addConfigPlugins();
    }

    /**
     * @inheritDoc
     */
    protected function bootstrapCli(): void
    {
        parent::bootstrapCli();
        if (Configure::read('debug')) {
            $this->addOptionalPlugin('Cake/Repl');
        }

        $this->addOptionalPlugin('IdeHelper');
    }
}
