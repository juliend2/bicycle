<?php

$migration_types = array(
  'string'=>'VARCHAR(255)',
  'integer'=>'INT(11)',
  'text'=>'TEXT',
  'datetime'=>'DATETIME',
);

// @return String: mysql type
// @params:
//  $type String: rails-migration-style type
function _get_mysql_type($type) {
  global $migration_types;
  return get_key_or_val($migration_types, $type);
}

function _get_migration_type($mysql_type) {
  global $migration_types;
  return get_key_or_val($migration_types, $type, 'key');
}

// add_column("pages", "title", "string");
// => ALTER TABLE pages ADD title VARCHAR(255)
function add_column($table_name, $column_name, $type='string') {
  $mysql_type = _get_mysql_type($type);
  $sql = 'ALTER TABLE '.$table_name.' '
       . 'ADD COLUMN '.$column_name.' '.$mysql_type;
  return $sql;
}

// remove_column("pages", "title");
// => ALTER TABLE pages DROP title
function remove_column($table_name, $column_name) {
  $sql = 'ALTER TABLE '.$table_name.' '
       . 'DROP COLUMN '.$column_name;
  return $sql;
}

// rename_column('pages', 'title', 'title_fr');
// => ALTER TABLE pages CHANGE title title_fr VARCHAR(255)
function rename_column($table_name, $column_name, $new_column_name) {
  $sql = 'ALTER TABLE '.$table_name.' CHANGE '.$column_name.' '
    . $new_column_name.' '/* DATA TYPE */;
  // TODO: get the column's data type somewhere (schema.php?)
  return $sql;
}

// TODO
// rename_table(old_name, new_name)
// rename_column(table_name, column_name, new_column_name)
// change_column(table_name, column_name, type, options)
// add_index(table_name, column_names, options)
// remove_index(table_name, :column => column_name)
// remove_index(table_name, :name => index_name)
// see: http://api.rubyonrails.org/classes/ActiveRecord/Migration.html

// create_table("pages", array(
//  'title' => 'string',
//  'slug' => 'string',
//  'content' => 'text',
//  'timestamps'
// ));
// => 
// CREATE TABLE pages (
//  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
//  title VARCHAR(255),
//  slug VARCHAR(255),
//  content TEXT,
//  created_at DATETIME,
//  updated_at DATETIME,
// );
function create_table($table_name, $columns) {
  $has_timestamp = false;
  $sql = 'CREATE TABLE '.$table_name.' ('
       . 'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,';

  foreach($columns as $column=>$type) {
    $mysql_type = _get_mysql_type($type);
    if ($type == 'timestamps') {
      $has_timestamp = true;
    } else {
      $sql .= $column . ' ' . $mysql_type . ', ';
    }
  }

  if ($has_timestamp) {
    $sql .= 'created_at DATETIME,'
          . 'updated_at DATETIME';
  }
  $sql .= ');';

  return $sql;
}

// drop_table($table_name);
// => DROP TABLE pages;
function drop_table($table_name) {
  return "DROP TABLE ". $table_name .";";
}

// @class Migrator
// @author Julien Desrosiers
class Migrator 
{
  var $_db;
  var $_paths;
  var $_path_to_migrations;

  // @params:
  //  $db: ezSQL Database object
  //  $path_to_migrations String
  function __construct($db, $path_to_migrations)
  {
    $this->_db = $db;
    $this->_path_to_migrations = $path_to_migrations;
    $this->_create_migrations_table();
  }
  
  function migrate_all()
  {
    $this->_paths = glob($this->_path_to_migrations.'/*.php');
    foreach ($this->_paths as $path)
    {
      $migration_name = basename($path, '.php');
      if (!$this->_is_migrated($migration_name))
      {
        $this->migrate($path, $migration_name);
      }
    }
  }

  function migrate($path, $migration_name)
  {
    include_once($path);
    list($v_num, $m_name) = $this->_explode_file_name($migration_name);
    $function_name = $m_name.'_'.$v_num;
    $this->_execute_queries($function_name());
    $this->_save_migration_id($v_num);
  }

  function _save_migration_id($version_id)
  {
    $this->_db->query("INSERT INTO schema_migrations (migration_id) VALUES ({$version_id})");
  }

  function _is_migrated($path)
  {
    list($v_num, $m_name) = $this->_explode_file_name($path);
    return (int)$this->_db->get_var("SELECT count(*) FROM schema_migrations WHERE migration_id=".$v_num) > 0;
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
    if ( ! $this->_db->query("SHOW TABLES LIKE 'schema_migrations'") )
    {
      $query = create_table('schema_migrations', array(
        'migration_id'=>'integer'
      ));
      $this->_db->query($query);
    }
  }
}

