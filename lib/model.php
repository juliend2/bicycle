<?php
// DEPENDS ON :
//  -A GLOBAL $db VARIABLE IN bicycle.php
//  -A GLOBAL sluggize FUNCTION IN functions.php
//
// Base model class
class Model extends Validator {

  var $_db = null; // ezSQL_* instance
  var $_table_name = '';
  var $_schema = array();

  function Model($db, $table_name, $schema)
  {
    $this->_db = $db;
    $this->_table_name = $table_name;
    $this->_schema = $schema;
    // second arg is false because we don't want to immediately validate:
    parent::__construct($schema, false); 
  }


// --------------------------------------------------------------
// public

  function get_db()
  {
    return $this->_db;
  }

  function get_posted_data()
  {
    return $this->_posted;
  }

  function get_table_name()
  {
    return $this->_table_name;
  }

  function get_schema()
  {
    return $this->_schema;
  }

  // @return Array: escaped array
  // @params
  //  $string_array Array of Strings
  function escape_array($string_array)
  {
    $new_array = array();
    foreach ($string_array as $key=>$string)
    {
      $new_array[$key] = self::escape($string);
    }
    return $new_array;
  }


// SQL abstractions ---------------------------------------------

  // @return String: SQL statement
  // @params
  //  $table_name String: name of the table
  //  $data Array: data to be inserted
  //  $options Array: 'timestamp'=>true
  function insert_into($table_name, $data, $options=array())
  {
    if (!empty($options['timestamps']))
    {
      $current_datetime = date('Y-m-d H:i:s');
      $data['created_at'] = $current_datetime;
      $data['updated_at'] = $current_datetime;
    }
    $sql = "INSERT INTO {$table_name} ";
    $field_strings = '';
    $value_strings = '';
    $is_first = true;
    foreach ($data as $key=>$value)
    {
      $field_strings .= ($is_first?'':', ').$key;
      $value_strings .= ($is_first?'':', ').$this->_quote_wrap($value, $this->_get_field_datatype($key));
      $is_first = false;
    }
    $sql .= '('.$field_strings.')';
    $sql .= ' VALUES ';
    $sql .= '('.$value_strings.')';
    return $sql;
  }

  // @return String: SQL statement
  // @params
  //  $table_name String: name of the table
  //  $data Array: data to be updated
  //  $conditions Array: array of conditions (k=>v or just string values)
  //  $options Array: 'timestamp'=>true
  function update($table_name, $data, $conditions, $options=array())
  {
    if (!empty($options['timestamps']))
    {
      $current_datetime = date('Y-m-d H:i:s');
      $data['updated_at'] = $current_datetime;
    }
    $sql = "UPDATE {$table_name} SET ";
    $is_first = true;
    foreach ($data as $key=>$value)
    {
      $sql .= ($is_first?'':', ').$key.'='.$this->_quote_wrap($value, $this->_get_field_datatype($key));
      $is_first = false;
    }
    $is_first = true;
    $sql .= ' '.$this->where($conditions);
    return $sql;
  }

  // @return String: SQL statement
  // @params
  //  $table_name String: name of the table
  //  $conditions Array: array of conditions (k=>v or just string values)
  function delete_from($table_name, $conditions)
  {
    $sql = "DELETE FROM {$table_name} ";
    return $sql . $this->where($conditions);
  }

  // @return String: SQL statement
  // @params
  //  $conditions Array: array of conditions (k=>v or just string values)
  function where($conditions)
  {
    $sql = "WHERE ";
    $is_first = true;
    foreach ($conditions as $key=>$value)
    {
      if (is_int($key))
      {
        $sql .= ($is_first?'':' AND ').$value;
      }
      else
      {
        $sql .= ($is_first?'':' AND ').$key.'='.$this->_quote_wrap($value, $this->_get_field_datatype($key));
      }
      $is_first = false;
    }
    return $sql;
  }


// ezSQL proxy methods ------------------------------------------

  function query($sql)
  {
    return $this->_db->query($sql);
  }

  function get_results($sql)
  {
    return $this->_db->get_results($sql);
  }

  function get_row($sql)
  {
    return $this->_db->get_row($sql);
  }

  function escape($string)
  {
    return mysql_real_escape_string($string);
  }


// Filters ------------------------------------------------------

  function before_save() { }

  function after_save() { }


// --------------------------------------------------------------
// private

  // @return String: data type for the given field
  // @params:
  //  $fieldname String
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

  // @return String: quoted string
  // @params:
  //  $value String
  //  $data_type String: (string, text, datetime, date)
  function _quote_wrap($value, $data_type)
  {
    if (in_array($data_type, array('string','text','datetime','date')))
    {
      return "'".$value."'";
    }
    return $value;
  }


// --------------------------------------------------------------
// protected

  function _sluggize($str, $separator='_')
  {
    return sluggize($str, $separator);
  }

}
