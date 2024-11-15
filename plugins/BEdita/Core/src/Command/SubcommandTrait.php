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

trait SubcommandTrait
{
    /**
     * Execute subcommand.
     *
     * @param string $subcommand Subcommand to execute.
     * @return int
     */
    protected function executeSubcommand(string $subcommand): int
    {
        $subcommandArguments = [];
        $allowedArguments = $this->subcommands[$subcommand]['arguments'];
        foreach ($allowedArguments as $argumentName) {
            $subcommandArguments[] = $this->args->getArgument($argumentName);
        }
        $allowedOptions = $this->subcommands[$subcommand]['options'];
        foreach ($this->args->getOptions() as $option => $value) {
            if (in_array($option, $allowedOptions)) {
                $subcommandArguments[] = sprintf('--%s', $option);
                $subcommandArguments[] = $value;
            }
        }

        return $this->executeCommand($this->subcommands[$subcommand]['class'], $subcommandArguments, $this->io);
    }
}
