<?php
/*-----8<--------------------------------------------------------------------
 *
* BEdita - a semantic content management framework
*
* Copyright 2015 ChannelWeb Srl, Chialab Srl
*
*------------------------------------------------------------------->8-----
*/

require_once APP . DS . 'vendors' . DS . 'shells'. DS . 'bedita_base.php';

class PublicationShell extends BeditaBaseShell {

    protected $objDefaults = array(
        'status' => 'on',
        'user_created' => '1',
        'user_modified' => '1',
        'lang' => 'ita',
        'ip_created' => '127.0.0.1',
        'syndicate' => 'off',
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
    );

    private $options = array(
        'depth' => 3,
        'sublevel-sections' => 3,
        'leaf-documents' => 1
    );

    private $objectFakeId = 1;

    public function create() {

        $this->hr();

        $this->trackInfo('Create start');

        if (isset($this->params['d'])) {
            $this->options['depth'] = $this->params['d'];
        }

        if (isset($this->params['ns'])) {
            $this->options['sublevel-sections'] = $this->params['ns'];
        }

        if (isset($this->params['nd'])) {
            $this->options['leaf-documents'] = $this->params['nd'];
        }

        $optionsString = '';
        foreach ($this->options as $key => $value) {
            $optionsString .= ' | ' . $key . ': ' . $value;
        }
        $this->trackInfo('Options: ' . $optionsString);

        try {
            $this->createPublication();           
        } catch(BeditaException $e) {
            $this->trackInfo('Exception: ' . $e->getMessage());
        }

        // end
        $this->trackInfo('Create end');
    }

    private function createPublication() {
        $depth = 1;
        $publicationId = $this->createObject(null, 'Area', 'publication');
        if ($depth == $this->options['depth']) {
            for ($j=0; $j<$this->options['leaf-documents']; $j++) {
                $this->createObject($publicationId, 'Document', 'publication-' . $publicationId . '-depth-' . $depth .'-document-' . ($j+1));
            }
        } else if ($depth < $this->options['depth']) {
            $depth++;
            for ($i=0; $i<$this->options['sublevel-sections']; $i++) {
                $this->createSection($publicationId, $depth, 'section-' . ($i+1) . '-depth-' . $depth);
            }
        }
    }

    private function createSection($parentId, $depth = 1, $nickname = 'section-1') {
        $sectionId = $this->createObject($parentId, 'Section', $nickname);
        if ($depth == $this->options['depth']) {
            for ($j=0; $j<$this->options['leaf-documents']; $j++) {
                $this->createObject($sectionId, 'Document', 'section-' . $sectionId . '-depth-' . $depth .'-document-' . ($j+1));
            }
        } else if ($depth < $this->options['depth']) {
            $depth++;
            for ($i=0; $i<$this->options['sublevel-sections']; $i++) {
                $this->createSection($sectionId, $depth, 'section-' . ($i+1) . '-depth-' . $depth);
            }
        }
    }

    private function createObject($parentId, $objectType = 'Document', $nickname = 'document-1') {
        $data = array(
            'name' => $objectType . ' ' . $nickname,
            'title' => $objectType . ' ' . $nickname,
            'nickname' => $nickname
        );
        if ($parentId != null) {
            $data['parent_id'] = $parentId;
        }
        $data = array_merge($data, $this->objDefaults);
        $model = ClassRegistry::init($objectType);
        $model->create();
        if (!$model->save($data)) {
            throw new BeditaException('error saving ' . $objectType);
        }
        if (!empty($parentId)) {
            $tree = ClassRegistry::init('Tree');
            $tree->appendChild($model->id, $parentId);
        }
        $this->trackInfo('create' . $objectType . ':::id ' . $model->id . ' | type ' . $objectType);
        return $model->id;
    }

    public function help() {
        $this->hr();
        $this->out('publication script shell usage:');
        $this->out('');
        $this->out('./cake.sh publication create [-d <depth> [-ns <sublevel-number-of-sections>] [-nd <leafs-number-of-documents>]');
        $this->out('');
    }

    private function trackInfo($s, $param = null) {
        echo $s . "\n";
    }
}
?>