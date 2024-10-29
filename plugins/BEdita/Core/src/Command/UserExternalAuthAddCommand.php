<?php
declare(strict_types=1);

namespace BEdita\Core\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Command to add external authentication for a user.
 */
class UserExternalAuthAddCommand extends UserExternalAuthListCommand
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public static function defaultName(): string
    {
        return 'user:externalAuth add';
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser)
            ->setDescription('Add user\'s external authentications')
            ->addOption('provider-username', [
                'help' => 'Set provider username',
            ])
            ->addOption('provider-params', [
                'help' => 'Set provider parameters',
            ]);
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        if (empty($args->getOption('provider'))) {
            $io->error('Option "--provider" is required.');

            return static::CODE_ERROR;
        }
        if (empty($args->getOption('provider-username'))) {
            $io->error('Option "--provider-username" is required.');

            return static::CODE_ERROR;
        }

        $user = $this->getUser($args);
        if ($user === null) {
            $io->error('One option between "--bedita-id" or "--bedita-username" must be provided.');

            return static::CODE_ERROR;
        }

        /** @var \BEdita\Core\Model\Entity\AuthProvider $provider */
        $provider = $this->fetchTable('AuthProviders')
            ->find('enabled')
            ->where(['name' => $args->getOption('provider')])
            ->firstOrFail();
        $ExternalAuth = $this->fetchTable('ExternalAuth');
        $entity = $ExternalAuth->newEntity([
            'user_id' => $user->id,
            'auth_provider_id' => $provider->id,
            'params' => $args->getOption('provider-params')
                ? json_decode($args->getOption('provider-params'), true, 512, JSON_THROW_ON_ERROR)
                : null,
            'provider_username' => $args->getOption('provider-username'),
        ]);
        $ExternalAuth->saveOrFail($entity);
        $io->out(sprintf(
            'Added user "%s" external auth for provider "%s"',
            $user->username,
            $provider->name,
        ));

        return static::CODE_SUCCESS;
    }
}
