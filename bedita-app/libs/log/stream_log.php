<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2014 ChannelWeb Srl, Chialab Srl
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
 * Stream Storage for Logging.  Writes logs to a stream, optionally formatting it as a JSON.
 */
class StreamLog
{

    /**
     * Stream to write logs to.
     *
     * @var string|resource
     */
    protected $_stream = null;

    /**
     * Whether or not to format the log output as JSON.
     *
     * @var bool
     */
    protected $_json = false;

    /**
     * Constructs a new File Logger.
     *
     * Options
     *
     * - `stream` the stream to write logs to. Defaults to `php://stderr`. Can be either an open resource, or a path to
     *     write to that is accepted by {@see file_put_contents()}.
     * - `json` whether or not to format the log output as JSON. Defaults to false.
     *
     * @param array{stream?: string|resource, json?: bool} $options Options for the FileLog, see above.
     * @return void
     */
    public function __construct($options = [])
    {
        $options += ['stream' => 'php://stderr', 'json' => false];
        $this->_stream = $options['stream'];
        $this->_json = $options['json'];
    }

    /**
     * Implements writing to log files.
     *
     * @param string $type The type of log you are making.
     * @param string $message The message you want to log.
     * @return boolean success of write.
     */
    public function write($type, $message)
    {
        $ts = time();
        $out = $this->_json
            ? json_encode([
                'date' => date('c', $ts),
                'type' => $type,
                'message' => $message,
                'timestamp' => $ts,
            ])
            : (date('Y-m-d H:i:s', $ts) . ' ' . ucfirst($type) . ': ' . $message);

        if (is_resource($this->_stream)) {
            return (bool)fwrite($this->_stream, $out. PHP_EOL);
        }

        return (bool)file_put_contents(
            $this->_stream,
            $out . PHP_EOL,
            FILE_APPEND
        );
    }
}
