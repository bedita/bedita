<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeBotrComponent extends Object {
	
	private $cfg = array(
		'api_key'       => '9a7b72c4210d05ace1acc85abf9c1721',
		'api_secret'    => 'f27b477c723c8a00',
        'endpoint'      => 'http://manage.bitsontherun.com/services/',
        'auth_endpoint' => 'http://manage.bitsontherun.com/services/auth/?',
        'feed_endpoint'	=> 'http://manage.bitsontherun.com/services/feeds',
		'conn_timeout'	=> 5,
		'io_timeout'	=> 5,
	);
	
	function startup(&$controller) {
		$this->controller 	= $controller;
	}
	
	function getAuthUrl($perms, $frob=''){

		$args = array(
			'api_key'	=> $this->cfg['api_key'],
			'perms'		=> $perms,
		);

		if (strlen($frob)){ $args['frob'] = $frob; }

		$args['api_sig'] = $this->signArgs($args);

		#
		# build the url params
		#

		$pairs =  array();
		foreach($args as $k => $v){
			$pairs[] = urlencode($k).'='.urlencode($v);
		}

		return $this->cfg['auth_endpoint'].implode('&', $pairs);
	}
	
	function signArgs($args){
		ksort($args);
		$a = '';
		foreach($args as $k => $v){
			$a .= $k . $v;
		}
		return md5($this->cfg['api_secret'].$a);
	}
 	
}
 
?>