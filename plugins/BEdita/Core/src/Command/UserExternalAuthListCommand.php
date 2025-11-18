<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Command;

use BEdita\Core\Model\Entity\User;
use BEdita\Core\Model\Enum\ObjectEntityStatus;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Query\SelectQuery;

/**
 * Command to list external authentication records for a user.
 */
class UserExternalAuthListCommand extends Command
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public static function defaultName(): string
    {
        return 'user:externalAuth list';
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser)
            ->setDescription('List user\'s external authentications')
            ->addOption('bedita-id', [
                'help' => 'Filter by BEdita user ID or uname',
            ])
            ->addOption('bedita-username', [
                'help' => 'Filter by BEdita user username',
            ])
            ->addOption('provider', [
                'short' => 'p',
                'help' => 'Filter by provider name',
            ]);
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $query = $this->fetchTable('ExternalAuth')
            ->find()
            ->contain(['AuthProviders', 'Users']);
        if ($args->getOption('provider') !== null) {
            $query = $query->find('authProvider', authProvider: $args->getOption('provider'));
        }
        $user = $this->getUser($args);
        if ($user !== null) {
            $query = $query->innerJoinWith(
                'Users',
                fn(SelectQuery $q): SelectQuery => $q->where(['Users.id' => $user->id]),
            );
        }

        $io->out(sprintf(
            '%8s | %24.24s | %24.24s | %24.24s | %32.32s',
            'ID',
            'BEdita username',
            'Provider',
            'Provider username',
            'Provider params',
        ));
        $io->hr(0, 124); // sum of all columns max widths + separators
        foreach ($query->all() as $externalAuth) {
            $io->out(sprintf(
                '%8d | %24.24s | %24.24s | %24.24s | %32.32s', // fixed min and max width for each column
                $externalAuth->id,
                $externalAuth->user->username,
                $externalAuth->auth_provider->name,
                $externalAuth->provider_username ?? '<emtpy>',
                $externalAuth->params !== null ? json_encode($externalAuth->params) : '<empty>',
            ));
        }

        return static::CODE_SUCCESS;
    }

    /**
     * Get user from command options.
     *
     * @param \Cake\Console\Arguments $args Command arguments
     * @return \BEdita\Core\Model\Entity\User|null
     */
    protected function getUser(Arguments $args): ?User
    {
        $query = $this->fetchTable('Users')->find()
            ->where([
                'status IN' => [ObjectEntityStatus::On->value, ObjectEntityStatus::Draft->value],
                'deleted' => false,
                'blocked' => false,
            ])
            ->contain(['ExternalAuth.AuthProviders']);
        $value = $args->getOption('bedita-id');
        $field = is_numeric($value) ? 'id' : 'uname';
        if (empty($value)) {
            $value = $args->getOption('bedita-username');
            $field = 'username';
        }
        if (empty($value)) {
            return null;
        }

        /** @var \BEdita\Core\Model\Entity\User $user */
        $user = $query->andWhere([$field => $value])
            ->firstOrFail();

        return $user;
    }
}
