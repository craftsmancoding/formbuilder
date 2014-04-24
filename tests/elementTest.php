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


    public function testPassword() {

        $actual = Formbuilder\Form::password('test');  
        $expected = '<input type="password" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
    }

    public function testHidden() {

        $actual = Formbuilder\Form::hidden('test');  
        $expected = '<input type="hidden" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
    }


    public function testDatalist() {
        $actual = Formbuilder\Form::datalist('test',array('x','y','z'));  
        $expected = '<input list="test" name="test" id="test" ><datalist id="test"><option value="x"><option value="y"><option value="z"></datalist>';
        $this->assertEquals($actual, $expected);
    }
            
    public function testDropdown() {

        // Simple dropdown
        $options = array('x','y','z');
        $actual = Formbuilder\Form::dropdown('test',$options);  
        $expected = '<select name="test" id="test" ><option value="x">x</option><option value="y">y</option><option value="z">z</option></select>';
        $this->assertEquals($actual, $expected);
        
        // key/value 
        $options = array('x'=>'xRay','y'=>'Yellow','z'=>'Zebra');
        $actual = Formbuilder\Form::dropdown('test',$options);  
        $expected = '<select name="test" id="test" ><option value="x">xRay</option><option value="y">Yellow</option><option value="z">Zebra</option></select>';
        $this->assertEquals($actual, $expected);
        
        // Option Groups with simple options
        $options = array(
            'Dogs' => array('Husky','Labrador'),
            'Cats' => array('Maine Coon'),
        );
        $actual = Formbuilder\Form::dropdown('test',$options);  
        $expected = '<select name="test" id="test" ><optgroup label="Dogs"><option value="Husky">Husky</option><option value="Labrador">Labrador</option></optgroup><optgroup label="Cats"><option value="Maine Coon">Maine Coon</option></optgroup></select>';
        $this->assertEquals($actual, $expected);
    }

    public function testRadio() {

        // Simple Radio
        $options = array('x','y','z');
        $actual = Formbuilder\Form::radio('test',$options);
        $expected = '<input type="radio" name="test" id="test" value="x" > x<br/><input type="radio" name="test" id="test" value="y" > y<br/><input type="radio" name="test" id="test" value="z" > z<br/>';
        $this->assertEquals($actual, $expected);
        
        $options = array('x','y','z');
        $actual = Formbuilder\Form::radio('test',$options,'y');
        $expected = '<input type="radio" name="test" id="test" value="x" > x<br/><input type="radio" name="test" id="test" value="y" checked="checked" > y<br/><input type="radio" name="test" id="test" value="z" > z<br/>';
        $this->assertEquals($actual, $expected);        
        
        // key/value 
        $options = array('x'=>'xRay','y'=>'Yellow','z'=>'Zebra');
        $actual = Formbuilder\Form::radio('test',$options);  
        $expected = '<input type="radio" name="test" id="test" value="x" > xRay<br/><input type="radio" name="test" id="test" value="y" > Yellow<br/><input type="radio" name="test" id="test" value="z" > Zebra<br/>';
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