<?php
class create_pages_20111022160601 extends Migration {
  function up(){
    return array(
      create_table("pages", array(
        'title'=>'string',
        'slug'=>'string',
        'content'=>'text',
        'timestamps'
      ))
    );
  }
  function down(){
    return array(
      drop_table('pages')
    );
  }
}
