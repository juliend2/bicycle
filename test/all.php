<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/web_tester.php');
require_once('simpletest/reporter.php');
define('BASE_URL', 'http://localhost');
require_once('../bicycle.php');

$schema = array(
  'id'=> array(
    'data_type'=>'integer'
  ),
  'email'=> array(
    'human_name'=>'Email',
    'type'=>'string',
    'rules'=>array('not_empty'),
  ),
  'password'=> array(
    'human_name'=>'Password',
    'type'=>'string',
    'rules'=>array('not_empty'),
  ),
  'content'=> array(
    'human_name'=>'Content',
    'type'=>'text',
    'rules'=>array('not_empty'),
  ),
  'country'=> array(
    'human_name'=>'Country',
    'type'=>'select',
    'rules'=>array('not_empty'),
  )
);


class DispatcherTest extends UnitTestCase {

  function testBasicUsage() {
    $dispatcher = new Dispatcher('/fr/section/subsection/param1:value1/param2:value2');
    $segments = $dispatcher->get_segments();
    $params = $dispatcher->get_params();
    // test number of elements in arrays:
    $this->assertEqual(3, count($segments));
    $this->assertEqual(2, count($params));
    // test params
    $this->assertEqual('value1', $params['param1']);
    $this->assertEqual('value2', $params['param2']);
    // test segments
    $this->assertEqual('fr', $segments[0]);
    $this->assertEqual('section', $segments[1]);
    $this->assertEqual('subsection', $segments[2]);
  }

  function testWithLocale() {
    $dispatcher = new Dispatcher('/fr/section/subsection/param1:value1/param2:value2', true);
    $locale = $dispatcher->get_locale();
    $segments = $dispatcher->get_segments();
    $params = $dispatcher->get_params();
    // test number of elements in arrays:
    $this->assertEqual(2, count($segments));
    $this->assertEqual(2, count($params));
    // test params
    $this->assertEqual('value1', $params['param1']);
    $this->assertEqual('value2', $params['param2']);
    // test locale
    $this->assertEqual('fr', $locale);
    // test segments
    $this->assertEqual('section', $segments[0]);
    $this->assertEqual('subsection', $segments[1]);
  }

  function testEmptyURI() {
    $dispatcher = new Dispatcher('/');
    $locale = $dispatcher->get_locale();
    $segments = $dispatcher->get_segments();
    $params = $dispatcher->get_params();
    // test number of elements in arrays:
    $this->assertEqual(0, count($segments));
    $this->assertEqual(0, count($params));

    $dispatcher = new Dispatcher('', true);
    $locale = $dispatcher->get_locale();
    $segments = $dispatcher->get_segments();
    $params = $dispatcher->get_params();
    // test number of elements in arrays:
    $this->assertEqual(0, count($segments));
    $this->assertEqual(0, count($params));

    // test locale
    $this->assertEqual('', $locale);
    $dispatcher = new Dispatcher('/', true);
    $locale = $dispatcher->get_locale();
    $segments = $dispatcher->get_segments();
    $params = $dispatcher->get_params();
    // test number of elements in arrays:
    $this->assertEqual(0, count($segments));
    $this->assertEqual(0, count($params));
    // test locale
    $this->assertEqual('', $locale);
  }

}
/*
class FormHelperTest extends UnitTestCase {

  function setUp() {
    global $schema;
    $this->model = new Model(new ezSQL_mysql('root', '', '', 'localhost'), 'tablename', $schema);
  }

  function testFormHelper() {
    $form = new FormHelper($this->model, $this->model->get_db());
    // form
    $this->assertEqual('<form action="page.php" method="POST" >', $form->form_tag('page.php'));
    $this->assertEqual('<form action="page.php" method="GET" >', $form->form_tag('page.php', 'GET'));
    $this->assertEqual('<form action="page.php" method="GET"  id="form">', $form->form_tag('page.php', 'GET', array('id'=>'form')));
    // label
    $this->assertEqual('<label for="email_input" >Email</label>', $form->label('Email', 'email'));
    $this->assertEqual('<label for="email_input"  id="email_label">Email</label>', $form->label('Email', 'email', array('id'=>'email_label')));
    // basic input
    $this->assertEqual('<p  id="email_input_container" class=""><label for="email_input" >Email</label><input type="text" id="email_input" name="email" value=""  class="form_input"/></p>',
                      $form->basic_input('email', 'Email', 'text', '', array('class'=>'form_input')));
    // text area
    $this->assertEqual('<p  id="content_input_container" class=""><label for="content_input" >Content</label><textarea id="content_input" name="content"  class="input">Joie</textarea></p>',
                      $form->text_area('content', 'Content', 'Joie', array('class'=>'input')));
    // option tag
    $this->assertEqual('<option  value="cle">Valeur</option>', $form->option_tag('cle', 'Valeur'));
    // select
    $this->assertEqual('<p  id="country_input_container" class=""><label for="country_input" >Country</label><select id="country_input" name="country" ></select></p>', $form->select_input('country', 'Country'));
    // end form
    $this->assertEqual('<p  id="envoyer_submit_button"><input type="submit" value="Envoyer" /></p></form>', $form->end_form('Envoyer'));
    // exception
    $this->expectError(new PatternExpectation('/No field with this name/'));
    $form->text_area('innexistant', 'Innexistant', 'Fail');
  }
}
*/
class HelpersTest extends UnitTestCase {

