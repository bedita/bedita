<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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
 * Table toolbar for pagination and search helper
 */
class BeToolbarHelper extends AppHelper {
    /**
     * Included helpers.
     *
     * @var array
     */
    public $helpers = array('Form', 'Html', 'SessionFilter');

    /**
     * View publication data, if any
     *
     * @var array
     */
    private $_publication;

    /**
     * View section data, if any
     *
     * @var array
     */
    private $_section;

    /**
     * View current content data, if any
     *
     * @var array
     */
    private $_currentContent;

    /**
     * Template itemName string parameter
     *
     * @var string
     */
    private $_itemName;

    /**
     * Template noitem string parameter
     *
     * @var string
     */
    private $_noitem;

    /**
     * View moduleName string, if any
     *
     * @var string
     */
    private $_moduleName;

    /**
     * View current module array
     *
     * @var array
     */
    private $_currentModule;

    /**
     * Name used in toolbar, can be itemName or moduleName value
     *
     * @var string
     */
    private $_name;

    /**
     * Configuration instance
     *
     * @var Configure
     */
    private $_conf;

    /**
     * @inheritDoc
     */
    public function __construct() {
        $this->_view = ClassRegistry::getObject('view');
        $this->_publication = Set::classicExtract($this->_view->viewVars, 'publication', null);
        $this->_section =  Set::classicExtract($this->_view->viewVars, 'section', null);
        $this->_currentContent = Set::classicExtract($this->_view->viewVars, 'section.currentContent', null);
        $this->_moduleName = Set::classicExtract($this->_view->viewVars, 'moduleName', null);
        $this->_currentModule = Set::classicExtract($this->_view->viewVars, 'currentModule', null);
        $this->_conf = Configure::getInstance();
    }

    /**
     * Tags types of enclosuers (with or without text)
     *
     * @var array
     */
    public $tags = array(
        'with_text' => '<span %s >%s</span>',
        'without_text' => '<span %s />',
    );

    /**
     * Default options for change page combo
     *
     * @var array
     */
    protected $changePageDefaultOptions = array(1, 5, 10, 20, 50, 100);

    /**
     * Initialize toolbar parameters
     *
     * @param array $toolbar The toolbar
     * @param string $prefix The prefix
     *
     * @return void
     */
    public function init(&$toolbar, $prefix = '') {
        $this->params['toolbar'] = $toolbar;
        $this->params['prefix'] = $prefix;
    }

    /**
     * Return the link (html anchor tag) for the next page
     *
     * @param string $title Label link
     * @param array $option  attributes for link
     * @param string $disabledTitle Label link disabled
     * @param array $disabledOption HTML attributes for link disabled (if present, insert a tag SPAN)
     *
     * @return string
     */
    public function next($title = ' > ', $options = array(), $disabledTitle = ' > ', $disabledOption = array()) {
        return $this->_scroll('next', $title, $options, $disabledTitle, $disabledOption);
    }

    /**
     * Return the link (html anchor tag) for the previous page
     *
     * @param string $title Label link
     * @param array $option HTML attributes for link
     * @param string $disabledTitle Label link disabled
     * @param array $disabledOption HTML attributes for link disabled (if present, insert a tag SPAN)
     *
     * @return string
     */
    public function prev($title = ' < ', $options = array(), $disabledTitle = ' < ', $disabledOption = array()) {
        return $this->_scroll('prev', $title, $options, $disabledTitle, $disabledOption);
    }

    /**
     * Return the link (html anchor tag) for the first page
     *
     * @param string $title Label link
     * @param array $option HTML attributes for link
     * @param string $disabledTitle Label link disabled
     * @param array $disabledOption HTML attributes for link disabled (if present, insert a tag SPAN)
     * @return string
     */
    public function first($title = ' |< ', $options = array(), $disabledTitle = ' |< ', $disabledOption = array()) {
        return $this->_scroll('first', $title, $options, $disabledTitle, $disabledOption);
    }

