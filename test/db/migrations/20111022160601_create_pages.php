<?php

function up($db){
  return array(
    create_table("pages", array(
      'title'=>'string',
      'slug'=>'string',
      'content'=>'text',
      'timestamps'
    ))
  );
}

function down($db){
  return array(
    drop_table('pages')
  );
}
