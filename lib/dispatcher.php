<?php

function dispatch_url($uri, $is_multilingual=false) {
  $locale = '';
  $params = array();
  $param_separator = ':';
  $segments = explode('/', $uri);

  if (!empty($segments) && $segments[0] ==='') { // remove empty element
    array_shift($segments);
  }
  // remove empty element (the other side of an empty '/') :
  if (!empty($segments) && count($segments) === 1 && $segments[0] ==='') {  
    array_shift($segments);
  }
  if ($is_multilingual && 
      (isset($segments[0]) && strpos($segments[0], ':') === false)) {
    $locale = array_shift($segments);
  }

  foreach ($segments as $k=>$segment) { # populate params array
    # TODO: remember to NOT allow ":" characters on slug values during pages validation

    $found = strpos($segment, $param_separator);

    if ( $found !== false               # Found the ":" character and
      && $found !== 0                   # it's Not the first char of segment and
      && $found !== strlen($segment)-1) # it's Not the last char of the segment
    {
      $split_segment = explode($param_separator, $segment);
      $params[ $split_segment[0] ] = $split_segment[1];
      unset($segments[$k]);
    }
  }

  return array(
    'segments' => $segments,
    'params'   => $params,
    'locale'   => $locale
  );
}

