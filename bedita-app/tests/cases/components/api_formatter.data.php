<?php

class ApiFormatterTestData extends BeditaTestData {

    public $data = array(
        'removeObjectFields' => array(
            'formatting' => array(
                'fields' => array(
                    'remove' => array(
                        'description',
                        'title',
                        'Category' => array('id'),
                        'GeoTag' => array('title'),
                        'Tag'
                    )
                )
            )
        ),

        'keepObjectFields' => array(
            'formatting' => array(
                'fields' => array(
                    'keep' => array(
                        'fixed',
                        'ip_created',
                        'Category' => array('object_type_id', 'priority'),
                        'Tag'
                    )
                )
            )
        ),
    );

}