  function testGeneralHelpers() {
    // url_for
    $this->assertEqual('http://localhost/joie', url_for('/joie'));
    $this->assertEqual('http://localhost/fr/joie?key=value', url_for('/fr/joie?key=value'));
    $this->assertEqual('http://localhost/fr/joie?key=value&color=yellow', url_for('/fr/joie?key=value&color=yellow'));
    // attr_to_string
    $this->assertEqual(' href="joie.html" title="Joie"', attr_to_string(array('href'=>'joie.html','title'=>'Joie')));
  }

  function testHTMLHelpers() {
    $this->assertEqual('<a href="http://localhost/joie.html" >Joie</a>', link_to('Joie', '/joie.html'));
    $this->assertEqual('<a href="http://localhost/joie.html?k=value"  title="Joy">Joie</a>', link_to('Joie', '/joie.html?k=value', array('title'=>'Joy')));
    $this->assertEqual('<p >Joie!</p>', p('Joie!'));
    $this->assertEqual('<p  title="joie">Joie!</p>', p('Joie!', array('title'=>'joie')));
  }
}

class FunctionsTest extends UnitTestCase {

  function testStringsFunctions() {
    $this->assertEqual('eblouissant', sluggize("éblouissant"));
    $this->assertEqual("èéîï", strtolower_utf8('ÈÉÎÏ'));
    $this->assertEqual('lastvalue', _or('', array(), null, 'lastvalue'));
    $this->assertEqual('joy', _or('', 'joy', array()));
    $this->assertEqual('first', _or('first', 'joy', array()));
  }
}

class ModelTest extends UnitTestCase {

  function setUp() {
    global $schema;
    $this->model = new Model(new ezSQL_mysql('root', '', ''/*TODO: create table for tests*/, 'localhost'), 'tablename', $schema);
  }

  function testInsert() {
    $this->assertEqual("INSERT INTO users (email, password) VALUES ('dude@mail.com', 'myp4ss')", $this->model->insert('users', array(
      'email'=>'dude@mail.com',
      'password'=>'myp4ss'
    )));
    $current_timestamp = date('Y-m-d H:i:s');
    $this->assertEqual("INSERT INTO users (email, password, created_at, updated_at) VALUES ('dude@mail.com', 'myp4ss', '".$current_timestamp."', '".$current_timestamp."')", $this->model->insert('users', array(
      'email'=>'dude@mail.com',
      'password'=>'myp4ss'
    ),
    array('timestamps'=>true)));
  }