    /**
     * Return the link (html anchor tag) for the last page
     *
     * @param string $title Label link
     * @param array $option HTML attributes for link
     * @param string $disabledTitle Label link disabled
     * @param array $disabledOption HTML attributes for link disabled (if present, insert a tag SPAN)
     *
     * @return string
     */
    public function last($title = ' >| ', $options = array(), $disabledTitle = ' >| ', $disabledOption = array()) {
        return $this->_scroll('last', $title, $options, $disabledTitle, $disabledOption);
    }

    /**
     * Return number of records found
     *
     * @return mixed int|string
     */
    public function size() {
        return Set::classicExtract($this->params, 'toolbar.size', '');
    }

    /**
     * Return current page
     *
     * @return mixed int|string
     */
    public function current() {
        return Set::classicExtract($this->params, 'toolbar.page', '');
    }

    /**
     * Return total number of pages
     *
     * @return mixed int|string
     */
    public function pages() {
        return Set::classicExtract($this->params, 'toolbar.pages', '');
    }

    /**
     * View page size html select tag
     *
     * @param array $htmlAttributes Associative Array with HTML attributes
     * @param array $options The options
     * @return string
     */
    public function changeDim($htmlAttributes = array(), $options = array()) {
        if (!isset($this->params['toolbar']['dim'])) {
            return '';
        }
        if (empty($options)) {
            $options = $this->changePageDefaultOptions;
        }

        // Define script for page change
        $data = $this->getPassedArgs();
        $data['dim'] = '__DIM__';
        $data['page'] = 1;
        $url = $this->getUrl($data);
        $htmlAttributes['onchange'] = "document.location = '{$url}'.replace('__DIM__', this[this.selectedIndex].value)";

        $tmp = array();
        foreach ($options as $k) $tmp[$k] = $k;
        $options = $tmp;

        return $this->Form->select('', $options, $this->params['toolbar']['dim'], $htmlAttributes, false);
    }

    /**
     * Return dropdown list with changed dimension
     * 
     * @param string $selectId The combo|select ID
     * @param array $htmlAttributes Associative Array with HTML attributes
     * @param array $options The options
     *
     * @return string
     */
    public function changeDimSelect($selectId, $htmlAttributes = array(), $options = array()) {
        if (!isset($this->params['toolbar']['dim'])) {
            return '';
        }
        if (empty($options)) {
            $options = array_combine($this->changePageDefaultOptions, $this->changePageDefaultOptions);
        }

        // Define script for page change
        $data = $this->getPassedArgs();
        unset($data["page"]);
        $data['dim'] = '__DIM__';
        $url = $this->getUrl($data);
        $htmlAttributes['onchange'] = "if ($('#loading')) { $('#loading').show(); } document.location = '{$url}'.replace('__DIM__', this[this.selectedIndex].value)";

        return $this->Form->select($selectId, $options, $this->params['toolbar']['dim'], $htmlAttributes, false);
    }

    /**
     * Change selected page
     *
     * @param array $htmlAttributes Associative Array with HTML attributes
     * @param array $items Number of available pages, before and after current. Default: 5
     *
     * @return string
     */
    public function changePage($htmlAttributes = array(), $items = 5) {
        if (!isset($this->params['toolbar']['page'])) {
            return '';
        }

        // Define script for page change
        $data = $this->getPassedArgs();
        $data['page'] = '__PAGE__';
        $url = $this->getUrl($data);

        $htmlAttributes['onchange'] = "document.location = '{$url}'.replace('__PAGE__', this[this.selectedIndex].value)";

        // Define the number of pages available
        $pages = array();
        for ($i = $this->params['toolbar']['page']; $i >= 1; $i--) {
            $pages[] =  $i;
        }
        for ($i = $this->params['toolbar']['page']; $i <= $this->params['toolbar']['pages']; $i++) {
            $pages[] =  $i;
        }
        sort($pages);

        // View select
        $tmp = array();
        foreach ($pages as $k) $tmp[$k] = $k;
        $pages = $tmp;

        return $this->Form->select('', $pages, $this->params['toolbar']['page'], $htmlAttributes, false);
    }

