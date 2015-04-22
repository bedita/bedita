<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2009-2015 ChannelWeb Srl, Chialab Srl
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

/*
 * Based on:
 * Project and Source hosted on http://code.google.com/p/hyphenator/
 * @license Hyphenator X.Y.Z - client side hyphenation for webbrowsers
 * Copyright (C) 2015  Mathias Nater, Zürich (mathiasnater at gmail dot com)
 */



function charCodeAt($str, $num) { 
    return utf8_ord(utf8_charAt($str, $num)); 
}

function utf8_ord($ch) {
    $len = strlen($ch);
    if($len <= 0) {
        return false;
    }
    $h = ord($ch{0});
    if ($h <= 0x7F) {
        return $h;
    }
    if ($h < 0xC2) {
        return false;
    }
    if ($h <= 0xDF && $len>1) {
        return ($h & 0x1F) <<  6 | (ord($ch{1}) & 0x3F);
    }
    if ($h <= 0xEF && $len>2) {
        return ($h & 0x0F) << 12 | (ord($ch{1}) & 0x3F) << 6 | (ord($ch{2}) & 0x3F);          
    }
    if ($h <= 0xF4 && $len>3) {
        return ($h & 0x0F) << 18 | (ord($ch{1}) & 0x3F) << 12 | (ord($ch{2}) & 0x3F) << 6 | (ord($ch{3}) & 0x3F);
    }
    return false;
}

function utf8_charAt($str, $num) { 
    return mb_substr($str, $num, 1, 'UTF-8'); 
}

class HyphenatorCharMap {

    public $int2code = array();

    public $code2int = array();

    function __construct($patternChars) {
        $len = mb_strlen($patternChars, 'utf-8');
        for ($i = 0; $i < $len; $i += 1) {
            $this->add(charCodeAt($patternChars, $i));
        }
    }

    function add($newValue) {
        if (!isset($this->code2int[$newValue])) {
            array_push($this->int2code, $newValue);
            $this->code2int[$newValue] = count($this->int2code) - 1;
        }
    }
}

class HyphenatorValueStore {

    public $keys = array();

    public $startIndex = 1;

    public $actualIndex = 2;

    public $lastValueIndex = 2;

    function __construct($len) {
        $this->key = $this->generateKeys($len);
    }

    function generateKeys($len) {
        $i = 0;
        $r = array();
        for ($i = $len - 1; $i >= 0; $i -= 1) {
            $r[$i] = 0;
        }
        return $r;
    }

    function add($p) {
        if ($p !== 0) {
            $this->keys[$this->actualIndex] = $p;
            $this->lastValueIndex = $this->actualIndex;
        }
        $this->actualIndex += 1;
    }

    function finalize() {
        $start = $this->startIndex;
        $this->keys[$start] = $this->lastValueIndex - $start;
        $this->startIndex = $this->lastValueIndex + 1;
        $this->actualIndex = $this->startIndex + 1;
        return $start;
    }

}

/**
 * @desc Provides all functionality to do hyphenation, except the patterns that are loaded externally
 */
class Hyphenator {

    public $languages = array();
    
    public $enableReducedPatternSet = false;

    public $exceptions = array();

    public $enableCache = true;

    /**
     * @member {number} Hyphenator~min
     * @desc
     * A number wich indicates the minimal length of words to hyphenate.
     * @default 6
     * @access private
     * @see {@link Hyphenator.config}
     */
    public $min = 2;

    /**
     * @member {number} Hyphenator~orphanControl
     * @desc
     * Control how the last words of a line are handled:
     * level 1 (default): last word is hyphenated
     * level 2: last word is not hyphenated
     * level 3: last word is not hyphenated and last space is non breaking
     * @default 1
     * @access private
     */
    public $orphanControl = 1;

    /**
     * @member {string|null} Hyphenator~defaultLanguage
     * @desc
     * The language defined by the developper. This language setting is defined by a config option.
     * It is overwritten by any html-lang-attribute and only taken in count, when no such attribute can
     * be found (i.e. just before the prompt).
     * @access private
     * @see {@link Hyphenator.config}
     * @see {@link Hyphenator~autoSetMainLanguage}
     */
    public $defaultLanguage = 'en';

