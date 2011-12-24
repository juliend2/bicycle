<?php

class Model {

  var $_schema = array();

  function Model($schema) {
    $this->_schema = $schema;
  }

  function get_fields() {
    return $this->fields;
  }

  // Filters

  function before_validate() {}
  function after_validate() {}
  function before_save() {}
  function after_save() {}


  // @return String: data type for the given field
  // @params:
  //  $fieldname String
  function _get_field_datatype($fieldname) {
    $datatype = 'string'; // set a default
    if (!empty($this->_schema[$fieldname]['type'])) {
      $datatype = $this->_schema[$fieldname]['type'];
    }
    return $datatype;
  }

  // @static
  // @return String: quoted string
  // @params:
  //  $value String
  //  $data_type String: (string, text, datetime, date)
  function quote_wrap($value, $data_type) {
    if (in_array($data_type, array('string','integer','text','datetime','date'))) {
      return "'".$value."'";
    }
    return $value;
  }

  // @return String: SQL statement
  // @params
  //  $table_name String: name of the table
  //  $data Array: data to be inserted
  //  $options Array: 'timestamp'=>true
  function insert($table_name, $data, $options=array()) {
    if (!empty($options['timestamps'])) {
      $current_datetime = date('Y-m-d H:i:s');
      $data['created_at'] = $current_datetime;
      $data['updated_at'] = $current_datetime;
    }
    $sql = "INSERT INTO {$table_name} ";
    $field_strings = '';
    $value_strings = '';
    $is_first = true;
    foreach ($data as $key=>$value) {
      $field_strings .= ($is_first?'':', ').$key;
      $value_strings .= ($is_first?'':', ').Model::quote_wrap($value, $this->_get_field_datatype($key));
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
  function update($table_name, $data, $conditions, $options=array()) {
    if (!empty($options['timestamps'])) {
      $current_datetime = date('Y-m-d H:i:s');
      $data['updated_at'] = $current_datetime;
    }
    $sql = "UPDATE {$table_name} SET ";
    $is_first = true;
    // we want the boolean fields to update their value to 0 if not checked
    $boolean_fields = array_flip(
      array_keys(filter($this->get_fields(), f('$f', 'return $f["type"] == "boolean";'))));
    $data = array_merge($boolean_fields, $data);

    foreach ($data as $key=>$value) {
      $type = $this->_get_field_datatype($key);
      if ($type == 'boolean') { $value = $value ? '1' : '0'; }
      $sql .= ($is_first?'':', ').$key.'='.Model::quote_wrap($value, $type);
      $is_first = false;
    }
    $sql .= ' '.$this->where($conditions);
    return $sql;
  }

  // @return Array: escaped array
  // @params
  //  $string_array Array of Strings
  function escape_array($string_array) {
    $new_array = array();
    foreach ($string_array as $key=>$string) {
      $new_array[$key] = Model::escape($string);
    }
    return $new_array;
  }

  function escape($string) {
    return mysql_real_escape_string($string);
  }

  // @return String: SQL statement
  // @params
  //  $conditions Array: array of conditions (k=>v or just string values)
  function where($conditions) {
    $sql = "WHERE ";
    $is_first = true;
    foreach ($conditions as $key=>$value) {
      if (is_int($key)) {
        $sql .= ($is_first?'':' AND ').$value;
      } else {
        $sql .= ($is_first?'':' AND ').$key.'='.Model::quote_wrap($value, $this->_get_field_datatype($key));
      }
      $is_first = false;
    }
    return $sql;
  }

  function _sluggize($str, $separator='_') {
    return sluggize($str, $separator);
  }
}