    /**
     * Change page in a dropdown list of results
     * 
     * @param string $selectId The combo|select ID
     * @param array $htmlAttributes Associative Array with HTML attributes
     * @param array $items Number of available pages, before and after current. Default: 5
     *
     * @return string
     */
    public function changePageSelect($selectId, $htmlAttributes = array(), $items = 5) {
        if (!isset($this->params['toolbar']['page'])) return '';

        // Define script for page change
        $data = $this->getPassedArgs();
        $data['page'] = '__PAGE__';
        $url = $this->getUrl($data);
        $htmlAttributes['onchange'] = "document.location = '{$url}'.replace('__PAGE__', this[this.selectedIndex].value)";

        // Define the number of pages available
        $pages = array();
        for ($i = $this->params['toolbar']['page']; $i >= 1; $i--) {
            $pages[] =  $i;
        }

        for ($i = $this->params['toolbar']['page']; $i <= $this->params['toolbar']['pages']; $i++) {
            $pages[] =  $i;
        }
        sort($pages);

        // View select
        $tmp = array();
        foreach ($pages as $k) $tmp[$k] = $k;
        $pages = $tmp;

        return $this->Form->select($selectId, $pages, $this->params['toolbar']['page'], $htmlAttributes, false);
    }

    /**
     * Change page as input text.
     * Onchange, reload page to specified page number
     * 
     * @return string
     */
    public function changePageInput() {
        $current = $this->current();
        $data = $this->getPassedArgs();
        $data['page'] = '__PAGE__';
        $url = $this->getUrl($data);
        $options = array(
            'div' => false,
            'label' => false,
            'placeholder' => $current,
            'value' => $current,
            'class' => 'paginationPage',
            'size' => 2,
            'onchange' => "if (this.value.length > 0) { if ($('#loading')) { $('#loading').show(); } document.location = '{$url}'.replace('__PAGE__', this.value); }",
            'onkeyup' => 'this.value = this.value.replace(/\D/g,\'\')',
            'onkeypress' => 'if (event.keyCode === 13) { event.preventDefault(); event.stopPropagation(); this.blur(); }',
        );

        return $this->Form->input('', $options);
    }

    /**
     * Change list order
     *
     * @param string $field Field for the "order by"
     * @param string $title Title for the link. Default: field name
     * @param array $htmlAttributes Associative Array with HTML attributes
     * @param boolean $dir If any, specify direction. 1: ascending, 0: descending; otherwise, !(<current value>)
     *
     * @return string
     */
    public function order($field, $title = '', $image = '', $htmlAttributes = array(), $dir = null) {
        if (!isset($this->params['toolbar'])) {
            return '';
        }

        $data = $this->getPassedArgs();
        if (isset($data['order']) && $data['order'] == $field) {
            if (!isset($dir)) {
                $dir = (isset($data['dir'])) ? (!$data['dir']) : true;
            }
            if ($dir == 1) {
                $class = "SortUp desc";
            } else {
                $class = "SortUp asc";
            }
        }  else {
            if (!isset($dir)) {
                $dir = true;
            }
            $class = '';
        }

        // Crea l'url
        $data['order'] = $field;
        $data['dir'] = (integer)$dir;
        $url = $this->getUrl($data);
        
        if (!empty($image)) {
            $htmlAttributes["alt"] = __($htmlAttributes["alt"], true);
            $title = $this->Html->image($image, $htmlAttributes);
        } else {
            $title = __($title, true);
        }

        return sprintf('<a class="%s" href="%s">%s</a>', $class, htmlentities($url), $title);
    }

