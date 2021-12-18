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
 * Users toolbar for pagination
 */
class BeUsersToolbarHelper extends AppHelper
{
    /**
     * Included helpers.
     *
     * @var array
     */
    public $helpers = array('Form', 'Html', 'Paginator', 'SessionFilter');

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
     * Default options for change page combo
     *
     * @var array
     */
    protected $changePageDefaultOptions = array(1, 5, 10, 20, 50, 100);

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
     * Return toolbar by type.
     *
     * @param string $type The view type, can be 'compact' or default
     * @return string
     */
    public function show($type = 'default', $options = array()) {
        $itemNameEng = Set::classicExtract($options, 'name');
        // if ($itemNameEng === null) {
        //     $itemNameEng = ($this->_view->action === 'index') ? 'User' : 'Group';
        // }
        if ($itemNameEng === null) {
            $itemNameEng = $this->_currentModule['name'];
        }

        $this->_itemName = __($itemNameEng, true);
        $this->_noitem = null;
        $this->_name = Inflector::pluralize($itemNameEng);
        
        if ($type === 'compact') {
            $content = $this->pageCount(); // i.e. '12823 documents'
            $separator = ' <span class="separator"></span> ';
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

        $content = sprintf('<h2>%s %s</h2>', $this->pageHeader($options), $this->pageQuery());
        $content.= $this->pagePagination($options);

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
     * Page title, System users or Users groups
     *
     * @return string
     */
    public function pageHeader(array $options) {
        // if ($this->_view->action === 'index') {
        //     return __('System users', true);
        // }
        // if ($this->_view->action === 'groups') {
        //     return __('User groups', true);
        // }
        $headerName = Set::classicExtract($options, 'headerName');
        if ($headerName === null) {
            return Inflector::humanize($this->_name);
        }
        
        return Inflector::humanize(__($headerName, true));
    }

    /**
     * Page query data, info about matching query, if any
     *
     * @return string
     */
    public function pageQuery() {
        if ($this->SessionFilter->check('query')) {
            $html = __('matching the query', true);
            if ($this->SessionFilter->check('substring')) {
                $html = __('matching the query containing', true);
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
    public function pagePagination(array $options) {
        $newAction = Set::classicExtract($options, 'newAction');
        if ($newAction === null) {
            $newAction = 'view';
        }
        $cells = '';
        $moduleModify = Set::classicExtract($this->_view, 'viewVars.module_modify', null);
        if ($moduleModify === "1" && empty($_noitem)) {
            $title = __('Create new', true) . '&nbsp;' . $this->_itemName;
            $url = $this->Html->url(sprintf('/%s/%s', $this->_currentModule['name'], $newAction));
            $anchor = sprintf('<a href="%s">%s</a>', $url, $title);
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
        $last = $this->last();
        $label = sprintf('<span>%s</span>', __('page', true));
        $input = sprintf('<span class="evidence">%s</span>', $this->changePageInput());
        $of = __('of', true);
        $total = sprintf('<span class="evidence">&nbsp;%s</span>', $last);

        return sprintf('%s&nbsp;%s&nbsp;%s&nbsp;%s', $label, $input, $of, $total);
    }

    /**
     * Link to previous page
     *
     * @return string
     */
    public function pagePrev() {
        return sprintf('%s <span class="evidence">&nbsp;</span>', $this->prev());
    }

    /**
     * Link to next page
     *
     * @return string
     */
    public function pageNext() {
        return sprintf('%s <span class="evidence">&nbsp;</span>', $this->next());
    }

    /**
     * Page size data for page
     *
     * @return string
     */
    public function pageSize() {
        return sprintf('%s: %s', __('size', true), $this->changeDimSelect('selectTop'));
    }

    /**
     * Return number of records found
     *
     * @return mixed int|string
     */
    public function size() {
        return Set::classicExtract($this->Paginator->params(), 'count', '');
    }

    /**
     * Return current page
     *
     * @return mixed int|string
     */
    public function current() {
        return Set::classicExtract($this->Paginator->params(), 'page', '');
    }

    /**
     * Return the link (html anchor tag) for the previous page
     *
     * @return void
     */
    public function prev() {
        return $this->Paginator->prev(__('prev', true), null, __('prev', true), array('style' => 'display: inline'));
    }

    /**
     * Return the link (html anchor tag) for the next page
     *
     * @return void
     */
    public function next() {
        return $this->Paginator->next(__('next', true), null, __('next', true), array('style' => 'display: inline'));
    }

    /**
     * Return the link (html anchor tag) for the last page
     *
     * @return void
     */
    public function last() {
        return $this->Paginator->last(Set::classicExtract($this->Paginator->params(), 'pageCount', '1'));
    }

    /**
     * Change page as input text.
     * Onchange, reload page to specified page number
     * 
     * @return string
     */
    public function changePageInput() {
        if ($this->last() <= 1) {
            return "1";
        }
        $current = $this->current();
        $data = $this->getPassedArgs();
        $data['page'] = '__PAGE__';
        $url = $this->Paginator->url($data);
        $options = array(
            'type' => 'text',
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
     * Return dropdown list with changed dimension
     * 
     * @param string $selectId The combo|select ID
     * @param array $htmlAttributes Associative Array with HTML attributes
     * @param array $options The options
     *
     * @return string
     */
    public function changeDimSelect($selectId, $htmlAttributes = array(), $options = array()) {
        if (empty($options)) {
            $options = array_combine($this->changePageDefaultOptions, $this->changePageDefaultOptions);
        }

        // Define script for page change
        $data = $this->getPassedArgs();
        unset($data['page']);
        $data['limit'] = '__LIMIT__';
        $url = $this->Paginator->url($data);
        $htmlAttributes['onchange'] = "if ($('#loading')) { $('#loading').show(); } document.location = '{$url}'.replace('__LIMIT__', this[this.selectedIndex].value)";
        $limit = Set::classicExtract($this->Paginator->params(), 'options.limit');
        if (empty($limit)) {
            $limit = Set::classicExtract($this->Paginator->params(), 'defaults.limit');
        }

        return $this->Form->select($selectId, $options, $limit, $htmlAttributes, false);
    }

    /**
     * Get array arguments
     *
     * @return array
     */
    public function getPassedArgs() {
        $params = $this->Paginator->params();
        $query = array_diff_assoc($this->params['url'], $this->params['named'], $this->Paginator->params());
        unset($query['url']);

        return array_merge(
            $this->params['pass'],
            $this->params['named'],
            array('?' => $query)
        );
    }
}
