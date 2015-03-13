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

class XmlJsonConverter extends BEAppModel
{
    /**
     * Namespace URI
     */
    const BEDITA_NS_URI = 'http://bedita.com/bedita';

    /**
     * This model uses no table.
     *
     * @var boolean
     */
    public $useTable = false;

    /**
     * Set maximum recursion depth.
     *
     * @var int
     */
    private $maxDepth = 10;

    /**
     * Returns an array out of a string or a DOMDocument itself.
     *
     * @param string|DOMDocument $document
     * @return array
     */
    public function toArray ($document) {
        if (!($document instanceof DOMDocument)) {

            $document = DOMDocument::loadXML($document);
            if (empty($document)) {
                return false;
            }
        }
        return $this->nodeToArray($document);
    }

    /**
     * Returns a JSON string out of a string or a DOMDocument itself.
     *
     * @param string|DOMDocument $document
     * @param int $options
     * @return string
     */
    public function toJson ($document, $options = JSON_PRETTY_PRINT) {
        return json_encode($this->toArray($document), $options);
    }

    /**
     * Returns a DOMDocument out of an array.
     *
     * @param array $array
     * @param string $root
     * @return DOMDocument
     */
    public function toXml (array $array, $root = 'bedita') {
        $dom = new DOMDocument('1.0', 'iso-8859-1');
        $root = $dom->appendChild(new DOMElement($root));
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:bedita', self::BEDITA_NS_URI);
        $this->arrayToNode($root, $array);
        return $dom;
    }

    /**
     * Returns an XML string out of an array.
     *
     * @param array $array
     * @param string $root
     * @return string
     */
    public function toXmlString (array $array, $root = 'bedita', $prettyPrint = true) {
        $dom = $this->toXml($array, $root);
        if ($prettyPrint) {
            $dom->formatOutput = true;
            $dom->preserveWhitespace = false;
        }
        return $dom->saveXML();
    }

    /**
     * Pushes `$value` in `$array` at the selected `$index` with multiplicity `$counter[$index]`.
     *
     * @param array $array
     * @param array $counter
     * @param string $index
     * @param mixed $value
     * @param bool $forceArray
     * @return void
     */
    private function pushVal (array &$array, array &$counter, $index, $value, $forceArray = false) {
        if (empty($value)) {
            return;
        }

        if ($forceArray) {
            if (!array_key_exists($index, $array)) {
                $array[$index] = array();
                $counter[$index] = 0;
            }
            array_push($array[$index], $value);
            $counter[$index]++;
            return;
        }

        if (!array_key_exists($index, $array)) {
            $counter[$index] = 1;
            $array[$index] = $value;
            return;
        }
        if ($counter[$index] == 1) {
            $array[$index] = array($array[$index]);
        }
        array_push($array[$index], $value);
        $counter[$index]++;
    }

    /**
     * Converts a DOM Node to an associative array.
     *
     * @param DOMNode $node
     * @param int $depth
     * @return array
     */
    private function nodeToArray (DOMNode $node, $depth = 0) {
        // Text node.
        if ($depth > $this->maxDepth || (!$node->childNodes->length && !$node->attributes->length)) {
            return $node->textContent;//!$node->isWhitespaceInElementContent() ? $node->textContent : null;
        }
        $array = array();
        $counter = array();

        // Attributes.
        if (!is_null($node->attributes)) {
            foreach ($node->attributes as $attr) {
                $array[$attr->name] = $attr->value;
                $counter[$attr->name] = 1;
            }
        }

        // Child nodes.
        foreach ($node->childNodes as $cNode) {
            switch ($cNode->nodeType) {
                case XML_TEXT_NODE:
                case XML_CDATA_SECTION_NODE:
                    // Text.
                    if (!$cNode->isWhitespaceInElementContent()) {
                        array_push($array, $cNode->textContent);
                    }
                    break;
                default:
                    $forceArray = false;
                    if ($cNode->hasAttributeNS(self::BEDITA_NS_URI, 'array')) {
                        $forceArray = true;
                        $cNode->removeAttributeNS(self::BEDITA_NS_URI, 'array');
                    }
                    $this->pushVal($array, $counter, $cNode->nodeName, $this->nodeToArray($cNode, $depth + 1), $forceArray);
                    break;
            }
        }

        // Unique element array.
        $keys = array_keys($array);
        if (count($array) == 1 && is_numeric(reset($keys))) {
            return reset($array);
        }
        return $array;
    }

    /**
     * Checks whether an array has only numeric keys.
     *
     * @param array $array
     * @return bool
     */
    private function numericKeys (array $array) {
        foreach (array_keys($array) as $key) {
            if (!is_numeric($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Converts an array to a DOM Node.
     *
     * @param DOMNode $node
     * @param mixed $array
     * @param int $depth
     * @return
     */
    private function arrayToNode (DOMNode $node, $array, $depth = 0) {
        // Text element.
        if ($depth > $this->maxDepth || !is_array($array)) {
            $node->appendChild((strpbrk($array, '<>&\'"') === false) ? new DOMText($array) : new DOMCdataSection($array));
            return;
        }

        foreach ($array as $key => $val) {
            // Text element.
            if (is_numeric($key)) {
                $node->appendChild((strpbrk($val, '<>&\'"') === false) ? new DOMText($val) : new DOMCdataSection($val));
                continue;
            }

            // Multiple elements under the same key.
            if (is_array($val) && $this->numericKeys($val)) {
                foreach ($val as $v) {
                    $newNode = $node->appendChild(new DOMElement($key));
                    $newNode->setAttributeNS(self::BEDITA_NS_URI, 'bedita:array', 'array');
                    $this->arrayToNode($newNode, $v);
                }
                continue;
            }

            // Single key.
            $newNode = $node->appendChild(new DOMElement($key));
            $this->arrayToNode($newNode, $val);
        }
        return;
    }
}
