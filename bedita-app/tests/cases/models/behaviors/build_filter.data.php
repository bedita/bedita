<?php

class BuildFilterTestData extends BeditaTestData {

    public $data = array(
        'simpleFilter' => array('object_type_id' => 1),

        'simpleFilterArr' => array('object_type_id' => array(1, 3, 22)),

        'selectStreamFields' => array('Stream.*' => ''),

        'notFilter' => array(
            'NOT' => array(
                'BEObject.status' => array('draft', 'off')
            )
        ),

        'notOldStyle' => array(
            'Stream.mime_type' => array('NOT'  => array('image/jpeg', 'image/png'))
        ),

        'orFilter' => array(
            'object_type_id' => 1,
            'OR' => array(
                'BEObject.status' => 'off',
                'BEObject.id' => array(10, 20)
            )
        ),

        'orOnSameField' => array(
            'OR' => array(
                array('BEObject.title LIKE' => '%one%'),
                array('BEObject.title LIKE' => '%two%')
            )
        ),

        'signedFilter' => array(
            'Content.end_date <=' => '2014-06-30',
            'BEObject.title LIKE' => '%title%'
        ),

        'complexConditions' => array(
            'Content.start_date >' => '2014-06-23 09:30:00',
            'OR' => array(
                array('BEObject.title' => 'title one'),
                array('BEObject.title' => 'title two')
            ),
            'AND' => array(
                array(
                    'OR' => array(
                        array('Stream.mime_type' => 'image/png'),
                        'NOT' => array(
                            array('BEObject.status' => array('on', 'off'))
                        )
                    )
                )
           )
        ),

        'sqlInjection' => array(
            'BEObject.object_type_id' => "1' OR '1' = '1"
        )

    );

}
