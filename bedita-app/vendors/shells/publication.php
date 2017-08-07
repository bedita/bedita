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
        $this->trackInfo('Create end');
    }

    public function createContents() {
        $this->hr();
        $this->trackInfo('Create contents start');
        if (isset($this->params['t'])) {
            $this->options['objectType'] = $this->params['t'];
        } else {
            $this->options['objectType'] = 'Document';
        }
        if (isset($this->params['n'])) {
            $this->options['number'] = $this->params['n'];
        } else {
            $this->options['number'] = 1;
        }
        if (isset($this->params['pid'])) {
            $this->options['parentId'] = $this->params['pid'];
        }
        if (isset($this->params['tpf'])) {
            $this->options['titlePostFix'] = $this->params['tpf'];
        }
        if (isset($this->params['uri'])) {
        	$this->options['uri'] = $this->params['uri'];
        }
        $optionsString = '';
        foreach ($this->options as $key => $value) {
            $optionsString .= ' | ' . $key . ': ' . $value;
        }
        $this->trackInfo('Options: ' . $optionsString);
        try {
            $this->createMulti($this->options);
        } catch(BeditaException $e) {
            $this->trackInfo('Exception: ' . $e->getMessage());
        }
        $this->trackInfo('Create contents end');
    }

    /* private methods */

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

    private function createObject($parentId, $objectType = 'Document', $nickname = 'document-1', $title = null, $uri = null) {
        $data = array(
            'name' => $objectType . ' ' . $nickname,
            'title' => ($title!=null) ? $title : $objectType . ' ' . $nickname,
            'nickname' => $nickname
        );
        if ($parentId != null) {
            $data['parent_id'] = $parentId;
        }
        if ($uri != null) {
        	$data['uri'] = $uri;
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

    private function createMulti($params) {
        $parentId = (!empty($params['parentId'])) ? $params['parentId'] : null;
        $numElems = $params['number'];
        for($i=0; $i<$numElems; $i++) {
            $index = $i+1;
            $title = $params['objectType'] . ' ' . $index;
            if (!empty($params['titlePostFix'])) {
                $title.= ' ' . $params['titlePostFix'];
            }
            $nickname = strtolower($params['objectType']) . '-' . $index;
            if (!empty($params['titlePostFix'])) {
                $nickname.= '-' . strtolower($params['titlePostFix']);
            }
            $uri = null;
            if (!empty($params['uri'])) {
            	$uri = $params['uri'];
            }
            $this->createObject($parentId, $params['objectType'], $nickname, $title, $uri);
        }
    }

    public function remove() {
        $this->hr();
        if (!isset($this->params['ids'])) {
            $this->out('Missing param -ids');
            $this->out('Bye');
            return;
        }
        $deleteContents = (isset($this->params['deleteContents']));
        $dbCfg = "default";
        App::import('Component', 'Transaction');
        $transaction = new TransactionComponent($dbCfg);
        $ids = explode(',', $this->params['ids']);
        foreach ($ids as $id) {
            $area = ClassRegistry::init('Area')->find('all', array(
                'fields' => array('id'),
                'conditions' => array('id' => $id),
                'contain' => array()
            ));
            if (empty($area)) {
                $this->out('Publication ' . $id . ' not found');
            } else {
                $transaction->begin();
                $tree = ClassRegistry::init('Tree')->find('all', array(
                    'fields' => array('id'),
                    'conditions' => array('id' => $id),
                    'contain' => array()
                ));
                $descendants = array();
                $oo = ClassRegistry::init('Tree')->getDescendants($id, null, null, array('object_type_id' => Configure::read('objectTypes.section.id')));
                if (!empty($oo['items'])) {
                    $descendants = Set::extract('/id', $oo['items']);
                }
                $contents = array();
                if ($deleteContents) {
                    $contents = ClassRegistry::init('Tree')->getDescendants($id, null, null, array('NOT' => array('BEObject.object_type_id' => Configure::read('objectTypes.section.id'))));
                    $contents = Set::extract('/id', $contents['items']);
                }
                if (!empty($tree)) {
                    $res = ClassRegistry::init('Tree')->removeTree($id);
                    if ($res === false) {
                        $this->out('Error in deleting tree for publication ' . $id);
                        $transaction->rollback();
                        break;
                    } else {
                        $this->out('Publication ' . $id . ' tree deleted');
                    }
                }
                if (!empty($descendants)) {
                    foreach ($descendants as $descendantId) {
                        $res = ClassRegistry::init('BEObject')->delete($descendantId);
                        if ($res === false) {
                            $this->out('Error in deleting section ' . $descendantId);
                            $transaction->rollback();
                            break;
                        }
                    }
                }
                $ubiqContents = 0;
                $numDelContents = 0;
                if ($deleteContents && !empty($contents)) {
                    foreach ($contents as $contentId) {
                        $contentParents = ClassRegistry::init('Tree')->getParents($contentId);
                        if (count($contentParents) == 0) { // content is not elsewhere
                            $res = ClassRegistry::init('BEObject')->delete($contentId);
                            if ($res === false) {
                                $this->out('Error in deleting content ' . $contentId);
                                $transaction->rollback();
                                break;
                            }
                            $numDelContents++;
                        } else {
                            $ubiqContents++;
                        }
                    }
                }
                $res = ClassRegistry::init('BEObject')->delete($id);
                if ($res === false) {
                    $this->out('Error in deleting publication ' . $id);
                    $transaction->rollback();
                    break;
                } else {
                    $this->out('Publication ' . $id . ' deleted');
                    if ($deleteContents) {
                        if ($numDelContents > 0) {
                            $this->out($numDelContents . ' contents deleted');
                        }
                        if ($ubiqContents > 0) {
                            $this->out($ubiqContents . ' contents not deleted (found in other publication(s)/section(s))');
                        }
                    }
                }
                $transaction->commit();
            }
        }
    }

    public function stats() {
        $areaModel = ClassRegistry::init('Area');        
        if (isset($this->params['id'])) {
            $result = $areaModel->getArea($this->params['id'], true);
        } else {
            $result = $areaModel->getAreas(true);
        }
        $noask = isset($this->params['noask']);
        if (!empty($result)) {
            $this->out(count(array_keys($result)) . ' publication(s) found');
            $this->out('-------------------------------------------------------------------');
            foreach ($result as $r) {
                $this->out('id: "' . $r['id'] . '" | title: "' . $r['title'] . '" | status: "' . $r['status'] . '"');
                $this->out('-------------------------------------------------------------------');
                $this->out($r['count']['allTypes'] . ' objects');
                foreach ($r['count']['byType'] as $otype => $count) {
                    $this->out('|=> ' . $count . ' ' . $otype . '(s)');
                }
                $this->out('-------------------------------------------------------------------');

                $response = ($noask) ? 'y' : $this->in('Continue?', array('y', 'n'), 'y');
                if ($response === 'n') {
                    $this->out("Bye");
                    return;
                }
                $this->out('-------------------------------------------------------------------');
            }
        } else {
            $this->out('No publication found');
        }
    }

    public function help() {
        $this->hr();
        $this->out('publication script shell usage:');
        $this->out('');
        $this->out('./cake.sh publication create [-d <depth> [-ns <sublevel-number-of-sections>] [-nd <leafs-number-of-documents>]');
        $this->out('./cake.sh publication createContents [-t <type>] [-n <number>] [-pid <parentId>] [-tpf <titlePostFix>] [-uri <uriInsideMediaFolder>]');
        $this->out('./cake.sh publication remove -ids <publicationIds> (comma separated id list string) [-deleteContents (remove contents, if not ubiquitous)]');
        $this->out('./cake.sh publication stats [-id <publicationId>] [-noask (interactive mode off)]');
        $this->out('');
    }

    private function trackInfo($s, $param = null) {
        echo $s . "\n";
    }
}
?>