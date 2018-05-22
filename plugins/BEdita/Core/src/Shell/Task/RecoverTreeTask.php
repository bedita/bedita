<?php
namespace BEdita\Core\Shell\Task;

use Cake\Console\Shell;

/**
 * Shell task to recover tree.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\TreesTable $Trees
 */
class RecoverTreeTask extends Shell
{

    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Trees';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->setDescription('Recover tree from corruption.');

        return $parser;
    }

    /**
     * {@inheritDoc}
     */
    public function main()
    {
        $this->out('=====> <info>Beginning tree recovery...</info>');

        $start = microtime(true);
        $this->Trees->recover();
        $end = microtime(true);

        $this->out(sprintf('=====> <success>Tree recovery completed</success> (took <info>%f</info> seconds)', $end - $start));
    }
}
