<?php
namespace BEdita\Core\Model\Entity;

use Cake\ORM\Entity;

/**
 * Media Entity
 *
 * @property int $id
 * @property string $uri
 * @property string $name
 * @property string $mime_type
 * @property int $file_size
 * @property string $hash_file
 * @property string $original_name
 * @property int $width
 * @property int $height
 * @property string $provider
 * @property string $media_uid
 * @property string $thumbnail
 */
class Media extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
