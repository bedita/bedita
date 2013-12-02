<?php
/**
 * CategoryTestData class
 */
class CategoryTestData extends BeditaTestData {
	var $data =  array(
        'document' => array(
            'insert' => array(
                'title' => "test document",
                'description' => "Tag and categorize me",
                'user_created' => 1,
                'object_type_id' => 22
            )
        ),

        'category' => array(
            'insert' => array('label' => 'category one', 'object_type_id' => 22), // category of document
            'nameExpected' => 'category-one'
        ),

        'mediaCategory' => array(
            'insert' => array('label' => 'image', 'object_type_id' => 12), // category of image
            'nameExpected' => 'image'
        ),

        'tag' => array(
            'insert' => array(
                array('label' => 'tag one'),
                array('label' => 'tag two'),
                array('label' => 'tag three')
            ),
            'insertOrphan' => array(
                array('label' => 'tag orphan one'),
                array('label' => 'tag orphan two')
            ),
            'insertOffDraft' => array(
                array('label' => 'tag off', 'status' => 'off'),
                array('label' => 'tag draft', 'status' => 'draft')
            )
        )
	);
}

?>