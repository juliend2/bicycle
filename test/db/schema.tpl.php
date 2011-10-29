<?php

$schema = array(
  'schema_migration'=>array('version'=>20111022160601),

  'pages'=>array(
    'title'=>array('type'=>'string'),
    'slug'=>array('type'=>'string'),
    'content'=>array('type'=>'text'),
    'created_at'=>array('datetime'),
    'updated_at'=>array('datetime')
  )
);