    /**
     * @member {string} Hyphenator~zeroWidthSpace
     * @desc
     * A string that holds a char.
     * Depending on the browser, this is the zero with space or an empty string.
     * zeroWidthSpace is used to break URLs
     * @access private
     */
    public $zeroWidthSpace = '&#8203;';

    /**
     * @member {string} Hyphenator~hyphen
     * @desc
     * A string containing the character for in-word-hyphenation
     * @default the soft hyphen
     * @access private
     * @see {@link Hyphenator.config}
     */
    public $hyphen = '&shy;';

    /**
     * @member {string} Hyphenator~urlhyphen
     * @desc
     * A string containing the character for url/mail-hyphenation
     * @default the zero width space
     * @access private
     * @see {@link Hyphenator.config}
     * @see {@link Hyphenator~zeroWidthSpace}
     */
    public $urlhyphen = '&#8203;';

    /**
     * @method Hyphenator~hyphenateURL
     * @desc
     * Puts {@link Hyphenator~urlhyphen} (default: zero width space) after each no-alphanumeric char that my be in a URL.
     * @param {string} url to hyphenate
     * @returns string the hyphenated URL
     * @access public
     */
    function hyphenateURL($url) {
        $tmp = preg_replace($this->urlReg, '$&' + $this->urlhyphen, $url);
        $parts = explode($this->urlhyphen, $tmp);

        for ($i = 0; $i < count($parts); $i += 1) {
            if (count($parts[$i]) > (2 * $this->min)) {
                $parts[$i] =  preg_replace('/(\w{3})(\w)/gi', "$1" + $this->urlhyphen + "$2", $parts[$i]);
            }
        }
        if ($parts[count($parts) - 1] === '') {
            array_pop($parts);
        }
        return implode($this->urlhyphen, $parts);
    }


