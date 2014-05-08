<?php
/**
 *
 * To run these tests, pass the test directory as the 1st argument to phpunit:
 *
 *   phpunit path/to/tests
 *
 * or if you're having any trouble running phpunit, download its .phar file, and 
 * then run the tests like this:
 *
 *  php phpunit.phar path/to/tests
 *
 * To run just the tests in this file, specify the file:
 *
 *  phpunit tests/autoloadTest.php
 *
 */

class autoloadTest extends PHPUnit_Framework_TestCase {

        
    public function testLoad() {
        $F = new Formbuilder\Form();
        $this->assertTrue(is_object($F), 'Form.'); 

/*
        $E = new Formbuilder\Element();
        $this->assertTrue(is_object($F), 'Element.'); 
*/
    
//        $T = new Formbuilder\Element\Text();       
//        $this->assertTrue(is_object($F), 'Text.');

        $x = 'something|else';
        $this->assertFalse(is_callable($x));
        
        $x = function ($val) { return $val; };
        $this->assertTrue(is_callable($x));
        
        $val = call_user_func_array($x, array('hello'));
        $this->assertEquals('hello',$val);
        
    }
}