<?php
namespace BEdita\Core\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Command to recover tree.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\TreesTable $Trees
 */
class TreeRecoverCommand extends Command
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
    public static function defaultName(): string
    {
        return 'tree recover';
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser)
            ->setDescription('Recover objects\' tree from corruption.');
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console I/O.
     * @return int Exit code.
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $io->out('=====> <info>Beginning tree recovery...</info>');

        $start = microtime(true);
        $this->Trees->recover();
        $end = microtime(true);

        $io->out(sprintf('=====> <success>Tree recovery completed</success> (took <info>%f</info> seconds)', $end - $start));

        return static::CODE_SUCCESS;
    }
}
