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

namespace BEdita\Core\I18n;

use Cake\Core\Plugin;
use Cake\I18n\MessagesFileLoader as BaseLoader;
use Locale;

/**
 * Loader for translation messages.
 *
 * @see \Cake\I18n\MessagesFileLoader
 *
 * @since 4.0.0
 */
class MessagesFileLoader extends BaseLoader
{

    /**
     * List of plugins where lookup should happen for the given domain.
     *
     * @var string[]
     */
    protected $plugins = [];

    /**
     * {@inheritDoc}
     *
     * @param string[] $plugins Additional plugins to look up in for translations.
     */
    public function __construct($name, $locale, $extension = 'po', array $plugins = [])
    {
        parent::__construct($name, $locale, $extension);

        $this->plugins = $plugins;
    }

    /**
     * {@inheritDoc}
     */
    public function translationsFolders()
    {
        $searchPaths = parent::translationsFolders();

        $locale = Locale::parseLocale($this->_locale) + ['region' => null];
        $folders = [
            implode('_', [$locale['language'], $locale['region']]),
            $locale['language']
        ];
        foreach ($this->plugins as $pluginName) {
            if (Plugin::loaded($pluginName)) {
                $basePath = Plugin::classPath($pluginName) . 'Locale' . DIRECTORY_SEPARATOR;
                foreach ($folders as $folder) {
                    $searchPaths[] = $basePath . $folder . DIRECTORY_SEPARATOR;
                }
            }
        }

        return $searchPaths;
    }
}
