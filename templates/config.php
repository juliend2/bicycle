<?php

ini_set('display_errors', 1); // DEV ONLY! Comment this line before going live

$config = array(

  // basic configurations :
  'db.host' => 'localhost',
  'db.user' => 'root',
  'db.pass' => 'root',
  'db.name' => 'mywebsite',
  'base_url' => 'http://localhost/mywebsite',

  // admin configurations :
  'admin.sections' => array(
    'Pages' => 'pages'
  ),

  // multi-lingual configurations :
  'multilingual' => false,
  'multilingual.languages' => array(
    // 'fr' => array('locale'=>'fr-CA', 'name'=>'Français'),
    'en' => array('locale'=>'en-CA', 'name'=>'English')
  )
);

// SITE-SPECIFIC INCLUDES:
// include_once LIB_PATH.'/my_helpers.php';

// app's models:
// include_once MODEL_PATH.'/page.php';
// include_once MODEL_PATH.'/post.php';


