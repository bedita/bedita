<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace BEdita\Controller;

use Cake\Core\Configure;
use Cake\Error;
use Cake\Utility\Inflector;
use Cake\ORM\TableRegistry;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * Displays a view
 *
 * @return void
 * @throws Cake\Error\NotFoundException When the view file could not be found
 *    or Cake\Error\MissingViewException in debug mode.
 */
	public function display() {

		debug($this->Auth->user());

		$images = TableRegistry::get('ImageObjects');
		//$query = $images->find('all', ['formatResults' => false]);
		$query = $images->find();
		$query->contain([
			'Contents',
			'Streams',
			'Images',
			'ObjectTypes',
			'UserCreated',
			'UserModified',
			'Permissions',
			'Versions',
			'ObjectProperties',
			'ObjectRelations',
			'Categories',
			'Tags',
			'Users'
		]);
		//$query->hydrate(false);
		$row = $query->first();

		if ($row) {
			//debug($row->toArray());

			// $imageData = [];
			// $imageData['license'] = 'licenza tua';
			// $imageData['body'] = 'body';
			// $imageData['start_date'] = '2014-05-29';

			// $imageEntity = $images->newEntity($imageData);
			// debug($imageEntity->toArray());
			// $images->save($imageEntity);
			// exit;
	 		// $images->patchEntity($row, $imageData);

	 		// $images->save($row);

		}

		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render(implode('/', $path));
		} catch (Error\MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new Error\NotFoundException();
		}
	}
}
