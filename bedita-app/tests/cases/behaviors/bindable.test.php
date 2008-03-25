<?php
/* SVN FILE: $Id: bindable.test.php 19 2007-11-23 18:42:23Z mgiglesias $ */

/**
 * Test cases for Bindable Behavior, which are basically testing methods to test several
 * aspects of bindable functionality.
 *
 * Go to the Bindable Behavior page at Cake Syrup to learn more about it:
 *
 * http://cake-syrup.sourceforge.net/ingredients/bindable-behavior/
 *
 * @filesource
 * @author Mariano Iglesias
 * @link http://cake-syrup.sourceforge.net/ingredients/bindable-behavior/
 * @version	$Revision: 19 $
 * @license	http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package app.tests
 * @subpackage app.tests.cases.behaviors
 */

App::import('Behavior', 'bindable');

/**
 * Base model that to load bindable behavior on every test model.
 *
 * @package app.tests
 * @subpackage app.tests.cases.behaviors
 */
class BindableTestModel extends CakeTestModel
{
	/**
	 * Behaviors for this model
	 *
	 * @var array
	 * @access public
	 */
	var $actsAs = array('Bindable');
}

/**
 * Model used in test case.
 *
 * @package	app.tests
 * @subpackage app.tests.cases.behaviors
 */
class Article extends BindableTestModel
{
	/**
	 * Name for this model
	 *
	 * @var string
	 * @access public
	 */
	var $name = 'Article';

	/**
	 * belongsTo bindings for this model
	 *
	 * @var array
	 * @access public
	 */
	var $belongsTo = array('User');

	/**
	 * hasMany bindings for this model
	 *
	 * @var array
	 * @access public
	 */
	var $hasMany = array('Comment');

	/**
	 * hasAndBelongsToMany bindings for this model
	 *
	 * @var array
	 * @access public
	 */
	var $hasAndBelongsToMany = array('Tag');
}

/**
 * Model used in test case.
 *
 * @package	app.tests
 * @subpackage app.tests.cases.behaviors
 */
class ArticleFeatured extends BindableTestModel
{
	/**
	 * Name for this model
	 *
	 * @var string
	 * @access public
	 */
	var $name = 'ArticleFeatured';

	/**
	 * belongsTo bindings for this model
	 *
	 * @var array
	 * @access public
	 */
	var $belongsTo = array('User');

	/**
	 * hasOne bindings for this model
	 *
	 * @var array
	 * @access public
	 */
	var $hasOne = array('Featured');

	/**
	 * hasMany bindings for this model
	 *
	 * @var array
	 * @access public
	 */
	var $hasMany = array('Comment' => array('foreignKey' => 'article_id'));

	/**
	 * hasAndBelongsToMany bindings for this model
	 *
	 * @var array
	 * @access public
	 */
	var $hasAndBelongsToMany = array('Tag');
}

/**
 * Model used in test case.
 *
 * @package	app.tests
 * @subpackage app.tests.cases.behaviors
 */
class Attachment extends BindableTestModel
{
	/**
	 * Name for this model
	 *
	 * @var string
	 * @access public
	 */
	var $name = 'Attachment';
}

/**
 * Model used in test case.
 *
 * @package	app.tests
 * @subpackage app.tests.cases.behaviors
 */
class Category extends BindableTestModel
{
	/**
	 * Name for this model
	 *
	 * @var string
	 * @access public
	 */
	var $name = 'Category';
}

/**
 * Model used in test case.
 *
 * @package	app.tests
 * @subpackage app.tests.cases.behaviors
 */
class Comment extends BindableTestModel
{
	/**
	 * Name for this model
	 *
	 * @var string
	 * @access public
	 */
	var $name = 'Comment';

	/**
	 * belongsTo bindings for this model
	 *
	 * @var array
	 * @access public
	 */
	var $belongsTo = array('Article', 'User');

	/**
	 * hasOne bindings for this model
	 *
	 * @var array
	 * @access public
	 */
	var $hasOne = array('Attachment');
}

/**
 * Model used in test case.
 *
 * @package	app.tests
 * @subpackage app.tests.cases.behaviors
 */
class Featured extends BindableTestModel
{
	/**
	 * Name for this model
	 *
	 * @var string
	 * @access public
	 */
	var $name = 'Featured';

	/**
	 * belongsTo bindings for this model
	 *
	 * @var array
	 * @access public
	 */
	var $belongsTo = array('ArticleFeatured', 'Category');
}

/**
 * Model used in test case.
 *
 * @package	app.tests
 * @subpackage app.tests.cases.behaviors
 */
class Tag extends BindableTestModel
{
	/**
	 * Name for this model
	 *
	 * @var string
	 * @access public
	 */
	var $name = 'Tag';
}

/**
 * Model used in test case.
 *
 * @package	app.tests
 * @subpackage app.tests.cases.behaviors
 */
class User extends BindableTestModel
{
	/**
	 * Name for this model
	 *
	 * @var string
	 * @access public
	 */
	var $name = 'User';

	/**
	 * hasMany bindings for this model
	 *
	 * @var array
	 * @access public
	 */
	var $hasMany = array('Article', 'ArticleFeatured', 'Comment');
}

/**
 * Test case for BindableBehavior
 *
 * @package app.tests
 * @subpackage app.tests.cases.models
 */
class BindableTestCase extends CakeTestCase
{
	/**
	 * Fixtures associated with this test case
	 *
	 * @var array
	 * @access public
	 */
	var $fixtures = array(
		'core.article', 'core.article_featured', 'core.article_featureds_tags', 'core.articles_tag', 'core.attachment', 'core.category',
		'core.comment', 'core.featured', 'core.tag', 'core.user'
	);

	/**
	 * Method executed after each test
	 *
	 * @access public
	 */
	function endTest()
	{
		ClassRegistry::flush();
	}

