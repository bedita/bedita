<?php
/**
 *
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */
class SectionTestData extends BeditaTestData {
	var $data =  array(
		'tree' => array(
			'area'	=> array(
				'title' => 'Pubblishing title'
			),
			'section' => array(
				'title' => 'Section title',
				'syndicate' => 'off',
				'priority_order' => 'desc',
				'children' => array(
					'Document' => array("title" => "doc 1"),
					'Event' => array("title" => "event 1")
				)
			),
			'subsection' => array(
				'title' => 'Subsection title',
				'children' => array(
					'Gallery' => array("title" => "gallery 1"),
					'Document' => array("title" => "doc 2")
				)
			)
		)
	);
}

?>