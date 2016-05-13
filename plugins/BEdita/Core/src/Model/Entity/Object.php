<?php
namespace BEdita\Core\Model\Entity;

use Cake\ORM\Entity;

/**
 * Object Entity.
 *
 * @property int $id
 * @property int $object_type_id
 * @property \BEdita\Core\Model\Entity\ObjectType $object_type
 * @property string $status
 * @property string $uname
 * @property bool $locked
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $published
 * @property string $title
 * @property string $description
 * @property string $body
 * @property string $extra
 * @property string $lang
 * @property int $created_by
 * @property int $modified_by
 * @property \Cake\I18n\Time $publish_start
 * @property \Cake\I18n\Time $publish_end
 *
 * @since 4.0.0
 */
class Object extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'locked' => false,
        'created' => false,
        'modified' => false,
        'published' => false,
        'created_by' => false,
        'modified_by' => false
    ];
}
