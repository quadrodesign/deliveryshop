<?php
return array(
  'shop_deliveryshop_city' => array(
    'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
    'cityCode' => array('int', 14, 'null' => 0),
    'city' => array('varchar', 255, 'null' => ''),
    'region' => array('varchar', 255, 'null' => 0),
    'status' => array('varchar', 255, 'null' => 0),
    ':keys' => array(
      'PRIMARY' => 'id'),
  ),

  'shop_deliveryshop_delivery' => array(
    'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
    'domain' => array('varchar', 255, 'null' => 0),
    'price' => array('varchar', 255, 'null' => ''),
    ':keys' => array(
      'PRIMARY' => 'id'),
  ),

  'shop_deliveryshop_city_description' => array(
    'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
    'url' => array('varchar', 255, 'null' => 0),
    'cityCode' => array('int', 14, 'null' => 0),
    'ID_TK' => array('varchar', 255, 'null' => ''),
    'delivery_time' => array('varchar', 255, 'null' => 0),
    'delivery_price' => array('varchar', 255, 'null' => 0),
    'courier_time' => array('varchar', 255, 'null' => ''),
    'courier_price' => array('varchar', 255, 'null' => 0),
    'description' => array('text', 'null' => 0),
    'anons_shop' => array('text', 'null' => 0),
    'anons_delivery' => array('text', 'null' => 0),
    'meta_title' => array('varchar', 255, 'null' => 0),
    'meta_keywords' => array('text', 'null' => 0),
    'meta_description' => array('text', 'null' => 0),
    'domain' => array('varchar', 255, 'null' => 0),
    ':keys' => array(
      'PRIMARY' => 'id'),
  ),

  'shop_deliveryshop_pvz' => array(
    'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
    'code' => array('varchar', 40, 'null' => 0),
    'name' => array('varchar', 255, 'null' => 0),
    'cityCode' => array('int', 14, 'null' => 0),
    'city' => array('varchar', 100, 'null' => ''),
    'workTime' => array('varchar', 255, 'null' => ''),
    'address' => array('varchar', 255, 'null' => ''),
    'phone' => array('varchar', 50, 'null' => ''),
    'note' => array('text', 'null' => 0),
    'coordX' => array('varchar', 20, 'null' => ''),
    'coordY' => array('varchar', 20, 'null' => ''),
    'weightMin' => array('varchar', 20, 'null' => ''),
    'weightMax' => array('varchar', 20, 'null' => ''),
    'TK' => array('varchar', 255, 'null' => ''),
    'domain' => array('varchar', 255, 'null' => ''),
    'status' => array('varchar', 255, 'null' => ''),
    'email' => array('varchar', 255, 'null' => ''),
    ':keys' => array(
      'PRIMARY' => 'id'),
  ),
);