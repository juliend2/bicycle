<?php

function _get_mysql_type($type) {
    if ($type == 'string') {
      $mysql_type = 'VARCHAR(255)';
    } elseif ($type == 'integer') {
      $mysql_type = 'INT(11)';
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
  $sql = 'CREATE TABLE '.$table_name.' ('
       . 'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,';
  
  foreach($columns as $column=>$type) {
    $mysql_type = _get_mysql_type($type);
    $sql .= $column . ' ' . $mysql_type;
  }

  if (in_array('timestamps', $columns)) {
    $sql .= 'created_at DATETIME,'
          . 'updated_at DATETIME';
  }
  $sql .= ');';

  return $sql;
}

// drop_table($table_name);
//
// Will generate this SQL statement:
//
// DROP TABLE pages;

function drop_table($table_name) {
  return "DROP TABLE ". $table_name .";";
}

class Migration
{
  var $_db;

  function __construct($db)
  {
    $this->_db = $db;
  }

  function up(){}

  function down(){}

}

class Migrator 
{
  var $_db;
  var $_paths;
  var $_path_to_migrations;

  function __construct($db, $path_to_migrations)
  {
    $this->_db = $db;
    $this->_path_to_migrations = $path_to_migrations;
    $this->_create_migrations_table();
  }
  
  function migrate_all($direction='up')
  {
    $this->_paths = glob($this->_path_to_migrations.'/*.php');
    foreach ($this->_paths as $path)
    {
      $migration_name = basename($path, '.php');
      if (!$this->_is_migrated($migration_name))
      {
        $this->migrate($path, $migration_name, $direction);
      }
    }
  }

  function migrate($path, $migration_name, $direction)
  {
    include_once($path);
    list($v_num, $m_name) = $this->_explode_file_name($migration_name);
    $class_name = $m_name.'_'.$v_num;
    $migration = new $class_name($this->_db);
    if ($direction == 'up')
    {
      $this->_execute_queries($migration->up($this->_db));
      // TODO: add this migration into schema_migrations
    } 
    elseif ($direction == 'down')
    {
      $this->_execute_queries($migration->down($this->_db));
      // TODO: remove this migration from schema_migrations
    }
  }

  function _is_migrated($path)
  {
    list($v_num, $m_name) = $this->_explode_file_name($path);
    return (bool)$this->_db->get_results("SELECT count(*) FROM schema_migrations WHERE migration_id=".$v_num);
  }

  function _explode_file_name($path)
  {
    return explode('_', $path, 2);
  }

  function _execute_queries($queries)
  {
    foreach ($queries as $query)
    {
      $this->_db->query($query);
    }
  }

  function _create_migrations_table()
  {
    if( ! $this->_db->query("SHOW TABLES LIKE 'schema_migrations'"))
    {
      $query = create_table('schema_migrations', array(
        'migration_id'=>'integer'
      ));
      $this->_db->query($query);
    }
  }
}

