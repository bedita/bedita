<?php

class ApiValidatorTestData extends BeditaTestData {

    public $data = array(
        'checkDate' => array(
            'iso8601' => '2015-07-06T12:04:19+02:00',
            'iso8601-2' => '2015-07-06T12:04:19-04:00',
            'jsIso' => '2015-09-25T08:12:51.123Z',
            'errors' => array(
                // wrong format
                'format' => array(
                    '2015-07-06T12:04:19',
                    '2015-09-25 08:12:51.123Z'
                ),
                // invalid dates
                'invalid' => array(
                    '2015-07-32T12:04:19+02:00', // day
                    '2015-00-02T12:04:19+02:00', // month
                    '2015-07-25T56:04:19+02:00', // hour
                    '2015-07-25T07:88:19+02:00', // minutes
                    '2015-07-25T07:04:61+02:00', // seconds
                )
            )
        ),
        'checkUrlParams' => array(
            'endpoint1' => array('name1', 'name2'),
            '__all' => array('common'),
            'endpoint2' => array('_group1', 'name3'),
            '_group1' => array('groupname1', 'groupname2'),
            'endpoint3' => array('filter[name1]', 'filter[name2]', '_group1')
        ),
        'checkCustomProp' => array(
            array(
                'name' => 'custom_text',
                'object_type_id' => 22, // document
                'property_type' => 'text'
            ),
            array(
                'name' => 'custom_number',
                'object_type_id' => 22, // document
                'property_type' => 'number'
            ),
            array(
                'name' => 'custom_date',
                'object_type_id' => 22, // document
                'property_type' => 'date'
            ),
            array(
                'name' => 'custom_option',
                'object_type_id' => 22, // document
                'property_type' => 'options',
                'PropertyOption' => array('one', 'two', 'three')
            ),
            array(
                'name' => 'custom_multiple_options',
                'object_type_id' => 22, // document
                'property_type' => 'options',
                'multiple_choice' => 1,
                'PropertyOption' => array('four', 'five', 'six')
            ),
        )
    );

}
