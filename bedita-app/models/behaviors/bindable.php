<?php
/* SVN FILE: $Id: bindable.php 19 2007-11-23 18:42:23Z mgiglesias $ */

/**
 * Bindable Behavior class file.
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
 * @package app
 * @subpackage app.models.behaviors
 */

/**
 * Model behavior to support unbinding of models.
 *
 * @package app
 * @subpackage app.models.behaviors
 */
class BindableBehavior extends ModelBehavior
{
	/**
	 * Contain settings indexed by model name.
	 *
	 * @var array
	 * @access private
	 */
	var $__settings = array();

	/**
	 * Types of relationships available for models
	 *
	 * @var array
	 * @access private
	 */
	var $__bindings = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');

	/**
	 * Initiate behavior for the model using specified settings. Available settings:
	 *
	 * - recursive: (boolean, optional) set to true to allow bindable to automatically
	 * 				determine the recursiveness level needed to fetch specified models,
	 * 				and set the model recursiveness to this level. setting it to false
	 * 				disables this feature. DEFAULTS TO: true
	 *
	 * - notices:	(boolean, optional) issues E_NOTICES for bindings referenced in a
	 * 				bindable call that are not valid. DEFAULTS TO: false
	 *
	 * @param object $Model Model using the behaviour
	 * @param array $settings Settings to override for model.
	 * @access public
	 */
	function setup(&$Model, $settings = array())
	{
		$default = array('recursive' => true, 'notices' => false);

		if (!isset($this->__settings[$Model->alias]))
		{
			$this->__settings[$Model->alias] = $default;
		}

		$this->__settings[$Model->alias] = am($this->__settings[$Model->alias], ife(is_array($settings), $settings, array()));
	}

	/**
	 * Unbinds all relations from a model except the specified ones. Calling this function without
	 * parameters unbinds all related models.
	 *
	 * @return mixed If direct call, integer with recommended value for recursive
	 * @access public
	 */
	function restrict()
	{
		$innerCall = false;
		$reset = true;
		$recursive = null;
		$arguments = func_get_args();
		$totalArguments = count($arguments);
		$Model =& $arguments[0];

		// Get the model, and find out if we're being called directly

		$shift = 1;
		
		//if (!$innerCall) { debug($arguments[1]); debug($arguments[2]); }

		if ($totalArguments > 1 && is_bool($arguments[1]))
		{
			$reset = $arguments[1];
			$shift++;
			
			if ($totalArguments > 2 && is_bool($arguments[2]))
			{
				$innerCall = $arguments[2];
				$shift++;
			}
		}
		
		// Special ways to specify

		$arguments = array_slice($arguments, $shift);

		// Merge all parameters into an array

		foreach($arguments as $index => $argument)
		{
			if (is_array($argument))
			{
				if (!empty($argument))
				{
					$arguments = am($arguments, $argument);
				}

				unset($arguments[$index]);
			}
		}

		// Process arguments into a set of models to include

		$models = array();

		if (!$innerCall)
		{
			$models = $this->__models($Model, $arguments, $this->__settings[$Model->alias]['notices']);
			$recursive = -1;

			if (!empty($models))
			{
				$recursive = Set::countDim($models, true);
			}
		}
		else if (!empty($arguments))
		{
			$models = $arguments;
		}

		// Go through all models and run bindable on inner models

		foreach($models as $name => $children)
		{
			if (isset($Model->$name))
			{
				// Process binding settings

				if (isset($children['__settings__']))
				{
					foreach($this->__bindings as $relation)
					{
						if (isset($Model->{$relation}[$name]))
						{
							if (isset($children['__settings__']['fields']) && !in_array($Model->$name->primaryKey, $children['__settings__']['fields']))
							{
								$children['__settings__']['fields'][] = $Model->$name->primaryKey;
							}
							
							if (!$reset)
							{
								$this->__backupAssociations($Model);
							}

							$Model->bindModel(array($relation => array(
								$name => am($Model->{$relation}[$name], $children['__settings__'])
							)), $reset);
						}
					}

					unset($children['__settings__']);
				}

				// Run bindable on inner model

				if (!isset($Model->__backInnerAssociation))
				{
					$Model->__backInnerAssociation = array();
				}

				$Model->__backInnerAssociation[] = $name;

				$this->restrict($Model->$name, $reset, true, $children);
			}
		}

		// Unbind unneeded models

		$unbind = array();
		$models = array_keys($models);
		$bindings = $Model->getAssociated();
		
		foreach($bindings as $bindingName => $relation)
		{
			if (!in_array($bindingName, $models))
			{
				$unbind[$relation][] = $bindingName;
			}
		}

		if (!empty($unbind))
		{
			if (!$reset)
			{
				$this->__backupAssociations($Model);
			}
			
			$Model->unbindModel($unbind, $reset);
		}

		// Keep a reference that this model is the originator of a chain-bindable call

		if (!$innerCall && $reset)
		{
			$this->__runResetBindable[$Model->alias] = true;
		}

		// If specified, set this model's recursiveness level

		if (!$innerCall && $this->__settings[$Model->alias]['recursive'] === true && $recursive !== null)
		{
			$Model->__backRecursive = $Model->recursive;
			$Model->recursive = $recursive;
		}

		return $recursive;
	}

