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

            
    
    public function testExplodeRules() {
        
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
//        print_r($actual); exit;
        $this->assertTrue(isset($actual['fieldname']));  
        $this->assertTrue(is_array($actual['fieldname']));  
        $this->assertTrue($actual['fieldname'][0][0]=='something');  
        $this->assertTrue(is_array($actual['fieldname'][0][1]));  
        //$this->assertEquals($expected, $actual);

        //$this->assertEquals(trim_html($expected), trim_html($actual));
    }
    
}
