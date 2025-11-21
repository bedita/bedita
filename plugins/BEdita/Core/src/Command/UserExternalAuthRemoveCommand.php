<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 Channelweb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Command;

use BEdita\Core\Model\Entity\ExternalAuth;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Command to remove external authentication for a user.
 *
 * @property \BEdita\Core\Model\Table\UsersTable $Users
 */
class UserExternalAuthRemoveCommand extends UserExternalAuthListCommand
{
    /**
     * @inheritDoc
     */
    protected ?string $defaultTable = 'Users';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public static function defaultName(): string
    {
        return 'user:externalAuth remove';
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser)
            ->setDescription('Remove user\'s external authentications')
            ->addOption('external-id', [
                'help' => 'Remove by record ID',
            ])
            ->removeOption('provider')
            ->addOption('provider', [
                'short' => 'p',
                'help' => 'Remove by provider name',
            ]);
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $user = $this->getUser($args);
        if ($user === null) {
            $io->error('One option between "--bedita-id" or "--bedita-username" must be provided.');

            return static::CODE_ERROR;
        }

        $value = $args->getOption('external-id');
        $field = 'id';
        if (empty($value) && !empty($args->getOption('provider'))) {
            $value = $this->fetchTable('AuthProviders')
                ->find()
                ->where(['name' => $args->getOption('provider')])
                ->firstOrFail()
                ->id;
            $field = 'auth_provider_id';
        }
        if (empty($value)) {
            $io->error('One option between "--external-id" or "--provider" must be provided.');

            return static::CODE_ERROR;
        }

        /** @var \BEdita\Core\Model\Entity\ExternalAuth|false $externalAuth */
        $externalAuth = current(array_filter(
            $user->external_auth,
            fn(ExternalAuth $auth) => $auth->{$field} == $value,
        ));
        if ($externalAuth === false) {
            $io->error('External auth record not found.');

            return static::CODE_ERROR;
        }

        $this->fetchTable('ExternalAuth')->deleteOrFail($externalAuth);
        $io->out(sprintf(
            'Removed user "%s" external auth for provider "%s"',
            $user->username,
            $externalAuth->auth_provider->name,
        ));

        return static::CODE_SUCCESS;
    }
}
