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

class validatorTest extends PHPUnit_Framework_TestCase {

            
    
    public function testChain() {
        
        $rules = array(
            'fieldname' => 'something'
        );
        $actual = Formbuilder\Validator::explodeRules($rules);
/*
        $expected = array(
            'fieldname' => array(
                array('something',array())
            ),
        );
*/
        $this->assertTrue(isset($actual['fieldname']));  
        //$this->assertEquals($expected, $actual);

        //$this->assertEquals(trim_html($expected), trim_html($actual));
    }
    
}
