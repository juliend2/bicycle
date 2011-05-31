<?php

require_once('../../simpletest/unit_tester.php');
require_once('../../simpletest/web_tester.php');
require_once('../../simpletest/reporter.php');
require_once('../functions.php');
define('BASE_URL', 'http://localhost');
require_once('../helpers.php');
// model's deps:
require_once('ez_sql/shared/ez_sql_core.php');
require_once('ez_sql/mysql/ez_sql_mysql.php');
require_once('julienphpformvalidation/validator.php');
// end model's deps
require_once('../model.php');

class HelpersTest extends UnitTestCase {

  function testGeneralHelpers()
  {
    // url_for
    $this->assertEqual('http://localhost/joie', url_for('/joie'));
    $this->assertEqual('http://localhost/fr/joie?key=value', url_for('/fr/joie?key=value'));
    $this->assertEqual('http://localhost/fr/joie?key=value&color=yellow', url_for('/fr/joie?key=value&color=yellow'));
    // attr_to_string
    $this->assertEqual(' href="joie.html" title="Joie"', attr_to_string(array('href'=>'joie.html','title'=>'Joie')));
  }

  function testHTMLHelpers()
  {
    $this->assertEqual('<a href="http://localhost/joie.html" >Joie</a>', link_to('Joie', '/joie.html'));
    $this->assertEqual('<a href="http://localhost/joie.html?k=value"  title="Joy">Joie</a>', link_to('Joie', '/joie.html?k=value', array('title'=>'Joy')));
    $this->assertEqual('<p >Joie!</p>', p('Joie!'));
    $this->assertEqual('<p  title="joie">Joie!</p>', p('Joie!', array('title'=>'joie')));
  }

  function testFormHelpers()
  {
    // form
    $this->assertEqual('<form action="page.php" method="POST" >', form_tag('page.php'));
    $this->assertEqual('<form action="page.php" method="GET" >', form_tag('page.php', 'GET'));
    $this->assertEqual('<form action="page.php" method="GET"  id="form">', form_tag('page.php', 'GET', array('id'=>'form')));
    // label
    $this->assertEqual('<label for="email_input" >Email</label>', label('Email', 'email'));
    $this->assertEqual('<label for="email_input"  id="email_label">Email</label>', label('Email', 'email', array('id'=>'email_label')));
    // basic input
    $this->assertEqual('<p  id="email_input_container"><label for="email_input" >Email</label><input type="text" id="email_input" name="email" value=""  class="form_input"/></p>',
                      basic_input('email', 'Email', 'text', '', array('class'=>'form_input')));
    // text area
    $this->assertEqual('<p  id="content_input_container"><label for="content_input" >Content</label><textarea id="content_input" name="content"  class="input">Joie</textarea></p>',
                      text_area('content', 'Content', 'Joie', array('class'=>'input')));
    // option tag
    $this->assertEqual('<option  value="cle">Valeur</option>', option_tag('cle', 'Valeur'));
    // select
    $this->assertEqual('<p  id="country_input_container"><label for="country_input" >Country</label><select id="country_input" name="country" ></select></p>', select_input('country', 'Country'));
    // end form
    $this->assertEqual('<p  id="envoyer_submit_button"><input type="submit" value="Envoyer" /></p></form>', end_form('Envoyer'));
  }
}

class FunctionsTest extends UnitTestCase {

  function testStringsFunctions()
  {
    $this->assertEqual('eblouissant', sluggize("éblouissant"));
    $this->assertEqual("èéîï", strtolower_utf8('ÈÉÎÏ'));
  }
}

class ModelTest extends UnitTestCase {

  function setUp()
  {
    $this->model = new Model(new ezSQL_mysql('root', 'root', ''/*TODO: create table for tests*/, 'localhost'), array(
        'email'=> array(
          'human_name'=>'Email',
          'type'=>'string',
          'rules'=>array('not_empty'),
          'data_type'=>'string'
        ),
        'password'=> array(
          'human_name'=>'Password',
          'type'=>'string',
          'rules'=>array('not_empty'),
          'data_type'=>'string'
        )
      )
    );
  }

  function testInsert()
  {
    $this->assertEqual("INSERT INTO users (email, password) VALUES ('dude@mail.com', 'myp4ss')", $this->model->insert_into('users', array(
      'email'=>'dude@mail.com',
      'password'=>'myp4ss'
    )));
  }
}

$test = new HelpersTest();
$test->run(new HtmlReporter('utf-8'));

$test = new FunctionsTest();
$test->run(new HtmlReporter('utf-8'));

$test = new ModelTest();
$test->run(new HtmlReporter('utf-8'));

