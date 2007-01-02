<?php
class NotesController extends AppController {
	var $name = 'Notes';
	
	var $scaffold ;
	
	function __construct() {
		parent::__construct() ;
//		$this->view = 'Smarty';
	}
	
	function index() {
		$this->set('notes', $this->Note->findAll());
	}
/*
	function view($id) {
       $this->Note->id = $id;
       $this->set('data', $this->Note->read());
   }
  
	function add() {
   		if (!empty($this->data['Note'])) {
           if($this->Note->save($this->data['Note'])) {
                $this->flash('Your note has been updated.','/notes/');
           } else {
		   		//Danger, Will Robinson. Validation errors.
            	$this->set('errorMessage', 'Please correct errors below.');
            	$this->render();
		   }
       }
   }
   
	function edit($id = null) {
	   if (empty($this->data['Note'])) {
	       $this->Note->id = $id;
	       $this->data = $this->Note->read();
	   } else {
	       if($this->Note->save($this->data['Note'])) {
	            $this->flash('Your note has been updated.','/notes/');
	       }  else {
		   		//Danger, Will Robinson. Validation errors.
            	$this->set('errorMessage', 'Please correct errors below.');
            	$this->render();
		   }
	   }
	}

	function delete($id) {
	       if ($this->Note->del($id)) {
	           $this->flash('The note with id: '.$id.' has been deleted.', '/notes');
	       }
   }
*/
} 

?>