    /**
     * @method Hyphenator~convertPatternsToArray
     * @desc
     * converts the patterns to a (typed, if possible) array as described by Liang:
     *
     * 1. Create the CharMap: an alphabet of used character codes mapped to an int (e.g. a: "97" -> 0)
     *    This map is bidirectional:
     *    charMap.code2int is an object with charCodes as keys and corresponging ints as values
     *    charMao.int2code is an array of charCodes at int indizes
     *    the length of charMao.int2code is equal the length of the alphabet
     *
     * 2. Create a ValueStore: (typed) array that holds "values", i.e. the digits extracted from the patterns
     *    The first value starts at index 1 (since the trie is initialized with zeroes, starting at 0 would create errors)
     *    Each value starts with its length at index i, actual values are stored in i + n where n < length
     *    Trailing 0 are not stored. So pattern values like e.g. "010200" will become […,4,0,1,0,2,…]
     *    The ValueStore-Object manages handling of indizes automatically. Use ValueStore.add(p) to add a running value.
     *    Use ValueStore.finalize() when the last value of a pattern is added. It will set the length and return the starting index of the pattern.
     *    To prevent doubles we could temporarly store the values in a object {value: startIndex} and only add new values,
     *    but this object deoptimizes very fast (new hidden map for each entry); here we gain speed and pay memory
     *    
     * 3. Create and zero initialize a (typed) array to store the trie. The trie uses two slots for each entry/node:
     *    i: a link to another position in the array or -1 if the pattern ends here or more rows have to be added.
     *    i + 1: a link to a value in the ValueStore or 0 if there's no value for the path to this node.
     *    Although the array is one-dimensional it can be described as an array of "rows",
     *    where each "row" is an array of length trieRowLength (see below).
     *    The first entry of this "row" represents the first character of the alphabet, the second a possible link to value store,
     *    the third represents the second character of the alphabet and so on…
     *
     * 4. Initialize trieRowLength (length of the alphabet * 2)
     *
     * 5. Now we apply extract to each pattern collection (patterns of the same length are collected and concatenated to one string)
     *    extract goes through these pattern collections char by char and adds them either to the ValueStore (if they are digits) or
     *    to the trie (adding more "rows" if necessary, i.e. if the last link pointed to -1).
     *    So the first "row" holds all starting characters, where the subsequent rows hold the characters that follow the
     *    character that link to this row. Therefor the array is dense at the beginning and very sparse at the end.
     * 
     * 
     * @access private
     * @param {Object} language object
     */
    function extract($patternSizeInt, $patterns, $charMapc2i, &$valueStore, &$indexedTrie, $trieRowLength, $trieNextEmptyRow = 0) {
        $prevWasDigit = false;
        $rowStart = 0;
        $nextRowStart = 0;
        for ($charPos = 0; $charPos < mb_strlen($patterns, 'utf-8'); $charPos += 1) {
            $charCode = charCodeAt($patterns, $charPos);
            if (($charPos + 1) % $patternSizeInt !== 0) {
                //more to come…
                if ($charCode >= 49 && $charCode <= 57) {
                    //charCode is a digit
                    $valueStore->add($charCode - 48);
                    $prevWasDigit = true;
                } else {
                    //charCode is alphabetical
                    if (!$prevWasDigit) {
                        $valueStore->add(0);
                    }
                    $prevWasDigit = false;
                    if ($nextRowStart === -1) {
                        $nextRowStart = $trieNextEmptyRow + $trieRowLength;
                        $trieNextEmptyRow = $nextRowStart;
                        $indexedTrie[$rowStart + $mappedCharCode * 2] = $nextRowStart;
                    }
                    $mappedCharCode = $charMapc2i[$charCode];
                    $rowStart = $nextRowStart;
                    $nextRowStart = $indexedTrie[$rowStart + $mappedCharCode * 2];
                    if ($nextRowStart === 0) {
                        $indexedTrie[$rowStart + $mappedCharCode * 2] = -1;
                        $nextRowStart = -1;
                    }
                }
            } else {
                //last part of pattern
                if ($charCode >= 49 && $charCode <= 57) {
                    //the last charCode is a digit
                    $valueStore->add($charCode - 48);
                    $indexedTrie[$rowStart + $mappedCharCode * 2 + 1] = $valueStore->finalize();
                } else {
                    //the last charCode is alphabetical
                    if (!$prevWasDigit) {
                        $valueStore->add(0);
                    }
                    $valueStore->add(0);
                    if ($nextRowStart === -1) {
                        $nextRowStart = $trieNextEmptyRow + $trieRowLength;
                        $trieNextEmptyRow = $nextRowStart;
                        $indexedTrie[$rowStart + $mappedCharCode * 2] = $nextRowStart;
                    }
                    $mappedCharCode = $charMapc2i[$charCode];
                    $rowStart = $nextRowStart;
                    if ($indexedTrie[$rowStart + $mappedCharCode * 2] === 0) {
                        $indexedTrie[$rowStart + $mappedCharCode * 2] = -1;
                    }
                    $indexedTrie[$rowStart + $mappedCharCode * 2 + 1] = $valueStore->finalize();
                }
                $rowStart = 0;
                $nextRowStart = 0;
                $prevWasDigit = false;
            }
        }

        return $trieNextEmptyRow;
    }

    function convertPatternsToArray(&$lo) {
        $lo->charMap = new HyphenatorCharMap($lo->patternChars); 
        $lo->valueStore = new HyphenatorValueStore($lo->valueStoreLength);

        $lo->indexedTrie = array();
        $len = $lo->patternArrayLength * 2;
        for ($i = $len - 1; $i >= 0; $i -= 1) {
            $lo->indexedTrie[$i] = 0;
        }

        $trieRowLength = count($lo->charMap->int2code) * 2;
        $trieNextEmptyRow = 0;

        foreach ($lo->patterns as $i => $val) {
            $trieNextEmptyRow = $this->extract(
                intval($i, 10), 
                $val, 
                $lo->charMap->code2int, 
                $lo->valueStore, 
                $lo->indexedTrie, 
                $trieRowLength,
                $trieNextEmptyRow
            );
        }
    }

