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
namespace BEdita\Core\Model\Table;

use BEdita\Core\Model\Entity\Media;
use BEdita\Core\Model\Table\ObjectsBaseTable as Table;
use BEdita\Core\Model\Validation\MediaValidator;
use Cake\Event\EventInterface;

/**
 * Media Model
 *
 * @property \BEdita\Core\Model\Table\StreamsTable|\Cake\ORM\Association\HasMany $Streams
 * @method \BEdita\Core\Model\Entity\Media get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\Media newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\Media[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Media>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Media> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Media>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Media> deleteManyOrFail(iterable $entities, array $options = [])
 * @since 4.0.0
 */
class MediaTable extends Table
{
    /**
     * @inheritDoc
     */
    protected string $_validatorClass = MediaValidator::class;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('media');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');
        $this->getSchema()->setColumnType('provider_extra', 'json');

        $this->extensionOf('Objects');

        $this->getBehavior('UniqueName')->setConfig([
            'sourceField' => 'title',
            'prefix' => 'media-',
        ]);

        $this->hasMany('Streams', [
            'foreignKey' => 'object_id',
            'className' => 'BEdita/Core.Streams',
            'sort' => ['Streams.version' => 'DESC'],
        ]);

        $this->setupSimpleSearch([
            'fields' => [
                'title',
                'description',
                'body',
                'provider',
                'name',
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Cake\Event\EventInterface $event Dispatched event.
     * @param \BEdita\Core\Model\Entity\Media $entity Entity.
     * @return void
     */
    public function beforeDelete(EventInterface $event, Media $entity): void
    {
        if (!empty($entity->get('streams'))) {
            return;
        }
        $this->loadInto($entity, ['Streams']);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Cake\Event\EventInterface $event Dispatched event.
     * @param \BEdita\Core\Model\Entity\Media $entity Entity.
     * @return void
     */
    public function afterDelete(EventInterface $event, Media $entity): void
    {
        $streams = (array)$entity->get('streams');
        foreach ($streams as $stream) {
            $this->Streams->deleteOrFail($stream);
        }
    }
}
