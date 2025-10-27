<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * CheckFilesystem command.
 */
class CheckFilesystemCommand extends Command
{
    use LocatorAwareTrait;

    /**
     * Console arguments
     *
     * @var \Cake\Console\Arguments
     */
    protected $args;

    /**
     * Console IO
     *
     * @var \Cake\Console\ConsoleIo
     */
    protected $io;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->setName('cake check_filesystem');
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription([
                'Check filesystem permissions.',
            ])
            ->addArgument('paths ...', [
                'help' => 'List of directories to check if they are writable.',
                'required' => false,
            ])
            ->addOption('httpd-user', [
                'help' => 'Manually set HTTPD user, instead of relying on automatic detection.',
                'required' => false,
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Check Filesystem';
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $this->args = $args;
        $this->io = $io;

        // Load paths to be checked.
        $paths = $this->args->getArguments('paths ...');
        $paths = array_unique(array_filter($paths) ?: [TMP, LOGS]);

        // Detect HTTP daemon user.
        $httpdUser = $this->getHttpdUser();
        if (empty($httpdUser)) {
            $this->io->out('=====> <warning>Unable to detect webserver user</warning>');

            return static::CODE_ERROR;
        }

        // Check that paths are writable by HTTPD user.
        if (!$this->checkPaths($paths, $httpdUser)) {
            $this->io->out('=====> <warning>Potential issues were found, please check your installation</warning>');

            return static::CODE_ERROR;
        }

        $this->io->out('=====> <success>Filesystem permissions look alright. Time to write something in those shiny folders!</success>');

        return static::CODE_SUCCESS;
    }

    /**
     * Get or detect HTTPD user name.
     *
     * @return string
     */
    protected function getHttpdUser(): string
    {
        static $cmd = 'ps aux | grep -E "[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx" | grep -v root | head -1 | cut -d\\  -f1';
        $httpdUser = $this->args->getOption('httpd-user');
        if (!empty($httpdUser)) {
            return $httpdUser;
        }

        $this->io->verbose('=====> Trying to detect HTTPD user');
        $this->io->verbose(sprintf('=====> <comment>%s</comment>', $cmd));
        $httpdUser = exec($cmd);

        return $httpdUser;
    }

    /**
     * Check that paths
     *
     * @param string[] $paths List of paths to check.
     * @param string $user Name of user to check permissions for.
     * @return bool
     */
    protected function checkPaths(array $paths, $user): bool
    {
        // Get info about HTTP daemon user.
        $user = posix_getpwnam($user);
        $group = posix_getgrgid($user['gid']);
        $this->io->verbose(
            sprintf('=====> Detected webserver user: <info>%s</info> (ID: <info>%d</info>)', $user['name'], $user['uid'])
        );
        $this->io->verbose(
            sprintf('=====> Detected webserver group: <info>%s</info> (ID: <info>%d</info>)', $group['name'], $group['gid'])
        );

        // Check paths.
        $ok = true;
        foreach ($paths as $path) {
            // Basic checks.
            $this->io->verbose(sprintf('=====> Checking directory <comment>%s</comment>', $path));
            if (!is_dir($path)) {
                $this->io->abort(sprintf('Path "%s" does not exist or is not a directory', $path));
            }
            if (!is_writable($path)) {
                $ok = false;
                $this->io->out(sprintf('=====> <warning>Path "%s" might not be writable by CLI user</warning>', $path));
            }

            // Obtain info about owner user and group.
            $ownerUser = posix_getpwuid(fileowner($path));
            $ownerGroup = posix_getgrgid(filegroup($path));
            $this->io->verbose(
                sprintf('=====> Detected owner user: <info>%s</info> (ID: <info>%d</info>)', $ownerUser['name'], $ownerUser['uid'])
            );
            $this->io->verbose(
                sprintf('=====> Detected owner group: <info>%s</info> (ID: <info>%d</info>)', $ownerGroup['name'], $ownerGroup['gid'])
            );

            // Check permissions for owner. Might not be accurate, but it might help spot issues.
            $perms = fileperms($path);
            if (($perms & 07) === 07) {
                $this->io->out(sprintf('=====> <warning>Path "%s" is world writable!</warning>', $path));

                continue;
            }
            if (($ownerUser['uid'] !== $user['uid'] || (($perms >> 6) & 07) !== 07) && ($ownerGroup['gid'] !== $group['gid'] || (($perms >> 3) & 07) !== 07)) {
                $ok = false;
                $this->io->out(sprintf('=====> <warning>Path "%s" might not be writable by webserver user</warning>', $path));
            }
            $this->io->verbose(sprintf('=====> <info>Path "%s" is writable by webserver user</info>', $path));
        }

        return $ok;
    }
}