    /**
     * @method Hyphenator~recreatePattern
     * @desc
     * Recreates the pattern for the reducedPatternSet
     * @param {string} pattern The pattern (chars)
     * @param {string} nodePoints The nodePoints (integers)
     * @access private
     * @return {string} The pattern (chars and numbers)
     */
    function recreatePattern($pattern, $nodePoints) {
        $r = array();
        $c = explode($pattern, '');
        for ($i = 0; $i <= count($c); $i += 1) {
            if ($nodePoints[$i] && $nodePoints[$i] !== 0) {
                array_push($r, $nodePoints[$i]);
            }
            if ($c[$i]) {
                array_push($r, $c[$i]);
            }
        }
        return implode('', $r);
    }

    /**
     * @method Hyphenator~convertExceptionsToObject
     * @desc
     * Converts a list of comma seprated exceptions to an object:
     * 'Fortran,Hy-phen-a-tion' -> {'Fortran':'Fortran','Hyphenation':'Hy-phen-a-tion'}
     * @access private
     * @param {string} exc a comma separated string of exceptions (without spaces)
     * @return {Object.<string, string>}
     */
    function convertExceptionsToObject($exc) {
        $w = explode(', ', $exc);
        $r = array();
        for ($i = 0, $l = count($w); $i < $l; $i += 1) {
            $key = str_replace('-', '', $w[$i]);
            if (!isset($r[$key])) {
                $r[$key] = $w[$i];
            }
        }
        return $r;
    }

    /**
     * @method Hyphenator~loadPatterns
     * @desc
     * Checks if the requested file is available in the network.
     * Adds a &lt;script&gt;-Tag to the DOM to load an externeal .js-file containing patterns and settings for the given language.
     * If the given language is not in the {@link Hyphenator~supportedLangs}-Object it returns.
     * One may ask why we are not using AJAX to load the patterns. The XMLHttpRequest-Object 
     * has a same-origin-policy. This makes the Bookmarklet impossible.
     * @param {string} lang The language to load the patterns for
     * @access private
     * @see {@link Hyphenator~basePath}
     */
    function loadPatterns($lang) {
        $path = realpath(dirname(__FILE__)) . '/patterns/' . $lang;
        if (file_exists($path)) {
            $contents = file_get_contents($path);
            $this->languages[$lang] = json_decode($contents);
        }
    }

    /**
     * @method Hyphenator.addExceptions
         * @desc
     * Adds the exceptions from the string to the appropriate language in the 
     * {@link Hyphenator~languages}-object
     * @param {string} lang The language
     * @param {string} words A comma separated string of hyphenated words WITH spaces.
     * @access public
     * @example &lt;script src = "Hyphenator.js" type = "text/javascript"&gt;&lt;/script&gt;
     * &lt;script type = "text/javascript"&gt;
     *   Hyphenator.addExceptions('de','ziem-lich, Wach-stube');
     *   Hyphenator.run();
     * &lt;/script&gt;
     */
    function addExceptions($lang, $words) {
        if ($lang === '') {
            $lang = 'global';
        }
        if (!empty($this->exceptions[$lang])) {
            $this->exceptions[$lang] .= ', ' . $words;
        } else {
            $this->exceptions[$lang] = $words;
        }
    }