	/**
	 * Resets all relations and inner model relations after calling restrict()
	 *
	 * @param object $Model	Model using the behaviour
	 * @param boolean $resetOriginal Force resetting original associations that may have been set to not reset
	 * @access public
	 */
	function resetBindable(&$Model, $resetOriginal = false)
	{
		$innerAssociations = array();

		if (isset($Model->__backInnerAssociation))
		{
			$innerAssociations = $Model->__backInnerAssociation;
			unset($Model->__backInnerAssociation);
		}
		
		if ($resetOriginal && isset($Model->__backOriginalAssociation))
		{
			$Model->__backAssociation = $Model->__backOriginalAssociation;
			unset($Model->__backOriginalAssociation);
		}

		// Reset associations

		if (isset($Model->__backAssociation))
		{
			$Model->__resetAssociations();
		}

		// Reset recursiveness

		if (isset($Model->__backRecursive))
		{
			$Model->recursive = $Model->__backRecursive;
			unset($Model->__backRecursive);
		}

		// Reset bindable on linked models (if needed)

		foreach($innerAssociations as $currentModel)
		{
			$this->resetBindable($Model->$currentModel, $resetOriginal);
		}
	}

	/**
	 * Runs before a find() operation. Used to allow 'restrict' setting
	 * as part of the find call, like this:
	 *
	 * Model->find('all', array('restrict' => array('Model1', 'Model2')));
	 *
	 * Model->find('all', array('restrict' => array(
	 * 	'Model1' => array('Model11', 'Model12'),
	 * 	'Model2',
	 * 	'Model3' => array(
	 * 		'Model31' => 'Model311',
	 * 		'Model32',
	 * 		'Model33' => array('Model331', 'Model332')
	 * )));
	 *
	 * @param object $Model	Model using the behaviour
	 * @param array $query Query parameters as set by cake
	 * @access public
	 */
	function beforeFind(&$Model, $query)
	{
		if (isset($query['restrict']))
		{
			$query = am(array('reset' => true), $query);
			
			$this->restrict($Model, $query['reset'], false, $query['restrict']);
		}
	}

	/**
	 * Runs after a find() operation.
	 *
	 * @param object $Model	Model using the behaviour
	 * @param array $results Results of the find operation.
	 * @access public
	 */
	function afterFind(&$Model, $results)
	{
		if (isset($this->__runResetBindable[$Model->alias]) && $this->__runResetBindable[$Model->alias])
		{
			$this->resetBindable($Model);
			unset($this->__runResetBindable[$Model->alias]);
		}
	}
	
	/**
	 * Backup associations for a model right before a non-resettable binding
	 * operation.
	 *
	 * @param object $Model Model being processed
	 * @access private
	 */
	function __backupAssociations(&$Model)
	{
		$Model->__backOriginalAssociation = array();
		
		foreach($this->__bindings as $relation)
		{
			$Model->__backOriginalAssociation[$relation] = $Model->{$relation};
		}
	}

