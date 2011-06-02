<?php

class Dispatcher {

  var $_uri = '';
  var $_params = array();
  var $_param_separator = ':';
  var $_segments = '';

  function Dispatcher($uri)
  {
    $this->_segments = explode('/', $uri);

    $this->_parse_uri();
  }

  function get_segments()
  {
    return $this->_segments;
  }

  function get_params()
  {
    return $this->_params;
  }

  function _parse_uri()
  {
    if (!empty($this->_segments) && $this->_segments[0] ==='')
    {
      array_shift($this->_segments);
    }

    foreach ($this->_segments as $k=>$segment) # populate params array
    {
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



