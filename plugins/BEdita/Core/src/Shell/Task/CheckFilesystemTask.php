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

use Cake\Console\Shell;

/**
 * Task to check permissions on filesystem.
 *
 * @since 4.0.0
 */
class CheckFilesystemTask extends Shell
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

        return $parser;
    }

    /**
     * Perform basic checks on filesystem.
     *
     * @param string[] ...$paths List of paths to check if they are writable.
     * @return bool
     */
    public function main(...$paths)
    {
        // Load paths to be checked.
        $paths = array_unique(array_filter($paths) ?: [TMP, LOGS]);

        // Detect HTTP daemon user.
        $httpdUser = $this->getHttpdUser();
        if (empty($httpdUser)) {
            $this->out('=====> <warning>Unable to detect webserver user</warning>');

            return false;
        }

        // Check that paths are writable by HTTPD user.
        if (!$this->checkPaths($paths, $httpdUser)) {
            $this->out('=====> <warning>Potential issues were found, please check your installation</warning>');

            return false;
        }

        $this->out('=====> <success>Filesystem permissions look alright. Time to write something in those shiny folders!</success>');

        return true;
    }

    /**
     * Get or detect HTTPD user name.
     *
     * @return string
     */
    protected function getHttpdUser()
    {
        static $cmd = 'ps aux | grep -E "[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx" | grep -v root | head -1 | cut -d\\  -f1';

        $httpdUser = $this->param('httpd-user');
        if (!empty($httpdUser)) {
            return $httpdUser;
        }

        $this->verbose('=====> Trying to detect HTTPD user');
        $this->verbose(sprintf('=====> <comment>%s</comment>', $cmd));
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
    protected function checkPaths(array $paths, $user)
    {
        // Get info about HTTP daemon user.
        $user = posix_getpwnam($user);
        $group = posix_getgrgid($user['gid']);
        $this->verbose(
            sprintf('=====> Detected webserver user: <info>%s</info> (ID: <info>%d</info>)', $user['name'], $user['uid'])
        );
        $this->verbose(
            sprintf('=====> Detected webserver group: <info>%s</info> (ID: <info>%d</info>)', $group['name'], $group['gid'])
        );

        // Check paths.
        $ok = true;
        foreach ($paths as $path) {
            // Basic checks.
            $this->verbose(sprintf('=====> Checking directory <comment>%s</comment>', $path));
            if (!is_dir($path)) {
                $this->abort(sprintf('Path "%s" does not exist or is not a directory', $path));
            }
            if (!is_writable($path)) {
                $ok = false;
                $this->out(sprintf('=====> <warning>Path "%s" might not be writable by CLI user</warning>', $path));
            }

            // Obtain info about owner user and group.
            $ownerUser = posix_getpwuid(fileowner($path));
            $ownerGroup = posix_getgrgid(filegroup($path));
            $this->verbose(
                sprintf('=====> Detected owner user: <info>%s</info> (ID: <info>%d</info>)', $ownerUser['name'], $ownerUser['uid'])
            );
            $this->verbose(
                sprintf('=====> Detected owner group: <info>%s</info> (ID: <info>%d</info>)', $ownerGroup['name'], $ownerGroup['gid'])
            );

            // Check permissions for owner. Might not be accurate, but it might help spot issues.
            $perms = fileperms($path);
            if (($perms & 07) === 07) {
                $this->out(sprintf('=====> <warning>Path "%s" is world writable!</warning>', $path));

                continue;
            }
            if (($ownerUser['uid'] !== $user['uid'] || (($perms >> 6) & 07) !== 07) && ($ownerGroup['gid'] !== $group['gid'] || (($perms >> 3) & 07) !== 07)) {
                $ok = false;
                $this->out(sprintf('=====> <warning>Path "%s" might not be writable by webserver user</warning>', $path));
            }
            $this->verbose(sprintf('=====> <info>Path "%s" is writable by webserver user</info>', $path));
        }

        return $ok;
    }
}