  function testUpdate() {
    $this->assertEqual("UPDATE users SET email='julien@mail.com', name='julien' WHERE id=1", $this->model->update('users', 
      array( // DATA
        'email'=>'julien@mail.com',
        'name'=>'julien'
      ),
      array( // CONDITIONS
        'id'=>'1'
      ))
    );
    // timestamps
    $current_timestamp = date('Y-m-d H:i:s');
    $this->assertEqual("UPDATE users SET email='julien@mail.com', name='julien', updated_at='".$current_timestamp."' WHERE id=1", $this->model->update('users', 
      array( // DATA
        'email'=>'julien@mail.com',
        'name'=>'julien'
      ),
      array( // CONDITIONS
        'id'=>'1'
      ),
      array('timestamps'=>true))
    );
    // multiple conditions
    $this->assertEqual("UPDATE users SET email='julien@mail.com', name='julien' WHERE id=1 AND email='julz@mail.com'", $this->model->update('users', 
      array( // DATA
        'email'=>'julien@mail.com',
        'name'=>'julien'
      ),
      array( // CONDITIONS
        'id'=>'1',
        'email'=>'julz@mail.com'
      ))
    );
    // NON-equal conditions
    $this->assertEqual("UPDATE users SET email='julien@mail.com', name='julien' WHERE email IS NULL AND created_at > 2011-05-31 12:00:00", $this->model->update('users', 
      array( // DATA
        'email'=>'julien@mail.com',
        'name'=>'julien'
      ),
      array( // CONDITIONS
        'email IS NULL',
        'created_at > 2011-05-31 12:00:00'
      ))
    );
  }

  function testDelete() {
    $this->assertEqual("DELETE FROM users WHERE id=3", $this->model->delete_from('users', array('id'=>3)));
  }
}

class MigrationTest extends UnitTestCase {

  function setUp() {
    global $schema;
    $this->db = new ezSQL_mysql('root', '', 'bicycle_tests', 'localhost');
    // remove all previously created tables
    $this->db->query("DROP TABLE IF EXISTS pages");
    $this->db->query("DROP TABLE IF EXISTS branches");
  }

  function testMigrate() {
    $migrator = new Migrator($this->db, "./db");
    $migration = './db/migrations/20111022160601_create_pages.php';
    $migrator->migrate($migration, basename($migration, '.php'));
    // table created
    $tables = $this->db->get_results("SHOW TABLES");
    $tables = array_map(f('$o','return $o->Tables_in_bicycle_tests;'),$tables);
    $this->assertTrue(in_array('pages', $tables));
    // with the right fields 
    $desc = $this->db->get_results("DESC pages");
    $this->assertEqual(
      array('id','title','slug','content','created_at','updated_at'), 
      array_map(f('$o', 'return $o->Field;'), $desc)
    );
  }

  function testMigrateAll() {
    $migrator = new Migrator($this->db, "./db");
    $migrator->migrate_all();
    // test that the branches was created
    $tables = $this->db->get_results("SHOW TABLES");
    $tables = array_map(f('$o','return $o->Tables_in_bicycle_tests;'),$tables);
    $this->assertTrue(in_array('branches', $tables));
    // test that these fields were added
    $desc = $this->db->get_results("DESC branches");
    $this->assertEqual(
      array('id','title_fr','slug_fr','content_fr','created_at','updated_at',
      'title_en','slug_en','content_en'), 
      array_map(f('$o', 'return $o->Field;'), $desc)
    );
  }

}

$test = new DispatcherTest();
$test->run(new HtmlReporter('utf-8'));

// $test = new FormHelperTest();
// $test->run(new HtmlReporter('utf-8'));

$test = new MigrationTest();
$test->run(new HtmlReporter('utf-8'));

$test = new HelpersTest();
$test->run(new HtmlReporter('utf-8'));

$test = new FunctionsTest();
$test->run(new HtmlReporter('utf-8'));

$test = new ModelTest();
$test->run(new HtmlReporter('utf-8'));