    /**
     * @method Hyphenator~prepareLanguagesObj
     * @desc
     * Adds some feature to the language object:
     * - cache
     * - exceptions
     * Converts the patterns to a trie using {@link Hyphenator~convertPatterns}
     * If storage is active the object is stored there.
     * @access private
     * @param {string} lang The language of the language object
     */
    function prepareLanguagesObj($lang) {
        $lo = $this->languages[$lang];

        if (empty($lo->prepared)) {
            if ($this->enableReducedPatternSet) {
                $lo->redPatSet = array();
            }
            //add exceptions from the pattern file to the local 'exceptions'-obj
            if (!empty($lo->exceptions)) {
                $this->addExceptions($lang, $lo->exceptions);
                unset($lo->exceptions);
            }
            //copy global exceptions to the language specific exceptions
            if (!empty($this->exceptions['global'])) {
                if (!empty($this->exceptions[$lang])) {
                    $this->exceptions[$lang] += ', ' + $this->exceptions['global'];
                } else {
                    $this->exceptions[$lang] = $this->exceptions['global'];
                }
            }
            //move exceptions from the the local 'exceptions'-obj to the 'language'-object
            if (!empty($this->exceptions[$lang])) {
                $lo->exceptions = $this->convertExceptionsToObject($this->exceptions[$lang]);
                unset($this->exceptions[$lang]);
            } else {
                $lo->exceptions = array();
            }
            $this->convertPatternsToArray($lo);
            if (!isset($lo->min)) {
                $lo->min = $this->min;
            }
            $wrd = '[\\w' . $lo->specialChars . chr(173) . chr(8204) . '-]{' . $lo->min . ',}';
            $lo->genRegExp = '/(' . $wrd . ')(?!([^<]+)?>)/i';
            $lo->prepared = true;
        }
    }

    /****
     * @method Hyphenator~prepare
     * @desc
     * This funtion prepares the Hyphenator~Object: If RemoteLoading is turned off, it assumes
     * that the patternfiles are loaded, all conversions are made and the callback is called.
     * If storage is active the object is retrieved there.
     * If RemoteLoading is on (default), it loads the pattern files and repeatedly checks Hyphenator.languages.
     * If a patternfile is loaded the patterns are
     * converted to their object style and the lang-object extended.
     * Finally the callback is called.
     * @access private
     */
    function prepare($lang) {
        $this->loadPatterns($lang);
        $this->prepareLanguagesObj($lang);
    }

    function doCharSubst($loCharSubst, $w) {
        foreach ($loCharSubst as $subst => $value) {
            $r = preg_replace('/' . $subst . '/g', $loCharSubst[$subst], $w);
        }
        return $r;
    }

