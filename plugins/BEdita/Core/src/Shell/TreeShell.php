<?php
namespace BEdita\Core\Shell;

use BEdita\Core\Shell\Task\CheckTreeTask;
use BEdita\Core\Shell\Task\RecoverTreeTask;
use Cake\Console\Shell;

/**
 * Trees shell command.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Shell\Task\RecoverTreeTask $Recover
 * @property \BEdita\Core\Shell\Task\CheckTreeTask $Check
 */
class TreeShell extends Shell
{

    /**
     * {@inheritDoc}
     */
    public $tasks = [
        'Recover' => ['className' => RecoverTreeTask::class],
        'Check' => ['className' => CheckTreeTask::class],
    ];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->addSubcommand('recover', [
                'help' => 'Recover objects\' tree from corruption.',
                'parser' => $this->Recover->getOptionParser(),
            ])
            ->addSubcommand('check', [
                'help' => 'Objects-aware sanity checks on tree.',
                'parser' => $this->Check->getOptionParser(),
            ]);

        return $parser;
    }
}
