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
namespace BEdita\Core\Test\TestCase\Event;

use BEdita\Core\Event\ImageThumbsHandler;
use BEdita\Core\Filesystem\Thumbnail;
use BEdita\Core\Filesystem\ThumbnailGenerator;
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Model\Entity\Stream;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\TestSuite\TestCase;
use Cake\Utility\Text;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Event\ImageThumbsHandler} Test Case
 */
#[CoversClass(ImageThumbsHandler::class)]
class ImageThumbsHandlerTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
    ];

    /**
     * Test `implementedEvents` method
     *
     * @return void
     */
    public function testImplementedEvents(): void
    {
        static::assertCount(0, EventManager::instance()->listeners('Associated.afterSave'));
        EventManager::instance()->on(new ImageThumbsHandler());
        static::assertCount(1, EventManager::instance()->listeners('Associated.afterSave'));
    }

    /**
     * Data provider for `testAfterSaveAssociated` test case.
     *
     * @return array
     */
    public static function afterSaveAssociatedProvider(): array
    {
        return [
            'noStream' => [
                [
                    'entity' => null,
                ],
                false,
            ],
            'noImages' => [
                [
                    'entity' => static fn(self $testCase) => $testCase->getMockBuilder(Stream::class)->getMock(),
                    'relatedEntities' => [],
                ],
                false,
            ],
            'stream and images' => [
                [
                    'entity' => static fn(self $testCase) => $testCase->getMockBuilder(Stream::class)->getMock(),
                    'relatedEntities' => static function (self $testCase) {
                        $image = $testCase->getMockBuilder(ObjectEntity::class)
                            ->onlyMethods(['get'])
                            ->getMock();
                        $image->method('get')->willReturn('images');

                        return [$image];
                    },
                ],
                true,
            ],
        ];
    }

    /**
     * Test `afterSaveAssociated` method
     *
     * @param array $data Event data.
     * @param bool $updateThumbsIsCalled If `updateThumbs` method is called.
     * @return void
     */
    #[DataProvider('afterSaveAssociatedProvider')]
    public function testAfterSaveAssociated(array $data, bool $updateThumbsIsCalled): void
    {
        $handler = new class extends ImageThumbsHandler {
            public $called = false;
            public function updateThumbs(ObjectEntity $image, Stream $stream, array $presets): void
            {
                $this->called = true;
            }
        };

        $data = array_map(
            fn($value) => is_callable($value) ? $value($this) : $value,
            $data,
        );

        $event = new Event('Associated.afterSave', $this, $data);
        $handler->afterSaveAssociated($event);
        static::assertEquals($updateThumbsIsCalled, $handler->called);
    }

    /**
     * Test `updateThumbs` method
     *
     * @return void
     */
    public function testUpdateThumbs(): void
    {
        $handler = new ImageThumbsHandler();

        $stream = new Stream(['uuid' => Text::uuid()]);
        $mock = $this->getMockBuilder(ThumbnailGenerator::class)
            ->onlyMethods(['getUrl', 'exists', 'generate', 'delete'])
            ->getMock();
        $mock->expects(static::once())
            ->method('getUrl')
            ->with($stream, [])
            ->willReturn('https://assets.example.org/thumbnail.jpg');
        $mock->expects(static::once())
            ->method('exists')
            ->with($stream, [])
            ->willReturn(true);

        Thumbnail::setConfig('test', ['className' => $mock]);
        $image = $this->fetchTable('Images')->newEmptyEntity();
        Configure::write('Thumbnails.allowAny', true);
        LoggedUser::setUserAdmin();
        $handler->updateThumbs($image, $stream, ['gustavo' => ['generator' => 'test']]);

        $image = $this->fetchTable('Images')->get(21);
        $extra = (array)$image->get('extra');
        static::assertNotEmpty($extra);
        static::assertNotEmpty($extra['thumbs']['gustavo']);
        Thumbnail::drop('test');
        Configure::delete('Thumbnails.allowAny');
        LoggedUser::resetUser();
    }
}
