<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */

/**
 * Image stream
 */
class Image extends BeditaStreamModel {

    var $actsAs = array();

    public $objectTypesGroups = array('multimedia', 'leafs', 'related');

    protected $modelBindings = array(
        'detailed' => array(
            'BEObject' => array(
                'ObjectType',
                'Permission',
                'UserCreated',
                'UserModified',
                'RelatedObject',
                'Category',
                'ObjectProperty',
                'LangText',
                'Annotation',
                'Alias',
                'Version' => array('User.realname', 'User.userid')
            ),
            'Content',
            'Stream'
        ),
        'default' => array(
            'BEObject' => array(
                'ObjectProperty',
                'LangText',
                'ObjectType',
                'RelatedObject',
                'Category',
                'Annotation'
            ),
            'Content',
            'Stream'
        ),
        'minimum' => array(
            'BEObject' => array('ObjectType','Category'),
            'Content',
            'Stream'
        ),
        'frontend' => array(
            'BEObject' => array(
                'LangText',
                'ObjectProperty',
                'Category',
                'RelatedObject'
            ),
            'Content',
            'Stream'
        ),
        'api' => array(
            'BEObject' => array(
                'LangText',
                'ObjectProperty',
                'Category'
            ),
            'Content',
            'Stream'
        )
    );

    /**
     * Set image width and height reading from file
     *
     * @param int $id
     * @return true on success, false on failure
     */
    public function setImageDim(&$id) {
        $data = $this->find('first', array(
                    'conditions' => array('Image.id' => $id),
                    'contain' => array('Stream')));
        return $this->setImageDimArray($data);
    }

    /**
     * Set image width and height reading from file
     * (using img $data array)
     *
     * @param array $data
     * @return true on success, false on failure
     */
    public function setImageDimArray(array &$data) {
        $conf = Configure::getInstance();
        $res = false;
        if(empty($data['uri'])) {
            throw new BeditaException(__('Missing img path', true) . ' - ' . print_r($data, true));
        }

        if (!preg_match($conf->validate_resource['URL'], $data['uri'])) {

            if ( !$imageSize =@ getimagesize($conf->mediaRoot . $data['uri']) )
                throw new BeditaException(__('Get image size failed', true) . ' - ' . $data['uri']);

            if ($imageSize[0] == 0  || $imageSize[1] == 0)
                throw new BeditaException(__("Can't get dimension ", true) . ' - ' . $data['uri']);

            $this->id = $data['id'];
            $data['width'] = $imageSize[0];
            $data['height'] = $imageSize[1];
            if (!$this->saveField('width', $data['width']))
                throw new BeditaException(__('Error saving width field', true));
            if (!$this->saveField('height', $data['height']))
                throw new BeditaException(__('Error saving height field', true));
            $res = true;
        }
        return $res;
    }

}
