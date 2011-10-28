<?php
function create_pages_20111022160601 () {
  return array(
    create_table("pages", array(
      'title'=>'string',
      'slug'=>'string',
      'content'=>'text',
      'timestamps'
    ))
  );
}
