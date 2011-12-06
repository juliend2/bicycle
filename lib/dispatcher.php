<?php

class Dispatcher {

  var $_uri = '';
  var $_is_multilingual = false;
  var $_locale = '';
  var $_params = array();
  var $_param_separator = ':';
  var $_segments = '';

  function Dispatcher($uri, $is_multilingual=false) {
    $this->_segments = explode('/', $uri);
    $this->_is_multilingual = $is_multilingual;

    $this->_parse_uri();
  }

  function get_segments() {
    return $this->_segments;
  }

  function get_locale() {
    return $this->_locale;
  }

  function get_params() {
    return $this->_params;
  }

  function _parse_uri() {
    if (!empty($this->_segments) && $this->_segments[0] ==='') { // remove empty element
      array_shift($this->_segments);
    }
    if (!empty($this->_segments) && count($this->_segments) === 1 && $this->_segments[0] ==='') { // remove empty element (the other side of an empty '/') 
      array_shift($this->_segments);
    }

    if ($this->_is_multilingual) {
      $this->_locale = array_shift($this->_segments);
    }

    foreach ($this->_segments as $k=>$segment) { # populate params array
      # TODO: remember to NOT allow ":" characters on slug values

      $found = strpos($segment, $this->_param_separator);

      if ( $found !== false               # Found the ":" character and
        && $found !== 0                   # it's Not the first char of segment and
        && $found !== strlen($segment)-1) # it's Not the last char of the segment
      {
        $split_segment = explode($this->_param_separator, $segment);
        $this->_params[ $split_segment[0] ] = $split_segment[1];
        unset($this->_segments[$k]);
      }
    }
  }
}



