<?php 
/**
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class TreeTestData extends BeditaTestData {
	var $data =  array(
		
		'buildTree' => array(
			array('Area' => array(
						'title' 	=> 'Publishing 1',
						'status' => 'on',
						'children'	=> array(
							array('Section'	=> array('title' => 'Section 1', 'status' => 'on', 'children' => array(
								array('Section'	=> array('title' => 'Section 8', 'status' => 'draft', 'children' => array()))
							))),
							array('Section'	=> array('title' => 'Section 2', 'status' => 'on', 'children' => array())),
							array('Section'	=> array('title' => 'Section 3', 'status' => 'on', 'children' => array(
								array('Section'	=> array('title' => 'Section 6', 'status' => 'on', 'children' => array())),
								array('Section'	=> array('title' => 'Section 7', 'status' => 'on', 'children' => array(
									array('Section' => array('title' => 'Section 9', 'status' => 'on', 'children' => array(
										array('Document' => array('title' => 'Document 2', 'status' => 'on')),
										array('Event' => array('title' => 'Event 2', 'status' => 'off'))
									))))
								)),
								array('Document' => array('title' => 'Document 1', 'status' => 'on')),
								array('Event' => array('title' => 'Event 1', 'status' => 'on'))
							))),
							array('Section'	=> array('title' => 'Section 4', 'status' => 'on', 'children' => array())),
							array('Section'	=> array('title' => 'Section 5', 'status' => 'draft', 'children' => array()))
						)
				  )),
			array('Area' => array(
						'title' 	=> 'Publishing 2',
						'status' => 'off',
						'children'	=> array(
							array('Section'	=> array('title' => 'Section 10', 'status' => 'on' , 'children' => array(
								array('Section'	=> array('title' => 'Section 11', 'status' => 'draft' , 'children' => array())),
							))),
							array('Section'	=> array('title' => 'Section 12', 'status' => 'on', 'children' => array())),
							array('Section'	=> array('title' => 'Section 13', 'status' => 'off', 'children' => array(
								array('Section'	=> array('title' => 'Section 14', 'status' => 'on', 'children' => array())),
								array('Section'	=> array('title' => 'Section 15', 'status' => 'draft', 'children' => array())),
							))),
							array('Section'	=> array('title' => 'Section 16', 'status' => 'on', 'children' => array())),
							array('Section'	=> array('title' => 'Section 17', 'status' => 'on', 'children' => array()))
						)
				  )
			)
		)
	
	); 			
	
}
?> 