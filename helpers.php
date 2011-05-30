<?php

function url_for($relative_path)
{
  if (strpos($relative_path, '/') !== 0)
  {
    $relative_path = '/'.$relative_path;
  }
  return BASE_URL.$relative_path;
}

function e($string)
{
  echo $string;
}

function pr($object)
{
  e('<pre>');
  print_r($object);
  e('</pre>');
}

function attr_to_string($attrs)
{
  $attr_string = '';
  foreach ($attrs as $k=>$v)
  {
    $attr_string .= " {$k}=\"{$v}\"";
  }
  return $attr_string;
}

// ==========================================================
// HTML helpers
// ----------------------------------------------------------

function link_to($label, $relative_path, $options = array())
{
  $absolute_path = url_for($relative_path);
  return "<a href='{$absolute_path}'>{$label}</a>";
}


function p($string, $attr=array())
{
  return '<p '.attr_to_string($attr).'>'. $string .'</p>';
}


// ==========================================================
// Form helpers
// ----------------------------------------------------------

function form_tag($action, $method="POST", $attr=array())
{
  return '<form action="'.$action.'" method="'.$method.'" '.attr_to_string($attr).'>';
}

function label($label, $for, $attr=array())
{
  return '<label for="'.$for.'_input" '.attr_to_string($attr).'>'.$label.'</label>';
}

function basic_input($name, $label, $type='text', $value='', $attr=array())
{
  return p(
    label($label, $name).'<input type="'.$type.'" id="'.$name.'_input" name="'.$name.'" value="'.$value.'" '.attr_to_string($attr).'/>',
    array('id'=>$name.'_input_container')
  );
}

function text_input($name, $label, $value='', $attr=array())
{
  return basic_input($name, $label, 'text', $value, $attr=array());
}

function text_area($name, $label, $value='', $attr=array())
{
  return p(
    label($label, $name).'<textarea id="'.$name.'_input" name="'.$name.'" '.attr_to_string($attr).'>'.$value.'</textarea>',
    array('id'=>$name.'_input_container')
  );
}

function option_tag($key, $value, $selected=false, $attr=array())
{
  return '<option '.attr_to_string($attr).' value="'.$key.'"'
         .($selected?' selected="selected"':'')
         .'>'.$value.'</option>';
}

function select_input($name, $label, $options=array(), $value=null, $attr=array())
{
  $select = '<select id="'.$name.'_input" name="'.$name.'" '.attr_to_string($attr).'>';
  foreach ($options as $k=>$v)
  {
    $select .= option_tag($k, $v, $k===$value);
  }
  $select .= '</select>';
  return p(
    label($label, $name) . $select,
    array('id'=>$name.'_input_container')
  );
}

// Returns a submit button followed by the end of the form tag
function end_form($submit_value='Submit', $attr=array())
{
  return p(
    '<input type="submit" value="'.$submit_value.'" '.attr_to_string($attr).'/>',
    array('id'=>sluggize($submit_value).'_submit_button')
  ) . '</form>';
}