    /**
     * Return the link (html anchor tag) for the page $where
     *
     * @param string $where Page target (next, prev, first, last)
     * @param string $title Label link
     * @param array $option HTML attributes for link
     * @param string $disabledTitle Label link disabled
     * @param array  $disabledOption HTML attributes for link disabled (if present, insert a tag SPAN)
     *
     * @return string
     */
    private function _scroll($where, $title, $options, $disabledTitle, $disabledOption) {
        $page = (isset($this->params['toolbar'][$where]))? $this->params['toolbar'][$where] : false;

        // Next page not found or toolbar not found, link disabled
        if (!$page) {
            return $this->_output($disabledTitle, $disabledOption);
        }

        // Create url
        $data = $this->getPassedArgs();
        $data['page'] = $page;
        $url = $this->getUrl($data);

        return sprintf('<a title="go to %s page" href="%s">%s</a>', $where, $url, __($title, true));
    }

    /**
     * Return output for text
     * 
     * @param string $text The output text
     * @param array $options The options
     *
     * @return string
     */
    private function _output($text, $options) {
        return $this->output(
            sprintf(
                    (($text)?$this->tags['with_text']:$this->tags['without_text']),
                    $this->_parseAttributes($options, null, ' ', ''), __($text,true)
            )
        );
    }

    /**
     * Get array arguments
     * 
     * @param array $otherParams Other parameters
     * @return array
     */
    public function getPassedArgs($otherParams=array()) {
        $query = array_diff_assoc($this->params['url'], $this->params['named'], $this->params['toolbar']);
        unset($query['url']);

        return array_merge(
            $this->params['pass'],
            $this->params['named'],
            array('?' => $query),
            $otherParams ?: array()
        );
    }
    
    /**
     * Fix the parameter $data to correct work with plugin modules. 
     * It must be manually removed from the data array.
     *
     * @param array $data The data to parse
     *
     * @return string
     */
    
    private function getUrl($data) {
        $data['plugin'] = '';
        if (!empty($this->params['prefix']) && !empty($data['page'])) {
            $data[$this->params['prefix'] . 'page'] = $data['page'];
            unset($data['page']);
        }

        return Router::url($data);
    }

    /**
     * Return toolbar by type.
     *
     * @param string $type The view type, can be 'compact' or default
     * @param array $params The parameters, can be 'itemName' and 'noitem'
     * @return string
     */
    public function show($type = 'default', $params = array()) {
        $this->_itemName = Set::classicExtract($params, 'itemName', null);
        $this->_noitem = Set::classicExtract($params, 'noitem', null);
        $this->_name = (!empty($this->_itemName)) ? Inflector::pluralize($this->_itemName) : $this->_moduleName;
        if ($type === 'compact') {
            $content = $this->pageCount(); // i.e. '12823 documents'
            $separator = ' | ';
            $this->separator($content, $separator);
            $content.= $this->pageNav(); // i.e. '(1) ... (61) 62 (63) ... (100)'
            $this->separator($content, $separator);
            $content.= $this->pageSize(); // i.e. 'Size <20>'
            $this->separator($content, $separator);
            $content.= $this->pagePrev(); // i.e. 'Prev'
            $this->separator($content, $separator);
            $content.= $this->pageNext(); // i.e. 'Next'

            return sprintf('<div class="toolbar">%s</div>', $content);
        }

        $content = sprintf('<h2>%s%s</h2>', $this->pageHeader(), $this->pageQuery());
        $content.= $this->pagePagination();

        return sprintf('<div class="toolbar">%s</div>', $content);
    }

    /**
     * Apply separator to specified html, if not empty
     *
     * @param string $html The html string
     * @param string $separator The separator
     * @return void
     */
    private function separator(&$html, $separator) {
        if (!empty($html)) {
            $html.= $separator;
        }
    }
    
    /**
     * Page title, combining publication|section|content title
     *
     * @return string
     */
    public function pageHeader() {
        $title = Set::classicExtract($this->_view->viewVars, 'title', null);
        if (!empty($title)) {
            return $title;
        }
        $section = Set::classicExtract($this->_view->viewVars, 'sectionSel', null);
        $publication = Set::classicExtract($this->_view->viewVars, 'pubSel', null);
        if (!empty($section)) {
            $title = Sanitize::escape($section['title']);

            return sprintf('%s in “ <span style="color:white" class="evidence">%s</span> ”', $this->_name, $title);
        }
        if (!empty($publication)) {
            $title = Sanitize::escape($publication['title']);

            return sprintf('%s in “ <span style="color:white" class="evidence">%s</span> ”', $this->_name, $title);
        }

        return __(sprintf('all %s', $this->_name), true); // default
    }

