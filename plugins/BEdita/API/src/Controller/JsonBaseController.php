<?php
namespace BEdita\API\Controller;

use BEdita\API\Controller\AppController;

/**
 * Base class for controllers handling pure `application/json` content-type, not using JSON API
 *
 */
abstract class JsonBaseController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();
        if ($this->components()->has('JsonApi')) {
            $this->components()->unload('JsonApi');
        }
        $this->viewBuilder()->setClassName('Json');
        $this->RequestHandler->setConfig('viewClassMap.json', 'Json');

        if ($this->request->contentType() === 'application/json') {
            $this->RequestHandler->setConfig('inputTypeMap.json', ['json_decode', true], false);
        }
    }
}
