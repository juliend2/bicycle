<?php

class FieldException extends Exception { }

class FormHelper {

  var $_model_instance = null;
  var $_db_object = null;

  function FormHelper($model_object, $db_object=null)
  {
    $this->_model_instance = $model_object;
    $this->_db_object = $db_object;
  }


// --------------------------------------------------------------
// public

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
      $this->label($label, $name).'<input type="'.$type.'" id="'.$name.'_input" name="'.$name.'" value="'.$value.'" '.attr_to_string($attr).'/>',
      array('id'=>$name.'_input_container', 'class'=> !$this->_field_is_valid($name) ? 'not_valid' : '')
    );
  }

  function text_input($name, $label, $value='', $attr=array())
  {
    return $this->basic_input($name, $label, 'text', $this->_field_value($name,$value), $attr=array());
  }

  function text_area($name, $label, $value='', $attr=array())
  {
    return p(
      $this->label($label, $name).'<textarea id="'.$name.'_input" name="'.$name.'" '.attr_to_string($attr).'>'.$this->_field_value($name,$value).'</textarea>',
      array('id'=>$name.'_input_container', 'class'=> !$this->_field_is_valid($name) ? 'not_valid' : '')
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
      $select .= $this->option_tag($k, $v, $k===$value);
    }
    $select .= '</select>';
    return p(
      $this->label($label, $name) . $select,
      array('id'=>$name.'_input_container', 'class'=> !$this->_field_is_valid($name) ? 'not_valid' : '')
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


// --------------------------------------------------------------
// private

  function _field_is_valid($name)
  {
    $fields = $this->_model_instance->get_fields();
    if (!isset($fields[$name]))
    {
      throw new FieldException('No field with this name');
    }
    return $fields[$name]->get_is_valid();
  }

  function _field_value($name, $value)
  {
    $model = $this->_model_instance;
    $fields = $model->get_fields();
    if ($model->is_posted())
    {
      return $fields[$name]->get_value();
    }
    else
    {
      if ($this->_db_object->{$name})
      {
        return $this->_db_object->{$name};
      }
      else
      {
        return $value;
      }
    }
  }
}

