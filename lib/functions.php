<?php

// Get configuration values by key
function config($key) {
  global $config;
  if (isset($config[$key])) {
    return $config[$key];
  } else {
    trigger_error("Config Error: $key not found in \$config.", E_USER_ERROR);
  }
}


// the strtolower version to support most amount of languages including russian, french and so on: 
// (thanks to leha_grobov on php.net)
function strtolower_utf8($string){
  $convert_to = array(
    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u",
    "v", "w", "x", "y", "z", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï",
    "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ø", "ù", "ú", "û", "ü", "ý", "а", "б", "в", "г", "д", "е", "ё", "ж",
    "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы",
    "ь", "э", "ю", "я"
  );
  $convert_from = array(
    "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U",
    "V", "W", "X", "Y", "Z", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï",
    "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж",
    "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ъ",
    "Ь", "Э", "Ю", "Я"
  );
  return str_replace($convert_from, $convert_to, $string);
}

/**
 * Returns a string with all spaces converted to underscores (by default), accented
 * characters converted to non-accented characters, and non word characters removed.
 *
 * @param string $string
 * @param string $replacement
 * @return string
 * @access public
 * @static
 * @link http://book.cakephp.org/view/572/Class-methods
 */
function sluggize($string, $replacement = '_') {
  $map = array(
    '/à|á|å|â/' => 'a',
    '/è|é|ê|ẽ|ë/' => 'e',
    '/ì|í|î/' => 'i',
    '/ò|ó|ô|ø/' => 'o',
    '/ù|ú|ů|û/' => 'u',
    '/ç/' => 'c',
    '/ñ/' => 'n',
    '/ä|æ/' => 'ae',
    '/ö/' => 'oe',
    '/ü/' => 'ue',
    '/Ä/' => 'Ae',
    '/Ü/' => 'Ue',
    '/Ö/' => 'Oe',
    '/ß/' => 'ss',
    '/[^\w\s]/' => ' ',
    '/\\s+/' => $replacement,
  );
  
  return preg_replace(array_keys($map), array_values($map), strtolower_utf8($string));
}


function glob_templates($mask) {
  $file_names = array();
  foreach(glob($mask) as $file) {
    $content = file_get_contents($file);
    // get the template's name
    preg_match('/<?'.'php\s*\/\/\s*Template\s*:\s*([^\n]+)/', $content, $match);
    if (!empty($match[1])) {
      $file_names[basename($file)] = $match[1]; // add it to the file_names
    }
  }
  return $file_names;
}

// @return String: value from hash
// @params:
//  $hash Array: hash from which we get the value
//  $key String: key to get the value
//  $key_or_val String: what do we want from this hash? (value or key) 
function get_key_or_val($hash, $key, $key_or_val='value') {
  if ($key_or_val==='key') { $hash = array_flip($hash); }
  if (array_key_exists($key, $hash)) {
    return $hash[$key];
  }
  else
  {
    return null;
  }
}

function throw_error($error_string) {
  trigger_error($error_string, E_USER_ERROR);
}

function _or() {
  $arguments = func_get_args();
  $nb_args = func_num_args();
  for ($i = 0; $i < $nb_args; $i++) {
    if (!empty($arguments[$i]) || $i === $nb_args-1) {
      return $arguments[$i];
    }
  }
}

// A bit of functional programming goodness...

function f($args_string, $func_string) {
  return create_function($args_string, $func_string);
}

function map($array, $callback) {
  return array_map($callback, $array);
}

function filter($array, $callback) {
  return array_filter($array, $callback);
}

// @return Array: array that contains the properties values 
// @params:
//  $array Array: array that contains objects 
//  $property String: name of the properties to get from each object
//  $has_hashes Boolean: true if the array contains associative arrays
function pluck($array, $property, $has_hashes = false) {
  $f_body = $has_hashes ? 
              'return $o["'.$property.'"];' : 
              'return $o->'.$property.';';
  return map($array, f('$o', $f_body));
}

