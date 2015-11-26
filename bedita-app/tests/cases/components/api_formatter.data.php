<?php

class ApiFormatterTestData extends BeditaTestData {

    public $data = array(
        'removeObjectFields' => array(
            'formatting' => array(
                'fields' => array(
                    'remove' => array(
                        'description',
                        'title',
                        'Category' => array('name'),
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

        'formatUrlParams' => array(
            'queryString' => 'one=1,&two=2&filter[object_type]=document,event&embed[relations_detail]=poster,seealso|4,attach|15',
            'expected' => array(
                'one' => '1',
                'two' => '2',
                'filter' => array(
                    'object_type' => array('document', 'event')
                ),
                'embed' => array(
                    'relations_detail' => array(
                        'poster' => 1,
                        'seealso' => '4',
                        'attach' => '15'
                    )
                )
            )
        )
    );

}
