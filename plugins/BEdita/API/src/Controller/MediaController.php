<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Controller;

use BEdita\Core\Filesystem\Thumbnail;
use BEdita\Core\Model\Entity\Stream;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;
use Cake\Utility\Hash;

/**
 * Controller for media.
 *
 * @since 4.0.0
 */
class MediaController extends ObjectsController
{

    /**
     * Get and validate IDs in request URL.
     *
     * @return int[]
     */
    protected function getIds()
    {
        $id = $this->request->getParam('id');
        $ids = $this->request->getQuery('ids');
        if ($id !== false) {
            if ($ids !== null) {
                throw new BadRequestException(__d('bedita', 'Cannot specify IDs in both path and query string'));
            }

            $ids = [$id];
        } elseif (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $validateOptions = [
            'default' => null,
            'min_range' => 1,
        ];
        $ids = array_unique(
            array_filter(
                array_map(
                    function ($id) use ($validateOptions) {
                        return filter_var(trim($id), FILTER_VALIDATE_INT, ['options' => $validateOptions]);
                    },
                    $ids
                )
            )
        );

        $maxLimit = $this->Paginator->getConfig('maxLimit');
        if (count($ids) > $maxLimit) {
            throw new BadRequestException(__d('bedita', 'Cannot generate thumbnails for more than {0} media at once', $maxLimit));
        }

        return $this->getAvailableIds($ids);
    }

    /**
     * Retrieve actual available IDs checking `status` and `deleted`
     *
     * @param array $ids Object ids array
     * @return array
     */
    protected function getAvailableIds(array $ids) : array
    {
        if (empty($ids)) {
            return $ids;
        }
        $available = $this->Table->find('available')
            ->where(['id IN' => $ids])
            ->select(['id'])
            ->toArray();

        return (array)Hash::extract($available, '{n}.id');
    }

    /**
     * Generate thumbnail for one or more media.
     *
     * @return void
     */
    public function thumbs()
    {
        $this->request->allowMethod(['get']);

        $ids = $this->getIds();
        if (empty($ids)) {
            throw new BadRequestException(__d('bedita', 'Missing IDs to generate thumbnails for'));
        }

        $preset = $this->request->getQuery('preset');
        $options = (array)$this->request->getQuery('options');

        $thumbnails = $this->Table->getAssociation('Streams')->find()
            ->where(function (QueryExpression $exp) use ($ids) {
                return $exp->in('object_id', $ids);
            })
            ->map(function (Stream $stream) use ($options, $preset) {
                $id = $stream->object_id;
                $uuid = $stream->uuid;

                $info = Thumbnail::get($stream, $preset ?: $options ?: 'default');

                return $info + compact('id', 'uuid');
            })
            ->toList();

        $this->fetchProviderThumbs($ids, $thumbnails);

        $this->set('_meta', compact('thumbnails'));
        $this->set('_serialize', []);
    }

    /**
     * Add provider thumbnails to thumbnails array for remote media
     *
     * @param array $ids Media ids
     * @param array $thumbnails Thumbnail array
     * @return void
     */
    protected function fetchProviderThumbs(array $ids, array &$thumbnails) : void
    {
        $mediaIds = array_diff($ids, (array)Hash::extract($thumbnails, '{n}.id'));
        if (empty($mediaIds)) {
            return;
        }

        $conditions = [
            'id IN' => $mediaIds,
            $this->Table->aliasField('provider_thumbnail') . ' IS NOT NULL',
        ];
        $thumbs = $this->Table->find()
            ->where($conditions)
            ->select(['id', 'provider_thumbnail'])
            ->toArray();
        foreach ($thumbs as $thumb) {
            $thumbnails[] = [
                'id' => $thumb['id'],
                'ready' => true,
                'url' => $thumb['provider_thumbnail'],
            ];
        }
    }
}
