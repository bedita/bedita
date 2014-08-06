<?php
/**-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2014 ChannelWeb Srl, Chialab Srl
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
namespace BEdita\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	/**
	 * Components this controller uses.
	 *
	 * Component names should not include the `Component` suffix. Components
	 * declared in subclasses will be merged with components declared here.
	 *
	 * @var array
	 */
	public $components = [
		'Flash',
		'Session',
		'Auth' => [
			'authenticate' => [
				'BEdita' => [
					'fields' => [
						'username' => 'userid',
						'password' => 'passwd'
					],
					'scope' => [
						'Users.valid' => true
					],
					'contain' => ['Groups'],
					'passwordHasher' => [
						'className' => 'Fallback',
						'hashers' => ['Default', 'Md5']
					]
				]
			],
			'authorize' => ['Group'],
			'unauthorizedRedirect' => '/users/login'
		]
	];
	
	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);
		$this->viewClass = 'Smarty';
	}
}
