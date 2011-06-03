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

function link_to($label, $relative_path, $attr = array())
{
  $absolute_path = url_for($relative_path);
  return "<a href=\"{$absolute_path}\" ".attr_to_string($attr).">{$label}</a>";
}


function p($string, $attr=array())
{
  return '<p '.attr_to_string($attr).'>'. $string .'</p>';
}


