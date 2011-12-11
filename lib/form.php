<?php

// do not instanciate this class, but subclass it and implement to_string()
class FormField {
  var $_form;
  var $_type;
  var $_name;
  var $_settings;

  function FormField($type, $name, $settings=array()) {
    $this->_type = $type;
    $this->_name = $name;
    $this->_settings = $settings;
  }

  // function to_string() {}

  function set_form($form) {
    $this->_form = $form;
  }

  function get_value() {
    $posted_form = $this->_form->_posted;
    $edited_object = $this->_form->_edited_object;
    if ($posted_form && isset($posted_form[$this->_name])) {
      return $posted_form[$this->_name];
    } elseif ($edited_object && isset($edited_object->{$this->_name})) {
      return $edited_object->{$this->_name};
    }
    return false;
  }

  function is_valid() {
    $field_definition = $this->_form->_field_definitions[$this->_name];
    $posted_form = $this->_form->_posted;
    if ($posted_form && isset($field_definition['rules'])) {
      if (in_array('not_empty', $field_definition['rules'])) {
        if (!isset($posted_form[$this->_name]) ||
          trim($posted_form[$this->_name]) === '') { 
          return false; 
        }
      }
    }
    return true;
  }

  function label() {
    return '<label for="'.$this->_name.'_field">'.$this->_human_field_name().'</label>';
  }

  // replace "_" for " " in field names
  function _human_field_name() {
    return strtr($this->_name, '_', ' ');
  }
}

class TextField extends FormField {
  function TextField($name, $settings=array()) {
    parent::FormField('text', $name);
  }

  function to_string() {
    return '<p '
      .'class="'.($this->is_valid()?'':'not_valid').' field" >'
      . $this->label().'<input id="'.$this->_name.'_field" '
      .'value="'.$this->get_value().'"'
      .'type="'.$this->_type.'" name="'.$this->_name.'"/></p>';
  }
}

class TextArea extends FormField {
  function TextArea($name, $settings=array()) {
    parent::FormField('textarea', $name);
  }

  function to_string() {
    return '<p '
      .'class="'.($this->is_valid()?'':'not_valid').' field" >'
      . $this->label().'<textarea id="'.$this->_name.'_field" name="'.$this->_name.'">'
      . $this->get_value()
      .'</textarea></p>';
  }
}

class Select extends FormField {
  var $options;
  function Select($name, $options, $settings=array()) {
    $this->options = $options;
    parent::FormField('select', $name);
  }

  // return: String
  function get_options() {
    $s = '';
    foreach ($this->options as $k=>$option) {
      $selected = ($this->get_value() == $k) ? $selected = 'selected="selected"' : '';
      $s .= '<option value="'.$k.'" '.$selected.'>'.$option.'</option>';
    }
    return $s;
  }

  function to_string() {
    return '<p '
      .'class="'.($this->is_valid()?'':'not_valid').' field" >'
      . $this->label().'<select id="'.$this->_name.'_field" name="'.$this->_name.'">'
      . $this->get_options()
      .'</select></p>';
  }
}

class CheckBox extends FormField {
  function CheckBox($name, $settings=array()) {
    parent::FormField('checkbox', $name);
  }

  function to_string() {
    $checked = ($this->get_value()) ? 'checked="checked"' : '';
    return '<p '
      .'class="'.($this->is_valid()?'':'not_valid').' field">'
      . $this->label().'<input id="'.$this->_name.'_field" '
      . $checked
      . ' type="checkbox" name="'.$this->_name.'" />'
      .'</p>';
  }
}

class Form {
  var $_action;
  var $_cancel_url;
  var $_fields = array();
  var $_model_object;
  var $_edited_object;
  var $_field_definitions;
  var $_posted;

  function Form($fields, $settings=array()) { 
    $this->_action = $settings['form_url'];
    $this->_cancel_url = $settings['cancel_url'];
    $this->_fields = $fields;
    foreach ($this->_fields as $field) {
      $field->set_form($this);
    }
    $this->_model_object = $settings['model'];
    $this->_edited_object = $settings['editable_object'];
    $this->_field_definitions = $this->_model_object->get_fields();
    $this->_posted = $_POST;
  }

  function to_string() {
    $s = $this->form_start($this->_action);
    foreach ($this->_fields as $f) {
      $s .= $f->to_string();
    }
    $s .= $this->form_end(empty($this->_edited_object) ? 'Create' : 'Edit' );
    $s .= ' or '.link_to('Cancel', $this->_cancel_url);
    return $s;
  }

  function form_start($action) {
    return '<form action="'.$action.'" method="POST">';
  }

  function form_end($value='Send') {
    return '<input type="submit" value="'.$value.'"/>'
           . '</form>';
  }

  function is_valid() {
    $this->_model_object->before_validate();
    foreach ($this->_fields as $f) {
      if (!$f->is_valid()) {
        return false;
      }
    }
    $this->_model_object->after_validate();
    return true;
  }

}


