<?php
declare(strict_types=1);

namespace BEdita\API\Controller;

use BEdita\API\View\JsonApiView;
use Cake\Controller\ErrorController as CakeErrorController;

/**
 * Error Handling Controller
 *
 * Extends Cake ErrorController to explicitly use JsonApiView for render errors.
 */
class ErrorController extends CakeErrorController
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function viewClasses(): array
    {
        return [JsonApiView::class];
    }
}
