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

class formTest extends PHPUnit_Framework_TestCase {

        
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

        // Alternate placeholder glyphs
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
    
    
    public function testChain() {
        // Need to reset this after setParser is called
        Formbuilder\Form::setParser('\\Formbuilder\\Form::defaultParse');
        $actual = Formbuilder\Form::open()->text('test')->close();    
        $expected = '<form action="" method="post" class="" id="" ><input type="text" name="test" id="test" value="" class="" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));

        //$this->assertEquals(trim_html($expected), trim_html($actual));
    }

    public function testAction() {
        // Need to reset this after setParser is called
        Formbuilder\Form::setParser('\\Formbuilder\\Form::defaultParse');
        $actual = Formbuilder\Form::open('http://somewhere.com/page/x/y?z=123')->text('test')->close();    
        $expected = '<form action="http://somewhere.com/page/x/y?z=123" method="post" class="" id="" ><input type="text" name="test" id="test" value="" class="" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));

        //$this->assertEquals(trim_html($expected), trim_html($actual));
    }
    
}