    /**
     * Page query data, info about matching query, if any
     *
     * @return string
     */
    public function pageQuery() {
        if ($this->SessionFilter->check('query')) {
            $html = __('{t}matching the query{/t}', true);
            if ($this->SessionFilter->check('substring')) {
                $html = __('{t}matching the query containing{/t}', true);
            }

            return sprintf('%s: “ <span style="color:white" class="evidence">%s</span> ”', $html, $this->SessionFilter->read('query'));
        }

        return '';
    }

    /**
     * Page pagination data, info about page(s), links, etc.
     *
     * @return string
     */
    public function pagePagination() {
        $cells = '';
        $moduleModify = Set::classicExtract($this->_view, 'viewVars.module_modify', null);
        if ($moduleModify === "1" && empty($_noitem)) {
            $title = __('Create new', true) . ' &nbsp;';
            if (!empty($this->_itemName)) {
                $title.= __($this->_itemName, true);
            } else {
                $objectTypes = Set::classicExtract($this->_conf, 'objectTypes');
                $leafs = Set::classicExtract($this->_conf, 'objectTypes.leafs');
                $isFirst = true;
                foreach ($objectTypes as $key => $type) {
                    if (in_array($type['id'], $leafs['id']) && is_numeric($key) && $type['module_name'] == $this->_currentModule['name']) {
                        if (!$isFirst) {
                            $title.= '&nbsp;/&nbsp;';
                        }
                        $title.= __(strtolower($type['model']), true);
                        $isFirst = false;
                    }
                }
            }
            $anchor = sprintf('<a href="%s">%s</a>', $this->Html->url(sprintf('/%s/view', $this->_currentModule['url'])), $title);
            $cells = sprintf('<td>%s</td>', $anchor);
        }
        $cells.= sprintf('<td>%s</td>', $this->pageCount());
        $cells.= sprintf('<td>%s</td>', $this->pageNav());
        $cells.= sprintf('<td>%s</td>', $this->pageSize());
        $cells.= sprintf('<td>%s</td>', $this->pagePrev());
        $cells.= sprintf('<td>%s</td>', $this->pageNext());
        $rows = sprintf('<tr>%s</tr>', $cells);

        return sprintf('<table>%s</table>', $rows);
    }

    /**
     * Return number of items per object type
     *
     * @return string
     */
    public function pageCount() {
        return sprintf('<span class="evidence">%d &nbsp;</span> %s', $this->size(), __($this->_name, true));
    }

    /**
     * Page nav data for page.
     * Shows 'page <i> of <n>', where <i> is an input field
     *
     * @return string
     */
    public function pageNav() {
        $pages = $this->pages();
        $last = ($pages > 0) ? $this->last($pages, '', $pages) : '1';
        $label = sprintf('<span>%s</span>', __('page', true));
        $input = sprintf('<span class="evidence">%s</span>', $this->changePageInput());
        $of = __('of', true);
        $total = sprintf('<span class="evidence">%s</span>', $last);

        return sprintf('%s&nbsp;%s&nbsp;%s&nbsp;%s', $label, $input, $of, $total);
    }

    /**
     * Link to previous page
     *
     * @return string
     */
    public function pagePrev() {
        return sprintf('%s <span class="evidence">&nbsp;</span>', $this->prev('prev', '', 'prev'));
    }

    /**
     * Link to next page
     *
     * @return string
     */
    public function pageNext() {
        return sprintf('%s <span class="evidence">&nbsp;</span>', $this->next('next', '', 'next'));
    }

    /**
     * Page size data for page
     *
     * @return string
     */
    public function pageSize() {
        return sprintf('%s: %s', __('size', true), $this->changeDimSelect('selectTop'));
    }
}
