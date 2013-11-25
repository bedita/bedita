<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
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
 *
 *
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $LastChangedDate$
 *
 * $Id$
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class RestClientTestCase extends BeditaTestCase {

    public $uses = array('RestClientModel');

    public $dataSource = 'test';

    public function __construct () {
        parent::__construct('RestClient', dirname(__FILE__)) ;
    }

    /////////////////////////////////////////////////
    //      TEST METHODS
    /////////////////////////////////////////////////
    public function testGithubGetCall() {

        $this->requiredData(array("github"));
        $baseUrl = $this->data['github']['url'];
        $OKstatus = '200 OK';
        $skipUnitTest = false;

        $this->RestClientModel->setup();

        // unit test work only with curl, skip if curl not used
        // @todo: adapt unit test to work with HttpSocket CakePHP
        if ($this->RestClientModel->useCurl) {
            $this->RestClientModel->setOptions($this->data['github']['curlOptions']);
            $response = $this->RestClientModel->get($baseUrl . $this->data['github']['rateLimit']);
            $result = $this->githubResponse($response);

            // if it's finished rate limit (60 call for hour) skip unittest
            if (!empty($result['headers']['X-RateLimit-Remaining']) && $result['headers']['X-RateLimit-Remaining'] > 0) {

                // connection ok
                $this->assertEqual($result['headers']['Status'], $OKstatus);

                // test requests
                foreach ($this->data['github']['requests'] as $request) {
                    $response = $this->RestClientModel->get($baseUrl . $request);
                    $result = $this->githubResponse($response);
                    $this->assertEqual($result['headers']['Status'], $OKstatus);
                    if ($result['headers']['X-RateLimit-Remaining'] == 0) {
                        $skipUnitTest = true;
                        break;
                    }
                }

                // test wrong request
                if (!$skipUnitTest) {
                    $response = $this->RestClientModel->get($baseUrl . $this->data['github']['wrongRequest']);
                    $result = $this->githubResponse($response);
                    $this->assertNotEqual($result['headers']['Status'], $OKstatus);
                }
            }
        }

    }

    private function githubResponse($originalResponse) {
        $response = array('headers' => array(), 'jsonContent' => '', 'content' => '');
        $res = explode("\r\n", $originalResponse);
        if (count($res) > 1) {
            $response['headers']['httpHeader'] = array_shift($res);
            $response['jsonContent'] = array_pop($res);
            if (!empty($res)) {
                foreach ($res as $row) {
                    $matches = array();
                    if (preg_match("/(.+):\s(.+)/", $row, $matches)) {
                        $response['headers'][$matches[1]] = $matches[2];
                    }
                }
            }
        } else {
            $response['jsonContent'] = $res[0];
        }
        $response['content'] = json_decode($response['jsonContent'], true);
        return $response;
    }

}