	/**
	 * Get a list of models in the form: Model1 => array(Model2, ...), converting
	 * dot-notation arguments (i.e: Model1.Model2.Model3) to their depth-notation
	 * equivalent (i.e: Model1 => Model2 => Model3). The convertion is used for
	 * backwards compatibility with previous versions of bindable (expects).
	 *
	 * @param object $Model Model being processed
	 * @param array $arguments Set of arguments to convert
	 * @param boolean $notices Set to true to throw a notice when a binding does not exist
	 * @param boolean $inner Set to true to indicate inner call, false otherwise
	 * @return array Converted arguments
	 * @access private
	 */
	function __models(&$Model, $arguments, $notices = false, $inner = false)
	{
		$models = array();
		$bindings = $Model->getAssociated();
		$settings = array('conditions', 'fields', 'limit', 'offset', 'order');

		foreach($arguments as $key => $children)
		{
			$name = null;
			$setting = null;
			$settingValue = array();

			if (is_numeric($key) && !is_array($children))
			{
				$name = $children;
				$children = array();
			}
			else if (!is_numeric($key))
			{
				$name = $key;
			}

			if (!empty($name) && is_string($name) && !in_array($name, $settings) && $Model->hasField($name) && (!isset($Model->$name) || !is_object($Model->$name)) && (!isset($children['fields']) || !in_array($name, $children['fields'])))
			{
				$setting = 'fields';
				$settingValue = array($name);
			}
			else if (!empty($name) && in_array($name, $settings))
			{
				$setting = $name;
				$settingValue = $children;
				$children = array();
			}

			if (!empty($setting))
			{
				if ($setting == 'fields')
				{
					if (!is_array($children))
					{
						$children = array($setting => array());
					}
					else if (!isset($children[$setting]))
					{
						$children[$setting] = array();
					}

					$settingValue = am($children[$setting], ife(!is_array($settingValue), array($settingValue), $settingValue));
				}

				$models = Set::merge($models, array('__settings__' => array($setting => $settingValue)));
			}
			else if (!empty($name))
			{
				if (!is_array($children) && $children != $key)
				{
					$children = array($children => array());
				}

				// Handle dot notation and in place list of fields

				if (strpos($name, '.') !== false)
				{
					$chain = explode('.', $name);
					$name = array_shift($chain);
					$children = array(join('.', $chain) => $children);

					if (isset($models[$name]))
					{
						$children = am($children, $models[$name]);
					}
				}

				$fields = null;

				if (preg_match('/^(\w+)\(([^\)]+)\)$/i', $name, $matches))
				{
					$name = $matches[1];
					$fields = preg_split('/,\s*/', $matches[2]);
				}

				if ($name != '*' && !isset($models[$name]))
				{
					$models[$name] = array();
				}

				// Do a processing of children and assign

				if ($name == '*')
				{
					$children = array_flip(array_keys($bindings));
					array_walk($children, create_function('&$item', '$item = array();'));
					$models = Set::merge($models, $children);
				}
				else if (isset($Model->$name) && is_object($Model->$name))
				{
					// Add fields, if any

					if (!empty($fields))
					{
						if (!isset($children['fields']))
						{
							$children['fields'] = array();
						}

						$children['fields'] = am($children['fields'], $fields);
					}

					$models[$name] = am($models[$name], $this->__models($Model->$name, $children, $notices, true));
				}
				else if ($notices)
				{
					trigger_error(sprintf(__('%s.%s is not a valid binding', true), $Model->alias, $name), E_USER_NOTICE);
				}
			}
		}

		// Process mandatory fields based on included child relations

		$mandatoryFields = array();

		if (isset($models['__settings__']) && isset($models['__settings__']['fields']))
		{
			foreach($models as $name => $children)
			{
				if ($name != '__settings__' && isset($bindings[$name]))
				{
					$relation = $bindings[$name];
					switch($relation)
					{
							case 'belongsTo':
								$mandatoryInnerFields = array($Model->$name->primaryKey);
								$mandatoryFields[] = $Model->{$relation}[$name]['foreignKey'];
								break;
							case 'hasOne':
							case 'hasMany':
								$mandatoryInnerFields = array($Model->{$relation}[$name]['foreignKey']);
								$mandatoryFields[] = $Model->primaryKey;
								break;
							case 'hasAndBelongsToMany':
								$mandatoryInnerFields = array($Model->$name->primaryKey);
								$mandatoryFields[] = $Model->primaryKey;
								break;
					}

					// Add mandatory fields to list of fields for this inner model

					if (isset($children['__settings__']) && isset($children['__settings__']['fields']) && !empty($mandatoryInnerFields))
					{
						$models[$name]['__settings__']['fields'] = array_unique(am($children['__settings__']['fields'], $mandatoryInnerFields));
					}
				}
			}

			// Add mandatory fields to list of fields for this model

			if (!empty($mandatoryFields))
			{
				$models['__settings__']['fields'] = array_unique(am($models['__settings__']['fields'], $mandatoryFields));
			}
		}

		return $models;
	}
}

?>