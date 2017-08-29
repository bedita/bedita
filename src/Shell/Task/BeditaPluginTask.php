<?php
namespace BEdita\App\Shell\Task;

use Bake\Shell\Task\PluginTask as BakeTask;

/**
 * The BEdita Plugin Task handles creating an empty BEdita4 plugin, ready to be used
 */
class BeditaPluginTask extends BakeTask
{
    /**
     * {@inheritDoc}
     */
    protected function _modifyBootstrap($plugin, $hasAutoloader)
    {
    }
}
