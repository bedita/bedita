<?php
class MailMessage extends BeditaContentModel
{
	var $actsAs 	= array(
			'CompactResult' 		=> array("MailGroup"),
			'SearchTextSave',
			'ForeignDependenceSave' => array('BEObject', 'Content'),
			'DeleteObject' 			=> 'objects',
	); 
	
	var $hasOne= array(
			'BEObject' => array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Content' => array(
					'className'		=> 'Content',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				)
		);
	
	var $hasAndBelongsToMany = array(
			'MailGroup' =>	array (
					'joinTable' => 'mail_group_messages'
				)
	);
		
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"RelatedObject"
															),
									 "Content", "MailGroup"
									),
				"default" => array("BEObject" => array("ObjectType", "RelatedObject"), "Content"),
									
				"mailgroup" => array("MailGroup"),

				"minimum" => array("BEObject" => array("ObjectType"))
	);
	
	var $validate = array(
		"subject" => array(
			"rule" 			=> array('custom', '/.+/') ,
			"required" 		=> true,
			"message" 		=> "Subject required"
		),
		
		"sender" => array(
			"rule"	=> "email",
			"required" => true,
			"message"	=> "Please supply a valid email address."
		)
	);
	
	
	function beforeValidate() {

        $this->checkDate('start');
        $this->checkDate('end');
		$this->checkDate('start_sending');

        $data = &$this->data[$this->name] ;
        if(!empty($data['start_sending']) && !empty($data['start_sending_time'])) {
            $data['start_sending'] .= " " . $data['start_sending_time'];
        }

        return true;
	}
}
?>