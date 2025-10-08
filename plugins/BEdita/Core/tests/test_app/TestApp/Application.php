<?php
declare(strict_types=1);

namespace TestApp;

use BEdita\App\Application as BaseApplication;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
{
    /**
     * @inheritDoc
     */
    public function bootstrapCli(): void
    {
    }
}
