<?php

include '../Validator.php';
$data = array(
    'name' => 'tonera',
    'age' => 18,
    'sex' => true,
    'address' => 'beijing',
    'postcode' => '100034',
);
$rules = array(
    'name' => array('type' => 'string', 'len' => 8, 'lenMax' => 8, 'lenMin' => 2, 'notNull' => true),
    'sex' => array('type' => 'bool'),
    'address' => array('type' => 'string', 'match' => "/jing$/i"),
    'postcode' => array('type' => 'numeric', 'len' => 6),
);
$v=new Validator();
$v->init($rules, $data);
$r = $v->validate();
print_r($v->errors);
var_dump($r);