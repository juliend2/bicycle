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
  $confirm = '';
  if (isset($attr['confirm']))
  {
    $confirm = 'onclick=\'return confirm("'.$attr['confirm'].'");\' ';
    unset($attr['confirm']);
  }
  $absolute_path = url_for($relative_path);
  return "<a href=\"{$absolute_path}\" ".$confirm."".attr_to_string($attr).">{$label}</a>";
}


function p($string, $attr=array())
{
  return '<p '.attr_to_string($attr).'>'. $string .'</p>';
}


// @return String: <tr> tag with <td> tags in it
// @params:
//  $tds Array: content for each <td> element
function tr($tds)
{
  $str = '<tr>';
  foreach ($tds as $k=>$td)
  {
    $str .= "<td>{$td}</td>";
  }
  $str .= '</tr>';
  return $str;
}

/*
 * $objects is a collection of objects
 * $fields is an array of the fields to display in the table, i.e:
 *  array(
 *    'title' => 'Title',
 *    'content' => 'Content'
 *  )
 */
function index_table($objects, $fields) {
?>
  <?php if($objects): ?>
    <table>
    <tr>
      <?php foreach($fields as $field => $fieldname): ?>
      <th><?php e($fieldname) ?></th>
      <?php endforeach ?> 
      <th></th>
      <th></th>
    </tr>
    <?php foreach($objects as $object): ?>
    <tr>
      <?php foreach($fields as $field => $fieldname): ?>
      <td><?php e($object->{$field}) ?></td>
      <?php endforeach ?>
      <td><?php e(link_to('Edit',"/admin/pages/edit.php?id={$object->id}")) ?></td>
      <td><?php e(link_to('Delete',"/admin/pages/index.php?delete={$object->id}", array('confirm'=>'Are you sure?'))) ?></td>
    </tr>
    <?php endforeach ?>
    </table>
  <?php endif ?>
<?php
  
}
