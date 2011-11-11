<?php

$prev_schema = array();

$migration_types = array(
  'string'=>'VARCHAR(255)',
  'integer'=>'INT(11)',
  'text'=>'TEXT',
  'datetime'=>'DATETIME',
);

// @return String: mysql type
// @params:
//  $type String: rails-migration-style type
function get_mysql_type($type) {
  global $migration_types;
  return get_key_or_val($migration_types, $type);
}

function get_migration_type($type) {
  global $migration_types;
  return get_key_or_val($migration_types, strtoupper($type), 'key');
}

// add_column("pages", "title", "string");
// => ALTER TABLE pages ADD title VARCHAR(255)
function add_column($table_name, $column_name, $type='string') {
  $mysql_type = get_mysql_type($type);
  $sql = "ALTER TABLE $table_name "
       . "ADD COLUMN $column_name $mysql_type";
  return $sql;
}

// remove_column("pages", "title");
// => ALTER TABLE pages DROP title
function remove_column($table_name, $column_name) {
  $sql = "ALTER TABLE $table_name "
       . "DROP COLUMN $column_name";
  return $sql;
}

// rename_column('pages', 'title', 'title_fr');
// => ALTER TABLE pages CHANGE title title_fr VARCHAR(255)
function rename_column($table_name, $column_name, $new_column_name) {
  global $prev_schema;
  $mysql_type = get_mysql_type($prev_schema[$table_name][$column_name]['type']);
  $sql = "ALTER TABLE $table_name CHANGE $column_name "
       . "$new_column_name $mysql_type";
  return $sql;
}

function rename_table($old_table_name, $new_table_name) {
  return "RENAME TABLE $old_table_name TO $new_table_name";
}


// TODO
// rename_table(old_name, new_name)
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
  $sql = "CREATE TABLE $table_name ("
       . "id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
  foreach($columns as $column=>$type) {
    $mysql_type = get_mysql_type($type);
    if ($type == 'timestamps') {
      $has_timestamp = true;
    } else {
      $sql .= "$column $mysql_type, ";
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
  return "DROP TABLE $table_name;";
}


// @class Migrator
// @author Julien Desrosiers
class Migrator {
  var $_db;
  var $_paths;
  var $_db_folder_path;

  // @params:
  //  $db: ezSQL Database object
  //  $db_folder_path String
  function Migrator($db, $db_folder_path) {
    $this->_db = $db;
    $this->_db_name = $this->_db->dbname;
    $this->_db_folder_path = $db_folder_path;
    $this->_create_migrations_table();
  }

// --------------------------------------------------------------
// public

  function migrate_all() {
    $this->_paths = glob($this->_db_folder_path.'/migrations/*.php');
    foreach ($this->_paths as $path) {
      $migration_name = basename($path, '.php');
      if (!$this->_is_migrated($migration_name)) {
        $this->migrate($path, $migration_name);
      }
    }
  }

  function migrate($path, $migration_name) {
    global $prev_schema;
    $prev_schema = $this->_load_schema_file();
    include_once($path); // include migration file
    list($v_num, $m_name) = $this->_explode_file_name($migration_name);
    $function_name = $m_name.'_'.$v_num;
    $this->_execute_queries($function_name());
    $this->_save_migration_id($v_num);
    $this->_write_schema_to_file($v_num);
  }

  // Migration methods ------------------------------------------


// --------------------------------------------------------------
// private

  function _save_migration_id($version_id) {
    $this->_db->query("INSERT INTO schema_migrations (migration_id) VALUES ({$version_id})");
  }

  function _is_migrated($path) {
    list($v_num, $m_name) = $this->_explode_file_name($path);
    return (int)$this->_db->get_var("SELECT count(*) FROM schema_migrations WHERE migration_id=".$v_num) > 0;
  }

  function _explode_file_name($path) {
    return explode('_', $path, 2);
  }

  function _execute_queries($queries) {
    foreach ($queries as $query) {
      $this->_db->query($query);
    }
  }

  function _create_migrations_table() {
    if ( ! $this->_db->query("SHOW TABLES LIKE 'schema_migrations'") ) {
      $query = create_table('schema_migrations', array(
        'migration_id'=>'integer'
      ));
      $this->_db->query($query);
    }
  }

  function _load_schema_file() {
    $filename = $this->_db_folder_path.'/schema.php';
    include $filename;
    $new_schema = array_shift($schema); // ['version'] => n
    foreach ($schema as $t_key=>$table) {
      foreach ($table as $f_key=>$field) {
        $k = array_keys($field);
        $new_schema[$t_key][$k[0]] = $field[$k[0]];
      }
    }
    return $new_schema;
  }

  function _write_schema_to_file($migration_number) {
    $schema_dumper = new MigrationSchemaDumper($migration_number, $this->_db);
    $schema_dumper->write_schema_to('./db/schema.php');
  }

}


class MigrationSchemaDumper {

  var $_migration_number;
  var $_db_instance;

  function MigrationSchemaDumper($migration_number, $db_instance) {
    $this->_migration_number = $migration_number;
    $this->_db_instance = $db_instance;
  }

  function write_schema_to($file) {
    $fp = fopen($file, 'w+');
    $file_content = "<?php\n\n\$schema = ".$this->_get_schema_code().";";
    fwrite($fp, $file_content);
    fclose($fp);
  }

  function _get_schema_code() {
    $tables = filter(
                pluck($this->_db_instance->get_results("SHOW TABLES"), "Tables_in_{$this->_db_instance->dbname}") ,
                f('$e', 'return $e != "schema_migrations";') );
    $schema = array(
      'schema_migration' => array('version'=>$this->_migration_number) );
    foreach ($tables as $table) {
      $schema[$table] = $this->_get_fields($table);
    }
    return var_export($schema, true);
  }

  function _get_fields($table_name) {
    $fields = map(
      $this->_db_instance->get_results("DESC $table_name"),
      f('$e', 'return array($e->Field => '
              . 'array("type"=>get_migration_type($e->Type)));'));
    return $fields;
  }

  function _get_migration_type($mysql_type) {
    global $migration_types;
    return get_key_or_val($migration_types, $mysql_type);
  }
}