    /**
     * @method Hyphenator~hyphenateWord
     * @desc
     * This function is the heart of Hyphenator.js. It returns a hyphenated word.
     *
     * If there's already a {@link Hyphenator~hypen} in the word, the word is returned as it is.
     * If the word is in the exceptions list or in the cache, it is retrieved from it.
     * If there's a '-' hyphenate the parts.
     * The hyphenated word is returned and (if acivated) cached.
     * Both special Events onBeforeWordHyphenation and onAfterWordHyphenation are called for the word.
     * @param {Object} lo A language object (containing the patterns)
     * @param {string} lang The language of the word
     * @param {string} word The word
     * @returns string The hyphenated word
     * @access public
     */
    function hyphenateWord(&$lo, $lang, &$word) {
        $pattern = '';
        $wordLength = mb_strlen($word, 'utf-8');
        $hw = '';
        $charMap = $lo->charMap->code2int;
        $row = 0;
        $link = 0;
        $value = 0;
        $indexedTrie = &$lo->indexedTrie;
        $valueStore = &$lo->valueStore->keys;

        $char = html_entity_decode($this->hyphen);

        if ($word === '') {
            $hw = '';
        } else if (mb_strlen($word) < $lo->min) {
            $hw = $word;
        } elseif ($this->enableCache && !empty($lo->cache) && !empty($lo->cache[$word])) { //the word is in the cache
            $hw = $lo->cache[$word];
        } elseif (strrpos($word, $char) !== false) {
            //word already contains shy; -> leave at it is!
            $hw = $word;
        } elseif (!empty($lo->exceptions[$word])) { //the word is in the exceptions list
            $hw = str_replace('-', $char, $lo->exceptions[$word]);
        } else if (strpos($word, '-') !== false) {
            //word contains '-' -> hyphenate the parts separated with '-'
            $parts = explode('-', $word);
            for ($i = 0; $i < count($parts); $i += 1) {
                $parts[$i] = $this->hyphenateWord($lo, $lang, $parts[$i]);
            }
            $hw = implode('-', $parts);
        } else {
            $hw = $word;
            $ww = strtolower($word);

            if (!empty($lo->charSubstitution)) {
                $ww = $this->doCharSubst($lo->charSubstitution, $ww);
            }
            if (strpos($word, "'") !== false) {
                $ww = preg_replace("/'/g", "’", $ww);
            }

            $ww = '_' . $ww . '_';
            $wwlen = mb_strlen($ww, 'utf-8');
            $wwhp = array();
            $wwAsMappedCharCode = array();
            for ($hp = 0; $hp < $wwlen - 1; $hp += 1) {
                $wwhp[$hp] = 0;
            }

            for ($pstart = 0; $pstart < $wwlen; $pstart += 1) {
                if (!isset($charMap[charCodeAt($ww, $pstart)])) {
                    $wwAsMappedCharCode[$pstart] = -1;
                } else {
                    $wwAsMappedCharCode[$pstart] = $charMap[charCodeAt($ww, $pstart)];
                }
            }

            for ($pstart = 0; $pstart < $wwlen; $pstart += 1) {
                $row = 0;
                $pattern = '';
                for ($plen = $pstart; $plen < $wwlen; $plen += 1) {
                    $mappedCharCode = $wwAsMappedCharCode[$plen];
                    if ($mappedCharCode === -1) {
                        break;
                    }
                    if ($this->enableReducedPatternSet) {
                        $pattern += substr($ww, $plen, 1);
                    }
                    $link = $indexedTrie[$row + $mappedCharCode * 2];
                    $value = $indexedTrie[$row + $mappedCharCode * 2 + 1];
                    if ($value > 0) {
                        $hp = $valueStore[$value];
                        while ($hp) {
                            $hp -= 1;
                            if (isset($valueStore[$value + 1 + $hp]) && isset($wwhp[$pstart + $hp]) && $valueStore[$value + 1 + $hp] > $wwhp[$pstart + $hp]) {
                                $wwhp[$pstart + $hp] = $valueStore[$value + 1 + $hp];
                            }
                        }
                        if ($this->enableReducedPatternSet) {
                            if (!$lo->redPatSet) {
                                $lo->redPatSet = array();
                            }
                            
                            $values = array_slice($valueStore, $value + 1, $value + 1 + $valueStore[$value]);
                            $lo->redPatSet[$pattern] = $this->recreatePattern($pattern, $values);
                        }
                    }
                    if ($link > 0) {
                        $row = $link;
                    } else {
                        break;
                    }
                }
            }
            $shift = 0;
            $shiftRange = strlen($char);
            for ($hp = 0; $hp < $wordLength; $hp += 1) {
                if ($hp >= $lo->leftmin && $hp <= ($wordLength - $lo->rightmin) && ($wwhp[$hp + 1] % 2) !== 0) {
                    $hw = substr_replace($hw, $char, $hp + $shift, 0);
                    $shift += $shiftRange;
                }
            }
        }

        if ($this->enableCache) { //put the word in the cache
            $lo->cache[$word] = $hw;
        }
        return $hw;
    }

    /**
     * @method Hyphenator.hyphenate
     * @access public
     * @desc
     * Hyphenates the target. The language patterns must be loaded.
     * If the target is a string, the hyphenated string is returned
     */
    function hyphenate($target, $lang) {
        if (empty($this->languages[$lang])) {
            $this->prepare($lang);
        }
        $lo = $this->languages[$lang];
        if (empty($lo->prepared)) {
            $this->prepareLanguagesObj(lang);
        }
        $target = html_entity_decode($target, ENT_NOQUOTES, 'UTF-8');
        $target = preg_replace_callback($lo->genRegExp, function($match) use ($lo, $lang) {
            return $this->hyphenateWord($lo, $lang, $match[0]);
        }, $target);
        return $target;
    }
}

?>