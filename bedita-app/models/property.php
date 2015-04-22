<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2015 ChannelWeb Srl, Chialab Srl
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
 * Property object
 */
class Property extends BEAppModel  {

    public $actsAs = array('CompactResult' => 
        array('PropertyOption', 'ObjectProperty', 'UserProperty'));
 
    public $hasMany = array(
        'PropertyOption', 
        'ObjectProperty', 
        'UserProperty');

    public $validate = array(
        'name' => array(
          'rule' => 'notEmpty'
        )
    );

    /**
     * Get property id from name and object type id
     * 
     * @param $name, name of the property
     * @param $name, name of the property
     * @return proerty id on success, null if no proerty id was found
     */
    public function propertyId($name, $objectTypeId) {
        $this->Behaviors->disable('CompactResult');
        $res = $this->field('id', array(
            'object_type_id' => $objectTypeId,
            'name' => $name));
        $this->Behaviors->enable('CompactResult');
        return $res;
    }

}