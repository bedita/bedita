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

namespace BEdita\Core\Model\Entity;

use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\Log\LogTrait;
use Cake\ORM\Entity;
use Cake\Utility\Text;
use League\Flysystem\FileNotFoundException;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream as ZendStream;

/**
 * Stream Entity
 *
 * @property string $uuid
 * @property int $version
 * @property int|null $object_id
 * @property string $uri
 * @property string|null $file_name
 * @property string $mime_type
 * @property int $file_size
 * @property string $hash_md5
 * @property string $hash_sha1
 * @property \Psr\Http\Message\StreamInterface|null $contents
 * @property string|null $url
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity|null $object
 */
class Stream extends Entity
{

    use LogTrait;

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => false,
        'file_name' => true,
        'contents' => true,
    ];

    /**
     * Get filesystem path (including mount point) under which file should be stored.
     *
     * Result of this method will be generally used as the entity `uri` on save.
     *
     * @param string $filesystem Filesystem for which path must be generated.
     * @param int $subLevels Number of sub-levels to organize files.
     * @return string
     */
    public function filesystemPath($filesystem = 'default', $subLevels = 0)
    {
        if (!$this->has('uuid')) {
            // Generate random UUID. This is needed for path, otherwise we'd let Cake take care of it.
            $this->uuid = Text::uuid();
        }

        // Prepare file name.
        $fileName = $extension = '';
        if ($this->has('file_name')) {
            $fileName = Text::transliterate($this->file_name);
            preg_match('/^(.+?)((?:\.[a-z0-9]+)*)$/i', strtolower(basename($fileName)), $matches);
            list(, $fileName, $extension) = $matches + [null, '', ''];
            $fileName = '-' . Text::slug($fileName);
        }
        $fileName = $this->uuid . $fileName . $extension;

        // Prepare sub-levels.
        $prefix = '';
        $hash = sha1($fileName);
        $subLevels = floor(max(0, min($subLevels, strlen($hash) / 2)));
        for ($i = 0; $i < $subLevels; $i++) {
            $prefix .= substr($hash, $i * 2, 2) . '/';
        }

        return sprintf(
            '%s://%s%s',
            $filesystem, // Flysystem mount point.
            $prefix, // Prefix.
            $fileName // File name.
        );
    }

    /**
     * Magic getter for file contents.
     *
     * It downloads the file using Flysystem adapter and returns a PSR-7 stream.
     *
     * Accessing file contents should be used with care, since it might incur in high network traffic
     * if files are stored in an external location such as another server or a service such as Amazon S3.
     *
     * @return \Psr\Http\Message\StreamInterface|null
     */
    protected function _getContents()
    {
        if (!empty($this->_properties['contents'])) {
            // Downloaded already.
            return $this->_properties['contents'];
        }

        if (!$this->has('uri')) {
            // This stream has no contents yet.
            return null;
        }

        try {
            $readStream = FilesystemRegistry::getMountManager()->readStream($this->uri);
        } catch (FileNotFoundException $e) {
            // Unable to read from filesystem. Better log a warning...
            $this->log(sprintf('Unable to read file contents: %s', $this->uri), 'warning');

            return null;
        }

        $stream = new ZendStream('php://temp', 'wb+');
        $stream->attach($readStream);

        return $this->_properties['contents'] = $stream;
    }

    /**
     * Setter for file contents.
     *
     * @param mixed $contents File contents. Can be either a PSR-7 stream, a PHP stream resource, or any
     *      other value that can be cast to string (scalars, nulls, and objects implementing `__toString()` method).
     * @return \Psr\Http\Message\StreamInterface
     * @throws \InvalidArgumentException Throws an exception if contents could not be converted to a PSR-7 stream.
     */
    protected function _setContents($contents)
    {
        /* @todo Implement this part. */
        $this->file_size = 0;
        $this->hash_md5 = md5('Not yet implemented');
        $this->hash_sha1 = sha1('Not yet implemented');
        $this->mime_type = 'application/octet-stream';

        if ($contents instanceof StreamInterface) {
            // Already a PSR-7 stream.
            return $contents;
        }
        if (is_resource($contents)) {
            // Not a stream, but a resource that can hopefully be attached to a PSR-7 stream.
            $stream = new ZendStream('php://temp', 'wb+');
            $stream->attach($contents);

            return $stream;
        }
        if (is_scalar($contents) || is_null($contents) || (is_object($contents) && method_exists($contents, '__toString'))) {
            // A value that can be cast to a string. A new PSR-7 stream is created, and value is written to it.
            $stream = new ZendStream('php://temp', 'wb+');
            $stream->write((string)$contents);

            return $stream;
        }

        throw new \InvalidArgumentException(
            'Invalid contents provided, must be a PSR-7 stream, a resource or a value that can be converted to string'
        );
    }

    /**
     * Getter for public URL from which file can be accessed.
     *
     * @return string|null
     */
    protected function _getUrl()
    {
        if (!empty($this->_properties['url'])) {
            // Already computed the public URL. Let's avoid requesting it again.
            return $this->_properties['url'];
        }

        if (!$this->has('object_id')) {
            // Stream is not yet (or not any more) associated to any object, thus it is not accessible.
            return null;
        }

        return $this->_properties['url'] = FilesystemRegistry::getPublicUrl($this->uri);
    }
}
