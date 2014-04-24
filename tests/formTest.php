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

        
    public function testParse() {

        $tpl = 'Hello [+person+]';
        $args = array('person' => 'Milo');
        $actual = Formbuilder\Form::defaultParse($tpl,$args);  
        $expected = 'Hello Milo';
        $this->assertEquals($actual,$expected);

        // Test that unused placeholders are removed
        $tpl = 'Hello [+person+] [+unused+]';
        $args = array('person' => 'Milo');
        $actual = Formbuilder\Form::defaultParse($tpl,$args);  
        $expected = 'Hello Milo';
        $this->assertEquals($actual,$expected);        

        // Alternate delineators
        $tpl = 'Hello {{person}}';
        $args = array('person' => 'Milo');
        $actual = Formbuilder\Form::defaultParse($tpl,$args,'{{','}}');  
        $expected = 'Hello Milo';
        $this->assertEquals($actual,$expected);        


//        $actual = Formbuilder\Form::defaultParse($tpl,$args=array(),$start='[+',$end='+]');        
    }

    /**
     * Testing using a php parsing function
     *
     */    
    public function testAlternateParser() {

        Formbuilder\Form::setParser('my_custom_parser');
        $tpl = 'test.php';
        $args = array('person' => 'Milo');
        $actual = Formbuilder\Form::parse($tpl,$args);  
        $expected = 'Hello Milo';
        $this->assertEquals($actual,$expected);    
    
    }
    
}