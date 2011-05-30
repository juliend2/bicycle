<?php
// DEPENDS ON :
//  -A GLOBAL $db VARIABLE IN bicycle.php
//  -A GLOBAL sluggize FUNCTION IN functions.php
//
// Base model class
class Model extends Validator {

  var $_db = null; // ezSQL_* instance
  var $_schema = array();

  function Model($db, $schema)
  {
    $this->_db = $db;
    $this->_schema = $schema;
    // second arg is false because we don't want to immediately validate
    parent::__construct($schema, false); 
  }

// public

  function insert_into($table_name, $data)
  {
    // get keys and values to be saved
    $sql = "INSERT INTO {$table_name} ";
    $field_strings = array();
    $value_strings = array();
    $is_first = true;
    foreach ($data as $key=>$value)
    {
      $field_strings .= ($is_first?',':'').' '.$key;
      $value_strings .= ($is_first?',':'').' '.$this->_quote_wrap($value, $this->_get_field_datatype($key));
      $is_first = false;
    }
    $sql .= '('.$field_strings.')';
    $sql .= ' VALUES ';
    $sql .= '('.$value_strings.')';
    return $sql;
  }

  function get_posted_data()
  {
    return $this->_posted;
  }

  function query($sql)
  {
    return $this->_db->query($sql);
  }

  function escape($string)
  {
    return mysql_real_escape_string($string);
  }

  function escape_array($string_array)
  {
    $new_array = array();
    foreach ($string_array as $key=>$string)
    {
      $new_array[$key] = self::escape($string);
    }
    return $new_array;
  }

// private

  function _get_field_datatype($fieldname)
  {
    $datatype = 'string'; // set a default
    if (!empty($this->_schema[$fieldname]['data_type']))
    {
      $datatype = $this->_schema[$fieldname]['data_type'];
    }
    elseif (!empty($this->_schema[$fieldname]['type']) && 
            in_array($this->_schema[$fieldname]['type'], array('text','radio','select')))
    {
      $datatype = 'string';
    }
    return $datatype;
  }

  function _quote_wrap($value, $data_type)
  {
    if (in_array($data_type, array('string','text','datetime','date')))
    {
      return "'".$value."'";
    }
    return $value;
  }

// filters

  function before_save() { }

  function after_save() { }

// protected

  function _sluggize($str, $separator='_')
  {
    return sluggize($str, $separator);
  }
}
