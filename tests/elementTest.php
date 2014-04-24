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

        
    public function testText() {

        $actual = Formbuilder\Form::text('test');  
        $expected = '<input type="text" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
        $actual = Formbuilder\Form::text('test','gnarrrr');  
        $expected = '<input type="text" name="test" id="test" value="gnarrrr" />';
        $this->assertEquals($actual, $expected);

        $args = array();
        $args['extra'] = 'onclick="alert(\'something...\');"';
        $actual = Formbuilder\Form::text('test','sample',$args);  
        $expected = '<input type="text" name="test" id="test" value="sample" onclick="alert(\'something...\');"/>';
        $this->assertEquals($actual, $expected);
        
    }
    

    public function testTextarea() {

        $actual = Formbuilder\Form::textarea('test');  
        $expected = '<textarea name="test" id="test" rows="4" cols="40" ></textarea>';
        $this->assertEquals($actual, $expected);
        
        $actual = Formbuilder\Form::textarea('test','gnarrrr');  
        $expected = '<textarea name="test" id="test" rows="4" cols="40" >gnarrrr</textarea>';
        $this->assertEquals($actual, $expected);

        $args = array();
        $args['rows'] = 5;
        $args['cols'] = 15;
        $actual = Formbuilder\Form::textarea('test','gnarrrr',$args);  
        $expected = '<textarea name="test" id="test" rows="5" cols="15" >gnarrrr</textarea>';
        $this->assertEquals($actual, $expected);
        
    }


    public function testCheckbox() {

        // Unchecked
        $actual = Formbuilder\Form::checkbox('test');  
        $expected = '<input type="hidden" name="test" value="0"/><input type="hidden" name="test" id="test" value="1" />';
        $this->assertEquals($actual, $expected);

        // Checked 
        $actual = Formbuilder\Form::checkbox('test',1);  
        $expected = '<input type="hidden" name="test" value="0"/><input type="hidden" name="test" id="test" value="1"  checked="checked"/>';
        $this->assertEquals($actual, $expected);
        
        // on|off instead of 1|0
/*
        $actual = Formbuilder\Form::textarea('test','gnarrrr');  
        $expected = '<textarea name="test" id="test" rows="4" cols="40" >gnarrrr</textarea>';
        $this->assertEquals($actual, $expected);

        $args = array();
        $args['rows'] = 5;
        $args['cols'] = 15;
        $actual = Formbuilder\Form::textarea('test','gnarrrr',$args);  
        $expected = '<textarea name="test" id="test" rows="5" cols="15" >gnarrrr</textarea>';
        $this->assertEquals($actual, $expected);
*/
        
    }    
    
    
}