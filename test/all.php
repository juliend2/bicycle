<?php

require_once('../../simpletest/unit_tester.php');
require_once('../../simpletest/web_tester.php');
require_once('../../simpletest/reporter.php');
require_once('../functions.php');
define('BASE_URL', 'http://localhost');
require_once('../helpers.php');

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
  }
}

class FunctionsTest extends UnitTestCase {

  function testStringsFunctions()
  {
    $this->assertEqual('eblouissant', sluggize("éblouissant"));
    $this->assertEqual("èéîï", strtolower_utf8('ÈÉÎÏ'));
  }
}

$test = new HelpersTest();
$test->run(new HtmlReporter('utf-8'));

$test = new FunctionsTest();
$test->run(new HtmlReporter('utf-8'));