	/**
	 * Test parsing of parameters in bindable using depth notation,
	 * dot notation, a combination of them, and applying settings
	 *
	 * @access public
	 */
	function testParameters()
	{
		$this->Article =& new Article();
		$Bindable =& new BindableBehavior();

		$result = $Bindable->__models($this->Article, array(), true);
		$expected = array();
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User'), true);
		$expected = array('User' => array());
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User', 'Comment'), true);
		$expected = array('User' => array(), 'Comment' => array());
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => 'ArticleFeatured', 'Comment'), true);
		$expected = array('User' => array('ArticleFeatured' => array()), 'Comment' => array());
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User.ArticleFeatured', 'Comment'), true);
		$expected = array('User' => array('ArticleFeatured' => array()), 'Comment' => array());
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => 'ArticleFeatured', 'Comment' => 'Attachment'), true);
		$expected = array('User' => array('ArticleFeatured' => array()), 'Comment' => array('Attachment' => array()));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User.ArticleFeatured', 'Comment.Attachment'), true);
		$expected = array('User' => array('ArticleFeatured' => array()), 'Comment' => array('Attachment' => array()));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => array('Article', 'Comment'), 'Comment' => 'Attachment'), true);
		$expected = array('User' => array('Article' => array(), 'Comment' => array()), 'Comment' => array('Attachment' => array()));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => array('Article', 'Comment'), 'Comment.Attachment'), true);
		$expected = array('User' => array('Article' => array(), 'Comment' => array()), 'Comment' => array('Attachment' => array()));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => 'ArticleFeatured', 'Comment' => array('Article' => 'Tag')), true);
		$expected = array('User' => array('ArticleFeatured' => array()), 'Comment' => array('Article' => array('Tag' => array())));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User.Comment.Article.Tag'), true);
		$expected = array('User' => array('Comment' => array('Article' => array('Tag' => array()))));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User.Comment.Article' => 'Tag'), true);
		$expected = array('User' => array('Comment' => array('Article' => array('Tag' => array()))));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => 'Comment.Article.Tag'), true);
		$expected = array('User' => array('Comment' => array('Article' => array('Tag' => array()))));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => array('Comment' => array('User' => array('ArticleFeatured' => 'Tag')))), true);
		$expected = array('User' => array('Comment' => array('User' => array('ArticleFeatured' => array('Tag' => array())))));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => array('Comment.User.ArticleFeatured' => 'Tag')), true);
		$expected = array('User' => array('Comment' => array('User' => array('ArticleFeatured' => array('Tag' => array())))));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User.Comment' => array('User.ArticleFeatured' => 'Tag')), true);
		$expected = array('User' => array('Comment' => array('User' => array('ArticleFeatured' => array('Tag' => array())))));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User.Comment' => 'User.ArticleFeatured.Tag'), true);
		$expected = array('User' => array('Comment' => array('User' => array('ArticleFeatured' => array('Tag' => array())))));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => array('id', 'user'), 'Comment'), true);
		$expected = array('User' => array('__settings__' => array(
			'fields' => array('id', 'user')
		)), 'Comment' => array());
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => array('user', 'password'), 'Comment' => 'comment'), true);
		$expected = array(
			'User' => array('__settings__' => array('fields' => array('user', 'password'))),
			'Comment' => array('__settings__' => array('fields' => array('comment')))
		);
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => array('fields' => array('user', 'password')), 'Comment' => 'comment'), true);
		$expected = array(
			'User' => array('__settings__' => array('fields' => array('user', 'password'))),
			'Comment' => array('__settings__' => array('fields' => array('comment')))
		);
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => array('user', 'password'), 'Comment' => array('comment', 'limit' => 5)), true);
		$expected = array(
			'User' => array('__settings__' => array('fields' => array('user', 'password'))),
			'Comment' => array('__settings__' => array('fields' => array('comment'), 'limit' => 5))
		);
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => array('user', 'password', 'order' => 'user ASC'), 'Comment' => array('comment', 'limit' => 5)), true);
		$expected = array(
			'User' => array('__settings__' => array('fields' => array('user', 'password'), 'order' => 'user ASC')),
			'Comment' => array('__settings__' => array('fields' => array('comment'), 'limit' => 5))
		);
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User(id, user)', 'Comment'), true);
		$expected = array('User' => array('__settings__' => array(
			'fields' => array('id', 'user')
		)), 'Comment' => array());
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User(user, password)', 'Comment(comment)'), true);
		$expected = array(
			'User' => array('__settings__' => array('fields' => array('user', 'password'))),
			'Comment' => array('__settings__' => array('fields' => array('comment')))
		);
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User(user,password)' => array('order' => 'user ASC'), 'Comment' => array('comment', 'limit' => 5)), true);
		$expected = array(
			'User' => array('__settings__' => array('order' => 'user ASC', 'fields' => array('user', 'password'))),
			'Comment' => array('__settings__' => array('fields' => array('comment'), 'limit' => 5))
		);
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User(user).Comment(comment).Article(id,title).Tag'), true);
		$expected = array('User' => array(
			'Comment' => array(
				'Article' => array(
					'Tag' => array(),
					'__settings__' => array('fields' => array('id', 'title'))
				),
				'__settings__' => array('fields' => array('comment', 'article_id', 'user_id'))
			),
			'__settings__' => array('fields' => array('user', 'id'))
		));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User.*', 'Comment'), true);
		$expected = array('User' => array('Article' => array(), 'ArticleFeatured' => array(), 'Comment' => array()), 'Comment' => array());
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => '*', 'Comment'), true);
		$expected = array('User' => array('Article' => array(), 'ArticleFeatured' => array(), 'Comment' => array()), 'Comment' => array());
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => array('*'), 'Comment'), true);
		$expected = array('User' => array('Article' => array(), 'ArticleFeatured' => array(), 'Comment' => array()), 'Comment' => array());
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => array('*', 'Article' => 'Tag'), 'Comment'), true);
		$expected = array('User' => array('Article' => array('Tag' => array()), 'ArticleFeatured' => array(), 'Comment' => array()), 'Comment' => array());
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => 'ArticleFeatured', 'Comment' => 'Article.*'), true);
		$expected = array('User' => array('ArticleFeatured' => array()), 'Comment' => array('Article' => array('User' => array(), 'Comment' => array(), 'Tag' => array())));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => 'ArticleFeatured', 'Comment' => array('Article' => '*')), true);
		$expected = array('User' => array('ArticleFeatured' => array()), 'Comment' => array('Article' => array('User' => array(), 'Comment' => array(), 'Tag' => array())));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => 'ArticleFeatured', 'Comment.Article.*'), true);
		$expected = array('User' => array('ArticleFeatured' => array()), 'Comment' => array('Article' => array('User' => array(), 'Comment' => array(), 'Tag' => array())));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => 'ArticleFeatured', 'Comment' => array('Article' => array('*', 'User' => 'Article'))), true);
		$expected = array('User' => array('ArticleFeatured' => array()), 'Comment' => array('Article' => array('User' => array('Article' => array()), 'Comment' => array(), 'Tag' => array())));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => 'ArticleFeatured', 'Comment' => array('Article' => array('*', 'title', 'User' => 'Article'))), true);
		$expected = array('User' => array('ArticleFeatured' => array()), 'Comment' => array(
			'Article' => array(
				'User' => array('Article' => array()),
				'Comment' => array(),
				'Tag' => array(),
				'__settings__' => array('fields' => array('title', 'user_id', 'id'))
			)
		));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => 'ArticleFeatured', 'Comment' => array('Article' => array('*', 'fields' => array('title'), 'User' => 'Article'))), true);
		$expected = array('User' => array('ArticleFeatured' => array()), 'Comment' => array(
			'Article' => array(
				'User' => array('Article' => array()),
				'Comment' => array(),
				'Tag' => array(),
				'__settings__' => array('fields' => array('title', 'user_id', 'id'))
			)
		));
		$this->assertEqual($result, $expected);

		$result = $Bindable->__models($this->Article, array('User' => 'ArticleFeatured', 'Comment' => array('Article(title)' => array('*', 'User' => 'Article'))), true);
		$expected = array('User' => array('ArticleFeatured' => array()), 'Comment' => array(
			'Article' => array(
				'User' => array('Article' => array()),
				'Comment' => array(),
				'Tag' => array(),
				'__settings__' => array('fields' => array('title', 'user_id', 'id'))
			)
		));
		$this->assertEqual($result, $expected);

		unset($Bindable);
		unset($this->Article);
	}

	/**
	 * Test calls to bindable with no parameters
	 *
	 * @access public
	 */
	function testNoBindings()
	{
		$this->Article =& new Article();

		$this->Article->restrict();
		$this->assertEqual($this->Article->recursive, -1);
		$this->__assertBindings($this->Article);

		$this->Article->resetBindable();
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));

		unset($this->Article);
	}

	/**
	 * Test calls to bindable specifying one or more first level bindings
	 *
	 * @access public
	 */
	function testFirstLevel()
	{
		$this->Article =& new Article();

		$this->Article->restrict('User');
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User')));

		$this->Article->resetBindable();
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));

		$this->Article->restrict('User', 'Comment');
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment')));

		$this->Article->resetBindable();
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));

		unset($this->Article);
	}

	/**
	 * Test calls to bindable specifying one or more second level bindings
	 *
	 * @access public
	 */
	function testSecondLevel()
	{
		$this->Article =& new Article();

		$this->Article->restrict(array('Comment' => 'User'));
		$this->assertEqual($this->Article->recursive, 2);
		$this->__assertBindings($this->Article, array('hasMany' => array('Comment')));
		$this->__assertBindings($this->Article->Comment, array('belongsTo' => array('User')));

		$this->Article->resetBindable();
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->Article->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		$this->Article->restrict(array('User' => 'ArticleFeatured'));
		$this->assertEqual($this->Article->recursive, 2);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User')));
		$this->__assertBindings($this->Article->User, array('hasMany' => array('ArticleFeatured')));

		$this->Article->resetBindable();
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->Article->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));

		$this->Article->restrict(array('User' => array('ArticleFeatured', 'Comment')));
		$this->assertEqual($this->Article->recursive, 2);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User')));
		$this->__assertBindings($this->Article->User, array('hasMany' => array('ArticleFeatured', 'Comment')));

		$this->Article->resetBindable();
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->Article->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));

		$this->Article->restrict(array('User' => array('ArticleFeatured')), 'Tag', array('Comment' => 'Attachment'));
		$this->assertEqual($this->Article->recursive, 2);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->Article->User, array('hasMany' => array('ArticleFeatured')));
		$this->__assertBindings($this->Article->Tag);
		$this->__assertBindings($this->Article->Comment, array('hasOne' => array('Attachment')));

		$this->Article->resetBindable();
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->Article->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->Article->Tag);
		$this->__assertBindings($this->Article->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		unset($this->Article);
	}

	/**
	 * Test calls to bindable specifying one or more second level bindings using
	 * dot notation
	 *
	 * @access public
	 */
	function testSecondLevelDotNotation()
	{
		$this->Article =& new Article();

		$this->Article->restrict('Comment.User');
		$this->assertEqual($this->Article->recursive, 2);
		$this->__assertBindings($this->Article, array('hasMany' => array('Comment')));
		$this->__assertBindings($this->Article->Comment, array('belongsTo' => array('User')));

		$this->Article->resetBindable();
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->Article->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		$this->Article->restrict('User.ArticleFeatured');
		$this->assertEqual($this->Article->recursive, 2);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User')));
		$this->__assertBindings($this->Article->User, array('hasMany' => array('ArticleFeatured')));

		$this->Article->resetBindable();
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->Article->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));

		$this->Article->restrict('User.ArticleFeatured', 'User.Comment');
		$this->assertEqual($this->Article->recursive, 2);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User')));
		$this->__assertBindings($this->Article->User, array('hasMany' => array('ArticleFeatured', 'Comment')));

		$this->Article->resetBindable();
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->Article->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));

		$this->Article->restrict('User.ArticleFeatured', 'Tag', 'Comment.Attachment');
		$this->assertEqual($this->Article->recursive, 2);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->Article->User, array('hasMany' => array('ArticleFeatured')));
		$this->__assertBindings($this->Article->Tag);
		$this->__assertBindings($this->Article->Comment, array('hasOne' => array('Attachment')));

		$this->Article->resetBindable();
		$this->assertEqual($this->Article->recursive, 1);
		$this->__assertBindings($this->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->Article->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->Article->Tag);
		$this->__assertBindings($this->Article->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		unset($this->Article);
	}

	/**
	 * Test calls to bindable specifying one or more second third level bindings
	 *
	 * @access public
	 */
	function testThirdLevel()
	{
		$this->User =& new User();

		$this->User->restrict(array('ArticleFeatured' => array('Featured' => 'Category')));
		$this->assertEqual($this->User->recursive, 3);
		$this->__assertBindings($this->User, array('hasMany' => array('ArticleFeatured')));
		$this->__assertBindings($this->User->ArticleFeatured, array('hasOne' => array('Featured')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('Category')));

		$this->User->resetBindable();
		$this->assertEqual($this->User->recursive, 1);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->User->ArticleFeatured, array('belongsTo' => array('User'), 'hasOne' => array('Featured'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('ArticleFeatured', 'Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		$this->User->restrict(array('ArticleFeatured' => array('Featured' => 'Category', 'Comment' => array('Article', 'Attachment'))));
		$this->assertEqual($this->User->recursive, 3);
		$this->__assertBindings($this->User, array('hasMany' => array('ArticleFeatured')));
		$this->__assertBindings($this->User->ArticleFeatured, array('hasOne' => array('Featured'), 'hasMany' => array('Comment')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article'), 'hasOne' => array('Attachment')));

		$this->User->resetBindable();
		$this->assertEqual($this->User->recursive, 1);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->User->ArticleFeatured, array('belongsTo' => array('User'), 'hasOne' => array('Featured'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('ArticleFeatured', 'Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		$this->User->restrict(array('ArticleFeatured' => array('Featured' => 'Category', 'Comment' => 'Attachment'), 'Article'));
		$this->assertEqual($this->User->recursive, 3);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured')));
		$this->__assertBindings($this->User->Article);
		$this->__assertBindings($this->User->ArticleFeatured, array('hasOne' => array('Featured'), 'hasMany' => array('Comment')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('hasOne' => array('Attachment')));

		$this->User->resetBindable();
		$this->assertEqual($this->User->recursive, 1);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->User->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured, array('belongsTo' => array('User'), 'hasOne' => array('Featured'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('ArticleFeatured', 'Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		unset($this->User);
	}

	/**
	 * Test calls to bindable specifying one or more second third level bindings using
	 * dot notation
	 *
	 * @access public
	 */
	function testThirdLevelDotNotation()
	{
		$this->User =& new User();

		$this->User->restrict('ArticleFeatured.Featured.Category');
		$this->assertEqual($this->User->recursive, 3);
		$this->__assertBindings($this->User, array('hasMany' => array('ArticleFeatured')));
		$this->__assertBindings($this->User->ArticleFeatured, array('hasOne' => array('Featured')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('Category')));

		$this->User->resetBindable();
		$this->assertEqual($this->User->recursive, 1);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->User->ArticleFeatured, array('belongsTo' => array('User'), 'hasOne' => array('Featured'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('ArticleFeatured', 'Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		$this->User->restrict('ArticleFeatured.Featured.Category', 'ArticleFeatured.Comment.Article', 'ArticleFeatured.Comment.Attachment');
		$this->assertEqual($this->User->recursive, 3);
		$this->__assertBindings($this->User, array('hasMany' => array('ArticleFeatured')));
		$this->__assertBindings($this->User->ArticleFeatured, array('hasOne' => array('Featured'), 'hasMany' => array('Comment')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article'), 'hasOne' => array('Attachment')));

		$this->User->resetBindable();
		$this->assertEqual($this->User->recursive, 1);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->User->ArticleFeatured, array('belongsTo' => array('User'), 'hasOne' => array('Featured'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('ArticleFeatured', 'Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		$this->User->restrict('ArticleFeatured.Featured.Category', 'ArticleFeatured.Comment.Attachment', 'Article');
		$this->assertEqual($this->User->recursive, 3);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured')));
		$this->__assertBindings($this->User->Article);
		$this->__assertBindings($this->User->ArticleFeatured, array('hasOne' => array('Featured'), 'hasMany' => array('Comment')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('hasOne' => array('Attachment')));

		$this->User->resetBindable();
		$this->assertEqual($this->User->recursive, 1);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->User->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured, array('belongsTo' => array('User'), 'hasOne' => array('Featured'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('ArticleFeatured', 'Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		unset($this->User);
	}

	/**
	 * Test calls to bindable with no parameters and then finding
	 *
	 * @access public
	 */
	function testFindNoBindings()
	{
		$this->Article =& new Article();

		$this->Article->restrict();
		$result = $this->Article->find('all', array('recursive' => 1));
		$expected = array(
			array('Article' => array(
				'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
				'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
			)),
			array('Article' => array(
				'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
				'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
			)),
			array('Article' => array(
				'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
				'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
			))
		);
		$this->assertEqual($result, $expected);

		unset($this->Article);
	}

	/**
	 * Test calls to bindable with no parameters, using the find-embedded call
	 *
	 * @access public
	 */
	function testFindEmbeddedNoBindings()
	{
		$this->Article =& new Article();

		$result = $this->Article->find('all', array('restrict' => array()));
		$expected = array(
			array('Article' => array(
				'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
				'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
			)),
			array('Article' => array(
				'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
				'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
			)),
			array('Article' => array(
				'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
				'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
			))
		);
		$this->assertEqual($result, $expected);

		unset($this->Article);
	}

	/**
	 * Test calls to bindable specifying one or more first level bindings and then find
	 *
	 * @access public
	 */
	function testFindFirstLevel()
	{
		$this->Article =& new Article();

		$this->Article->restrict('User');
		$result = $this->Article->find('all', array('recursive' => 1));
		$expected = array(
			array(
				'Article' => array(
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				)
			),
			array(
				'Article' => array(
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				),
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				)
			),
			array(
				'Article' => array(
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				)
			)
		);
		$this->assertEqual($result, $expected);

		$this->Article->restrict('User', 'Comment');
		$result = $this->Article->find('all', array('recursive' => 1));
		$expected = array(
			array(
				'Article' => array(
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'Comment' => array(
					array(
						'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31'
					),
					array(
						'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31'
					),
					array(
						'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31'
					),
					array(
						'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
						'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
					)
				)
			),
			array(
				'Article' => array(
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				),
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'Comment' => array(
					array(
						'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31'
					),
					array(
						'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31'
					)
				)
			),
			array(
				'Article' => array(
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'Comment' => array()
			)
		);
		$this->assertEqual($result, $expected);

		unset($this->Article);
	}

	/**
	 * Test calls to bindable specifying one or more first level bindings, using
	 * the find-embedded call
	 *
	 * @access public
	 */
	function testFindEmbeddedFirstLevel()
	{
		$this->Article =& new Article();

		$result = $this->Article->find('all', array('restrict' => array('User')));
		$expected = array(
			array(
				'Article' => array(
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				)
			),
			array(
				'Article' => array(
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				),
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				)
			),
			array(
				'Article' => array(
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				)
			)
		);
		$this->assertEqual($result, $expected);

		$result = $this->Article->find('all', array('restrict' => array('User', 'Comment')));
		$expected = array(
			array(
				'Article' => array(
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'Comment' => array(
					array(
						'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31'
					),
					array(
						'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31'
					),
					array(
						'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31'
					),
					array(
						'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
						'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
					)
				)
			),
			array(
				'Article' => array(
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				),
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'Comment' => array(
					array(
						'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31'
					),
					array(
						'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31'
					)
				)
			),
			array(
				'Article' => array(
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'Comment' => array()
			)
		);
		$this->assertEqual($result, $expected);

		unset($this->Article);
	}

	/**
	 * Test calls to bindable specifying one or more second level bindings
	 *
	 * @access public
	 */
	function testFindSecondLevel()
	{
		$this->Article =& new Article();

		$this->Article->restrict(array('Comment' => 'User'));
		$result = $this->Article->find('all', array('recursive' => 2));
		$expected = array(
			array(
				'Article' => array(
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				),
				'Comment' => array(
					array(
						'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
						'User' => array(
							'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
						)
					),
					array(
						'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
						'User' => array(
							'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
						)
					),
					array(
						'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
						'User' => array(
							'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
						)
					),
					array(
						'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
						'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
						'User' => array(
							'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
						)
					)
				)
			),
			array(
				'Article' => array(
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				),
				'Comment' => array(
					array(
						'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
						'User' => array(
							'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
						)
					),
					array(
						'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
						'User' => array(
							'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
						)
					)
				)
			),
			array(
				'Article' => array(
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				),
				'Comment' => array()
			)
		);
		$this->assertEqual($result, $expected);

		$this->Article->restrict(array('User' => 'ArticleFeatured'));
		$result = $this->Article->find('all', array('recursive' => 2));
		$expected = array(
			array(
				'Article' => array(
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => array(
						array(
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						),
						array(
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						)
					)
				)
			),
			array(
				'Article' => array(
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				),
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31',
					'ArticleFeatured' => array(
						array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						)
					)
				)
			),
			array(
				'Article' => array(
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => array(
						array(
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						),
						array(
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						)
					)
				)
			)
		);
		$this->assertEqual($result, $expected);

		$this->Article->restrict(array('User' => array('ArticleFeatured', 'Comment')));
		$result = $this->Article->find('all', array('recursive' => 2));
		$expected = array(
			array(
				'Article' => array(
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => array(
						array(
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						),
						array(
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						)
					),
					'Comment' => array(
						array(
							'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
							'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31'
						),
						array(
							'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
							'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
						),
						array(
							'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
							'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31'
						)
					)
				)
			),
			array(
				'Article' => array(
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				),
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31',
					'ArticleFeatured' => array(
						array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						)
					),
					'Comment' => array()
				)
			),
			array(
				'Article' => array(
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => array(
						array(
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						),
						array(
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						)
					),
					'Comment' => array(
						array(
							'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
							'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31'
						),
						array(
							'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
							'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
						),
						array(
							'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
							'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31'
						)
					)
				)
			)
		);
		$this->assertEqual($result, $expected);

		$this->Article->restrict(array('User' => array('ArticleFeatured')), 'Tag', array('Comment' => 'Attachment'));
		$result = $this->Article->find('all', array('recursive' => 2));
		$expected = array(
			array(
				'Article' => array(
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => array(
						array(
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						),
						array(
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						)
					)
				),
				'Comment' => array(
					array(
						'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
						'Attachment' => array()
					),
					array(
						'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
						'Attachment' => array()
					),
					array(
						'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
						'Attachment' => array()
					),
					array(
						'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
						'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
						'Attachment' => array()
					)
				),
				'Tag' => array(
					array('id' => 1, 'tag' => 'tag1', 'created' => '2007-03-18 12:22:23', 'updated' => '2007-03-18 12:24:31'),
					array('id' => 2, 'tag' => 'tag2', 'created' => '2007-03-18 12:24:23', 'updated' => '2007-03-18 12:26:31')
				)
			),
			array(
				'Article' => array(
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				),
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31',
					'ArticleFeatured' => array(
						array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						)
					)
				),
				'Comment' => array(
					array(
						'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
						'Attachment' => array(
							'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
							'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
						)
					),
					array(
						'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
						'Attachment' => array()
					)
				),
				'Tag' => array(
					array('id' => 1, 'tag' => 'tag1', 'created' => '2007-03-18 12:22:23', 'updated' => '2007-03-18 12:24:31'),
					array('id' => 3, 'tag' => 'tag3', 'created' => '2007-03-18 12:26:23', 'updated' => '2007-03-18 12:28:31')
				)
			),
			array(
				'Article' => array(
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => array(
						array(
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						),
						array(
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						)
					)
				),
				'Comment' => array(),
				'Tag' => array()
			)
		);
		$this->assertEqual($result, $expected);

		unset($this->Article);
	}

	/**
	 * Test calls to bindable specifying one or more second level bindings, using
	 * the find-embedded call
	 *
	 * @access public
	 */
	function testFindEmbeddedSecondLevel()
	{
		$this->Article =& new Article();

		$result = $this->Article->find('all', array('restrict' => array('Comment' => 'User')));
		$expected = array(
			array(
				'Article' => array(
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				),
				'Comment' => array(
					array(
						'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
						'User' => array(
							'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
						)
					),
					array(
						'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
						'User' => array(
							'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
						)
					),
					array(
						'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
						'User' => array(
							'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
						)
					),
					array(
						'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
						'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
						'User' => array(
							'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
						)
					)
				)
			),
			array(
				'Article' => array(
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				),
				'Comment' => array(
					array(
						'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
						'User' => array(
							'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
						)
					),
					array(
						'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
						'User' => array(
							'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
						)
					)
				)
			),
			array(
				'Article' => array(
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				),
				'Comment' => array()
			)
		);
		$this->assertEqual($result, $expected);

		$result = $this->Article->find('all', array('restrict' => array('User' => 'ArticleFeatured')));
		$expected = array(
			array(
				'Article' => array(
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => array(
						array(
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						),
						array(
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						)
					)
				)
			),
			array(
				'Article' => array(
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				),
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31',
					'ArticleFeatured' => array(
						array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						)
					)
				)
			),
			array(
				'Article' => array(
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => array(
						array(
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						),
						array(
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						)
					)
				)
			)
		);
		$this->assertEqual($result, $expected);

		$result = $this->Article->find('all', array('restrict' => array('User' => array('ArticleFeatured', 'Comment'))));
		$expected = array(
			array(
				'Article' => array(
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => array(
						array(
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						),
						array(
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						)
					),
					'Comment' => array(
						array(
							'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
							'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31'
						),
						array(
							'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
							'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
						),
						array(
							'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
							'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31'
						)
					)
				)
			),
			array(
				'Article' => array(
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				),
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31',
					'ArticleFeatured' => array(
						array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						)
					),
					'Comment' => array()
				)
			),
			array(
				'Article' => array(
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => array(
						array(
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						),
						array(
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						)
					),
					'Comment' => array(
						array(
							'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
							'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31'
						),
						array(
							'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
							'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
						),
						array(
							'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
							'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31'
						)
					)
				)
			)
		);
		$this->assertEqual($result, $expected);

		$result = $this->Article->find('all', array('restrict' => array('User' => 'ArticleFeatured', 'Tag', 'Comment' => 'Attachment')));
		$expected = array(
			array(
				'Article' => array(
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => array(
						array(
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						),
						array(
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						)
					)
				),
				'Comment' => array(
					array(
						'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
						'Attachment' => array()
					),
					array(
						'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
						'Attachment' => array()
					),
					array(
						'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
						'Attachment' => array()
					),
					array(
						'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
						'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
						'Attachment' => array()
					)
				),
				'Tag' => array(
					array('id' => 1, 'tag' => 'tag1', 'created' => '2007-03-18 12:22:23', 'updated' => '2007-03-18 12:24:31'),
					array('id' => 2, 'tag' => 'tag2', 'created' => '2007-03-18 12:24:23', 'updated' => '2007-03-18 12:26:31')
				)
			),
			array(
				'Article' => array(
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				),
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31',
					'ArticleFeatured' => array(
						array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						)
					)
				),
				'Comment' => array(
					array(
						'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
						'Attachment' => array(
							'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
							'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
						)
					),
					array(
						'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
						'Attachment' => array()
					)
				),
				'Tag' => array(
					array('id' => 1, 'tag' => 'tag1', 'created' => '2007-03-18 12:22:23', 'updated' => '2007-03-18 12:24:31'),
					array('id' => 3, 'tag' => 'tag3', 'created' => '2007-03-18 12:26:23', 'updated' => '2007-03-18 12:28:31')
				)
			),
			array(
				'Article' => array(
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				),
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => array(
						array(
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						),
						array(
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						)
					)
				),
				'Comment' => array(),
				'Tag' => array()
			)
		);
		$this->assertEqual($result, $expected);

		unset($this->Article);
	}

	/**
	 * Test calls to bindable specifying one or more third level bindings
	 *
	 * @access public
	 */
	function testFindThirdLevel()
	{
		$this->User =& new User();

		$this->User->restrict(array('ArticleFeatured' => array('Featured' => 'Category')));
		$result = $this->User->find('all', array('recursive' => 3));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);

		$this->User->restrict(array('ArticleFeatured' => array('Featured' => 'Category', 'Comment' => array('Article', 'Attachment'))));
		$result = $this->User->find('all', array('recursive' => 3));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							),
							array(
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							),
							array(
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							),
							array(
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array(),
						'Comment' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Article' => array(
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								),
								'Attachment' => array(
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								)
							),
							array(
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Article' => array(
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								),
								'Attachment' => array()
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);

		$this->User->restrict(array('ArticleFeatured' => array('Featured' => 'Category', 'Comment' => 'Attachment'), 'Article'));
		$result = $this->User->find('all', array('recursive' => 3));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'Article' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
					)
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Attachment' => array()
							),
							array(
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Attachment' => array()
							),
							array(
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Attachment' => array()
							),
							array(
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Attachment' => array()
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array(),
						'Comment' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'Article' => array(),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'Article' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
					)
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Attachment' => array(
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								)
							),
							array(
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Attachment' => array()
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'Article' => array(),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);

		unset($this->User);
	}

	/**
	 * Test calls to bindable specifying one or more second third bindings, using
	 * the find-embedded call
	 *
	 * @access public
	 */
	function testFindEmbeddedThirdLevel()
	{
		$this->User =& new User();

		$result = $this->User->find('all', array('restrict' => array('ArticleFeatured' => array('Featured' => 'Category'))));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);

		$result = $this->User->find('all', array('restrict' => array('ArticleFeatured' => array('Featured' => 'Category', 'Comment' => array('Article', 'Attachment')))));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							),
							array(
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							),
							array(
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							),
							array(
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array(),
						'Comment' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Article' => array(
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								),
								'Attachment' => array(
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								)
							),
							array(
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Article' => array(
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								),
								'Attachment' => array()
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);

		$result = $this->User->find('all', array('restrict' => array('ArticleFeatured' => array('Featured' => 'Category', 'Comment' => 'Attachment'), 'Article')));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'Article' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
					)
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Attachment' => array()
							),
							array(
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Attachment' => array()
							),
							array(
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Attachment' => array()
							),
							array(
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Attachment' => array()
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array(),
						'Comment' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'Article' => array(),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'Article' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
					)
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Attachment' => array(
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								)
							),
							array(
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Attachment' => array()
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'Article' => array(),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);

		unset($this->User);
	}

	/**
	 * Test calls to bindable specifying one or more second third bindings, using
	 * the find-embedded call and specifying settings
	 *
	 * @access public
	 */
	function testSettingsThirdLevel()
	{
		$this->User =& new User();

		$result = $this->User->find('all', array('restrict' => array('ArticleFeatured' => array('Featured' => array('Category' => array('id', 'name'))))));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'name' => 'Category 1'
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'name' => 'Category 1'
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);

		$result = $this->User->find('all', array('restrict' => array(
			'ArticleFeatured' => array(
				'title',
				'Featured' => array(
					'category_id',
					'Category' => 'name'
				)
			)
		)));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'title' => 'First Article', 'user_id' => 1,
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1,
							'Category' => array(
								'id' => 1, 'name' => 'Category 1'
							)
						)
					),
					array(
						'id' => 3, 'title' => 'Third Article', 'user_id' => 1,
						'Featured' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'title' => 'Second Article', 'user_id' => 3,
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1,
							'Category' => array(
								'id' => 1, 'name' => 'Category 1'
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);

		$result = $this->User->find('all', array('restrict' => array(
			'ArticleFeatured' => array(
				'title', 'order' => 'title DESC',
				'Featured' => array(
					'category_id',
					'Category' => 'name'
				)
			)
		)));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 3, 'title' => 'Third Article', 'user_id' => 1,
						'Featured' => array()
					),
					array(
						'id' => 1, 'title' => 'First Article', 'user_id' => 1,
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1,
							'Category' => array(
								'id' => 1, 'name' => 'Category 1'
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'title' => 'Second Article', 'user_id' => 3,
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1,
							'Category' => array(
								'id' => 1, 'name' => 'Category 1'
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);

		unset($this->User);
	}
	
	/**
	 * Test calls to bindable specifying one or more second third bindings, using
	 * the find-embedded call, and setting it to not reset associations
	 *
	 * @access public
	 */
	function testFindThirdLevelNonReset()
	{
		$this->User =& new User();

		$this->User->restrict(false, array('ArticleFeatured' => array('Featured' => 'Category')));
		$result = $this->User->find('all', array('recursive' => 3));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);
		
		$this->__assertBindings($this->User, array('hasMany' => array('ArticleFeatured')));
		$this->__assertBindings($this->User->ArticleFeatured, array('hasOne' => array('Featured')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('Category')));
		
		$this->User->resetBindable(true);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->User->ArticleFeatured, array('belongsTo' => array('User'), 'hasOne' => array('Featured'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('ArticleFeatured', 'Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		$this->User->restrict(false, array('ArticleFeatured' => array('Featured' => 'Category', 'Comment' => array('Article', 'Attachment'))));
		$result = $this->User->find('all', array('recursive' => 3));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							),
							array(
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							),
							array(
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							),
							array(
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array(),
						'Comment' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Article' => array(
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								),
								'Attachment' => array(
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								)
							),
							array(
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Article' => array(
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								),
								'Attachment' => array()
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);

		$this->__assertBindings($this->User, array('hasMany' => array('ArticleFeatured')));
		$this->__assertBindings($this->User->ArticleFeatured, array('hasOne' => array('Featured'), 'hasMany' => array('Comment')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article'), 'hasOne' => array('Attachment')));

		$this->User->resetBindable(true);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->User->ArticleFeatured, array('belongsTo' => array('User'), 'hasOne' => array('Featured'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('ArticleFeatured', 'Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		$this->User->restrict(false, array('ArticleFeatured' => array('Featured' => 'Category', 'Comment' => 'Attachment'), 'Article'));
		$result = $this->User->find('all', array('recursive' => 3));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'Article' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
					)
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Attachment' => array()
							),
							array(
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Attachment' => array()
							),
							array(
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Attachment' => array()
							),
							array(
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Attachment' => array()
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array(),
						'Comment' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'Article' => array(),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'Article' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
					)
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Attachment' => array(
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								)
							),
							array(
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Attachment' => array()
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'Article' => array(),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);
		
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured')));
		$this->__assertBindings($this->User->Article);
		$this->__assertBindings($this->User->ArticleFeatured, array('hasOne' => array('Featured'), 'hasMany' => array('Comment')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('hasOne' => array('Attachment')));
		
		$this->User->resetBindable(true);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->User->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured, array('belongsTo' => array('User'), 'hasOne' => array('Featured'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('ArticleFeatured', 'Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		unset($this->User);
	}
	
	/**
	 * Test calls to bindable specifying one or more second third bindings, using
	 * the find-embedded call, and setting it to not reset associations
	 *
	 * @access public
	 */
	function testFindEmbeddedThirdLevelNonReset()
	{
		$this->User =& new User();

		$result = $this->User->find('all', array('reset' => false, 'restrict' => array('ArticleFeatured' => array('Featured' => 'Category'))));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);
		
		$this->__assertBindings($this->User, array('hasMany' => array('ArticleFeatured')));
		$this->__assertBindings($this->User->ArticleFeatured, array('hasOne' => array('Featured')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('Category')));
		
		$this->User->resetBindable(true);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->User->ArticleFeatured, array('belongsTo' => array('User'), 'hasOne' => array('Featured'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('ArticleFeatured', 'Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		$result = $this->User->find('all', array('reset' => false, 'restrict' => array('ArticleFeatured' => array('Featured' => 'Category', 'Comment' => array('Article', 'Attachment')))));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							),
							array(
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							),
							array(
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							),
							array(
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Article' => array(
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								),
								'Attachment' => array()
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array(),
						'Comment' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Article' => array(
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								),
								'Attachment' => array(
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								)
							),
							array(
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Article' => array(
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								),
								'Attachment' => array()
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);
		
		$this->__assertBindings($this->User, array('hasMany' => array('ArticleFeatured')));
		$this->__assertBindings($this->User->ArticleFeatured, array('hasOne' => array('Featured'), 'hasMany' => array('Comment')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article'), 'hasOne' => array('Attachment')));

		$this->User->resetBindable(true);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->User->ArticleFeatured, array('belongsTo' => array('User'), 'hasOne' => array('Featured'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('ArticleFeatured', 'Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		$result = $this->User->find('all', array('reset' => false, 'restrict' => array('ArticleFeatured' => array('Featured' => 'Category', 'Comment' => 'Attachment'), 'Article')));
		$expected = array(
			array(
				'User' => array(
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				),
				'Article' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
					)
				),
				'ArticleFeatured' => array(
					array(
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => array(
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Attachment' => array()
							),
							array(
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Attachment' => array()
							),
							array(
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Attachment' => array()
							),
							array(
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Attachment' => array()
							)
						)
					),
					array(
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => array(),
						'Comment' => array()
					)
				)
			),
			array(
				'User' => array(
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				),
				'Article' => array(),
				'ArticleFeatured' => array()
			),
			array(
				'User' => array(
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				),
				'Article' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
					)
				),
				'ArticleFeatured' => array(
					array(
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => array(
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => array(
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							)
						),
						'Comment' => array(
							array(
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Attachment' => array(
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								)
							),
							array(
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Attachment' => array()
							)
						)
					)
				)
			),
			array(
				'User' => array(
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				),
				'Article' => array(),
				'ArticleFeatured' => array()
			)
		);
		$this->assertEqual($result, $expected);
		
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured')));
		$this->__assertBindings($this->User->Article);
		$this->__assertBindings($this->User->ArticleFeatured, array('hasOne' => array('Featured'), 'hasMany' => array('Comment')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('hasOne' => array('Attachment')));
		
		$this->User->resetBindable(true);
		$this->__assertBindings($this->User, array('hasMany' => array('Article', 'ArticleFeatured', 'Comment')));
		$this->__assertBindings($this->User->Article, array('belongsTo' => array('User'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured, array('belongsTo' => array('User'), 'hasOne' => array('Featured'), 'hasMany' => array('Comment'), 'hasAndBelongsToMany' => array('Tag')));
		$this->__assertBindings($this->User->ArticleFeatured->Featured, array('belongsTo' => array('ArticleFeatured', 'Category')));
		$this->__assertBindings($this->User->ArticleFeatured->Comment, array('belongsTo' => array('Article', 'User'), 'hasOne' => array('Attachment')));

		unset($this->User);
	}

	/**
	 * Make sure that the bindings for $Model are what is expected.
	 *
	 * @param object $Model A model instance
	 * @param array $expected Associative array in the form of: binding => keys
	 * @access private
	 */
	function __assertBindings(&$Model, $expected = array())
	{
		$expected = am(array('belongsTo' => array(), 'hasOne' => array(), 'hasMany' => array(), 'hasAndBelongsToMany' => array()), $expected);

		foreach($expected as $binding => $expect)
		{
			$this->assertEqual(array_keys($Model->$binding), $expect);
		}
	}

	/**
	 * Show first level bindings for specified model
	 *
	 * @param object $Model Model to debug
	 * @param boolean $output Set to true to output, false to just return
	 * @return string Debug string
	 * @access private
	 */
	function __bindings(&$Model, $output = true)
	{
		$relationTypes = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');

		$debug = '';

		foreach($relationTypes as $binding)
		{
			if (!empty($Model->$binding))
			{
				$models = array_keys($Model->$binding);
				$debug .= ife(!empty($debug), ' - ', '') . $binding . ': [' . implode(', ', $models) . ']';
			}
		}

		$debug = '<strong>' . $Model->alias . '</strong>: ' . $debug . '<br />';

		if ($output)
		{
			echo $debug;
		}

		return $debug;
	}
}

?>