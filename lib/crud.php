<?php

// CRUD helper
class Crud {

  public static function update($posted_data, $model_object, $id, $options=array())
  {
    if (!empty($posted_data))
    {
      if ($model_object->validate($posted_data))
      {
        $safe = Model::escape_array($model_object->get_posted_data());
        $model_object->query($model_object->update($model_object->get_table_name(), $safe, array('id'=>$id), $options));
        header('Location: index.php');
      }
    }
  }

  public static function create($posted_data, $model_object, $options=array())
  {
    if (!empty($posted_data))
    {
      if ($model_object->validate($posted_data))
      {
        $safe = Model::escape_array($model_object->get_posted_data());
        $model_object->query($model_object->insert_into($model_object->get_table_name(), $safe, $options));
        header('Location: index.php');
      }
    }
  }

  public static function destroy($get, $model_object, $options=array())
  {
    if (isset($get['id']))
    {
      $model_object->query("DELETE FROM {$model_object->get_table_name()} WHERE id={$get['id']}");
      header('Location: index.php');
    }
  }
}
