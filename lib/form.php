<?php

function form(/* action, method, [fields ...] */) {
  $args = func_get_args();
  $action = array_shift($args);
  $methods = array_shift($args);
  $valid = true;
  $s = "<form action='$action' method='$methods'>\n";
  foreach ($args as $field) { // remaining args are fields...
    $s .= $field['html'];
    if ($field['valid']===false) { $valid = false; }
  }
  $s .= "<input type='submit'/>\n";
  $s .= "</form>\n";
  return array(
    'valid' => $valid,
    'html' => $s
  );
}

function field_is_valid($rules, $field_name) {
  if (isset($_POST[$field_name]) && isset($rules[$field_name]['rules'])) {
    foreach ($rules[$field_name]['rules'] as $rule) {
      if ($rule == 'not_empty' && trim($_POST[$field_name]) === '') {
        return false;
      }
      // TODO: add more rules here ...
    }
  }
  return true;
}

function field_value($field_name, $value = null) {
  return isset($_POST[$field_name]) ? $_POST[$field_name] : (is_null($value)?'':$value);
}

function _human_field_name($field_name) {
  return strtr($field_name, '_', ' ');
}

function _select_options($options, $value = null) {
  $s = '';
  foreach ($options as $k=>$option) {
    $selected = ($value === $k) ? 'selected="selected"' : '';
    $s .= '<option value="'.$k.'" '.$selected.'>'.$option.'</option>';
  }
  return $s;
}

function label($field_name, $field_human_name = null) {
  $field_human_name = is_null($field_human_name) ? _human_field_name($field_name) : $field_human_name;
  return "<label for='$field_name'>$field_human_name</label>\n";
}

// _.o0()0o._.o0()0o._.o0()0o._.o0()0o._.o0()0o._.o0()0o._.o0()0o._.o0()0o._
// ## FIELD TYPES ##########################################################
// ‾°•0()0•°‾°•0()0•°‾°•0()0•°‾°•0()0•°‾°•0()0•°‾°•0()0•°‾°•0()0•°‾°•0()0•°‾

function hidden($rules, $field_name, $value = null) {
  $valid = field_is_valid($rules, $field_name);
  return array(
    'valid' => $valid,
    'html' => '<input id="'.$field_name.'_field" '
      . " type='hidden' name='$field_name' value='1' />\n"
  );
}

function text($rules, $field_name, $value = null) {
  $valid = field_is_valid($rules, $field_name);
  return array(
    'valid' => $valid,
    'html' => '<p class="'.($valid?'':'not_valid').' field">'
      . label($field_name).'<input id="'.$field_name.'_field" '
      . 'value="'.field_value($field_name, $value).'"'
      . "type='text' name='$field_name'/></p>\n"
  );
}

function text_area($rules, $field_name, $value = null) {
  $valid = field_is_valid($rules, $field_name);
  return array(
    'valid' => $valid,
    'html' => '<p class="'.($valid?'':'not_valid').' field">'
      . label($field_name).'<textarea name="'.$field_name.'" id="'.$field_name.'_field">'
      . field_value($field_name, $value)."</textarea></p>\n"
  );
}

function select($rules, $field_name, $options, $value = null) {
  $valid = field_is_valid($rules, $field_name);
  return array(
    'valid' => $valid,
    'html' => '<p class="'.($valid?'':'not_valid').' field">'
      . label($field_name).'<select id="'.$field_name.'_field" name="'.$field_name.'">'
      . _select_options($options, $value)
      . '</select></p>'
  );
}

function checkbox($rules, $field_name, $value = null) {
  $valid = field_is_valid($rules, $field_name);
  $hidden = hidden($rules, $field_name, '1');
  return array(
    'valid' => $valid,
    'html' => '<p class="'.($valid?'':'not_valid').' field">'
      . label($field_name).'<input id="'.$field_name.'_field" '
      . ($value ? 'checked="checked"' : '' )
      . " type='checkbox' name='$field_name' value='1' /></p>\n"
  );
}

