<?php
/**
 *
 * @filesource
 * @copyright		Copyright (c) 2007
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license
 * @author 		giangi giangi@qwerg.com	
 * 		
 * 						
*/
class Question extends BEAppObjectModel
{
	var $recursive 	= 2 ;
	var $actsAs 	= array(
			'CompactResult' 		=> array('QuestionType','answers'),
			'ForeignDependenceSave' => array('BEObject', 'Content'),
			'DeleteObject' 			=> 'objects',
	); 

	var $validate = array(
		'question_type_id' 	=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
	) ;

	var $hasOne = array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Content' =>
				array(
					'className'		=> 'Content',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
		) ;			

	var $belongsTo = array(
		'QuestionType' =>
			array(
				'className'		=> 'QuestionType',
				'foreignKey'	=> 'question_type_id',
				'conditions'	=> ''
			),
	) ;
	
	var $hasMany = array(
		'answers' =>
				array(
					'className'		=> 'Answer',
					'foreignKey'	=> 'question_id',
					'dependent'		=> true
				),
		) ;	
		

	/**
	 * Definisce i valori di default.
	 */		
	function beforeValidate() {
		if(isset($this->data[$this->name])) $data = &$this->data[$this->name] ;
		else $data = &$this->data ;
		
	 	$default = array(
			'question_type_id' => array('_getDefaultQuestionType', 	(isset($data['question_type_id']))?$data['question_type_id']:null),
		) ;
		
		foreach ($default as $name => $rule) {
			if(!is_array($rule)) {
				$data[$name] = $rule ;
				continue ;
			}
			
			$method = $rule[0];
			unset($rule[0]);
			
			if (method_exists($this, $method)) {
				$data[$name] = call_user_func_array(array(&$this, $method), $rule);
			} 
		}

		return true ;
	}

	/**
	 * Prima cancella le domande associate
	 *
	 * @param unknown_type $value
	 * @return unknown
	 */
	function beforeDelete() {
		// Preleva l'elenco delle risposte
		$this->bviorCompactResults 	= true ;
		$this->bviorHideFields		= array("Index", "CustomProperties", "Permission", "Version", "langObjs", "images", "attachments", "multimedia", "links") ;
		$domanda = $this->findById($this->{$this->primaryKey}) ;
		
		for ($i=0; $i < count($domanda['answers']) ; $i++) {
			if(!isset($domanda['answers'][$i]['id'])) continue ;
			
			if(!$this->Answer->delete($domanda['answers'][$i]['id'])) {
				return false ;
			}
		}
		
		return true;
	}
	
	
	private function _getDefaultQuestionType($value = null) {
		if(isset($value)) return $value ;

		$conf = Configure::getInstance() ;
		return ((isset($conf->questionTypeDefault))?$conf->questionTypeDefault:'') ;
	}
}
?>
