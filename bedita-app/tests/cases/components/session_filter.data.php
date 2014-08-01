<?php

class SessionFilterTestData extends BeditaTestData {

    public $data = array(
        'add' => array(
            'BEObject.title' => 'object title',
            'object_type_id' => array(1, 3)
        ),
        'addSanitized' => array(
            'query' => "<div><h1>Title</h1> <p>my text</p></div>",
            'BEObject.title' => "<script>alert('hello')</script> object title"
        )
    );

}
