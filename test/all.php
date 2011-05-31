<?php

require_once('../../simpletest/unit_tester.php');
require_once('../../simpletest/web_tester.php');
require_once('../../simpletest/reporter.php');
require_once('../functions.php');

class FunctionsTest extends UnitTestCase {

  function setUp()
  {
  }

  function testStringsFunctions()
  {
    $this->assertEqual('eblouissant', sluggize("éblouissant"));
    $this->assertEqual("èéîï", strtolower_utf8('ÈÉÎÏ'));

  }
}

$test = new FunctionsTest();
$test->run(new HtmlReporter('utf-8'));
