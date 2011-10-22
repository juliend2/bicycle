<?php

function _get_mysql_type($type) {
    if ($type == 'string') {
      $mysql_type = 'VARCHAR(255)';
    } elseif ($type == 'text') {
      $mysql_type = 'TEXT';
    } elseif ($type == 'datetime') {
      $mysql_type = 'DATETIME';
    }
    return $mysql_type;
}

// add_column("pages", "title", "string");
//
// Will generate this SQL statement:
//
// ALTER TABLE pages ADD title VARCHAR(255);

function add_column($table_name, $column_name, $type='string') {
  $mysql_type = _get_mysql_type($type);
  $sql = 'ALTER TABLE '.$table_name.' '
       . 'ADD COLUMN '.$column_name.' '.$mysql_type;
  return $sql;
}

// remove_column("pages", "title");
//
// Will generate this SQL statement:
//
// ALTER TABLE pages DROP title;

function remove_column($table_name, $column_name) {
  $sql = 'ALTER TABLE '.$table_name.' '
       . 'DROP COLUMN '.$column_name;
  return $sql;
}


// create_table("pages", array(
//  'title' => 'string',
//  'slug' => 'string',
//  'content' => 'text',
//  'timestamps'
// ));
//
// Will generate this SQL statement:
//
// CREATE TABLE pages (
//  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
//  title VARCHAR(255),
//  slug VARCHAR(255),
//  content TEXT,
//  created_at DATETIME,
//  updated_at DATETIME,
// );

function create_table($table_name, $columns) {
  $sql = 'CREATE TABLE '.$table_name.' '
       . 'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,';
  
  foreach($columns as $column=>$type) {
    $mysql_type = _get_mysql_type($type);
    $sql .= $column . ' ' . $mysql_type;
  }

  if (in_array('timestamps', $columns)) {
    $str .= 'created_at DATETIME,'
          . 'updated_at DATETIME';
  }
  $str .= ');';

  return $str;
}

// drop_table($table_name);
//
// Will generate this SQL statement:
//
// DROP TABLE pages;

function drop_table($table_name) {
  return "DROP TABLE ". $table_name .";";
}



