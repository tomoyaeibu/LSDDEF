<?php
// mongo Instance
$mongo = new Mongo();
// select DB and Collection
$db = $mongo->selectDB("db_test");
$coll = $db->selectCollection("coll_test");

// [[[[write section]]]]
$coll->update(
    array('user_id' => '001'), 
    array('$set' => array('user_name' => 'Abe')), 
    array('upsert' => true) 
);
$coll->update(
    array('user_id' => '002'), 
    array('$set' => array('user_name' => 'Seyama')), 
    array('upsert' => true)  //UPDATE OR INSERT
);

// [[[[read section]]]]
// read all Document
$docs = $coll->find();

// [print section] 
foreach ($docs as $obj) {
    print var_dump($obj);
    print ("\n");
}
?>
