<?php
/**
 * Use case data
 * @author ste
 *
 */
class ObjectRelationTestData extends BeditaTestData {
    var $data =  array(

        'doc1' => array(
            'title' => "test doc1 relations",
        ),

        'doc2' => array(
            'title' => "test doc2 relations",
            'RelatedObject' => array(
                'testRelation' => array(
                    array(
                        "switch" => 'testRelation',
                        "priority" => 1,
                        "params" => array("p1" => 3, "p2" => "YES")
                    )
                )
            )
        ),

        'relationParms' => array(
            "switch" => 'testRelation2',
            "priority" => 10,
            "params" => array("p1" => 10, "p2" => "NO")
        ),

        'relationNewPrior' => 5,

        'relationNewParms' => array("p1" => 15, "p2" => "NONO"),

        'relationNoParms' => array(
            "switch" => 'testRelation3',
            "inverse" => 'inverseRelation3'
        ),
    );
}
?>