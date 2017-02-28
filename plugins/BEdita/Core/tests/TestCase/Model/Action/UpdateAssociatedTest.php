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

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\UpdateAssociated;
use Cake\ORM\Association\BelongsToMany;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Action\UpdateAssociated
 */
class UpdateAssociatedTest extends TestCase
{

    /**
     * Test getter for association.
     *
     * @return void
     *
     * @covers ::getAssociation()
     */
    public function testGetAssociation()
    {
        $association = new BelongsToMany('MyAssociation');

        $action = $this->getMockForAbstractClass(UpdateAssociated::class, [$association]);

        static::assertAttributeSame($association, 'Association', $action);
        static::assertSame($association, $action->getAssociation());
    }

    /**
     * Test setter for association.
     *
     * @return void
     *
     * @covers ::setAssociation()
     */
    public function testSetAssociation()
    {
        $association = new BelongsToMany('MyAssociation');

        $action = $this->getMockForAbstractClass(UpdateAssociated::class, [new BelongsToMany('AnotherAssociation')]);

        $result = $action->setAssociation($association);

        static::assertAttributeSame($association, 'Association', $action);
        static::assertSame($action, $result);
    }
}
