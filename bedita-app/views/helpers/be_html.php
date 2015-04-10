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
 * Html helper class
 *
 */
class BeHtmlHelper extends HtmlHelper {

    public function hyphen($text, $lang = null, $excludeSelectors = array('.formula'), $excludeSelectorDefaults = array('pre', 'code', 'embed', 'object', 'iframe', 'img', 'svg', 'video', 'audio', 'script', 'style', 'head', 'sub', 'sup')) {
        if (empty($lang)) {
            $lang = Configure::read('defaultLang');
        }

        App::Import('Vendor', 'simple_html_dom');
        App::Import('Vendor', 'hyphenator/Hyphenator');

        $hyphenator = ClassRegistry::init('Hyphenator');

        $excludeSelectors = array_merge($excludeSelectorDefaults, $excludeSelectors);
        $restore = array();
        if (!empty($excludeSelectors)) {
            $html = str_get_html('<html><body>' . $text . '</body></html>');
            $body = $html->find('body', 0);
            $badsSelectors = implode(', ', $excludeSelectors);
            $bads = $body->find($badsSelectors);
            foreach ($bads as $bad) {
                if (empty($bad->beNotHyphen)) {
                    $innerBads = $bad->find($badsSelectors);
                    foreach ($innerBads as $innerBad) {
                        $innerBad->beNotHyphen = true;
                    }
                    $restore[] = $bad->innertext;
                    $bad->innertext = '<!-- be-not-hyphen-' . (count($restore) - 1) . ' -->';
                }
            }
            $text = $body->innertext;
        }

        $text = $hyphenator->hyphenate($text, $lang);

        foreach ($restore as $key => $value) {
            $text = str_replace('<!-- be-not-hyphen-' . $key . ' -->', $value, $text);
        }

        return $text;
    }

}

?>