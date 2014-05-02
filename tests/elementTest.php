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

class elementTest extends PHPUnit_Framework_TestCase {

    public function testCheckbox() {

        // Unchecked
        $actual = Formbuilder\Form::checkbox('test');  
        $expected = '<input type="hidden" name="test" value="0"/><input type="checkbox" name="test" id="test" value="1" />';
        $this->assertEquals($actual, $expected);

        // Checked 
        $actual = Formbuilder\Form::checkbox('test',1);  
        $expected = '<input type="hidden" name="test" value="0"/><input type="checkbox" name="test" id="test" value="1"  checked="checked"/>';
        $this->assertEquals($actual, $expected);
        
        // on|off instead of 1|0
        $actual = Formbuilder\Form::checkbox('test','on',array('checked_value'=>'on','unchecked_value'=>'off'));  
        $expected = '<input type="hidden" name="test" value="off"/><input type="checkbox" name="test" id="test" value="on"  checked="checked"/>';
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

    public function testFile() {

        $actual = Formbuilder\Form::file('test');  
        $expected = '<input type="file" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
    } 

    public function testHidden() {

        $actual = Formbuilder\Form::hidden('test');  
        $expected = '<input type="hidden" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
    }

    public function testKeygen() {

        $actual = Formbuilder\Form::keygen('test');  
        $expected = '<keygen name="test" id="test" >';
        $this->assertEquals($actual, $expected);
        
    } 


    public function testMulticheck() {

        // Simple dropdown
        $options = array('x','y','z');
        $actual = Formbuilder\Form::multicheck('test',$options);  
        $expected = '<input type="checkbox" name="test[]" id="test" value="x" /> x<br/><input type="checkbox" name="test[]" id="test" value="y" /> y<br/><input type="checkbox" name="test[]" id="test" value="z" /> z<br/>';
        $this->assertEquals($actual, $expected);
        
        // key/value 
        $options = array('x'=>'xRay','y'=>'Yellow','z'=>'Zebra');
        $actual = Formbuilder\Form::multicheck('test',$options);  
        $expected = '<input type="checkbox" name="test[]" id="test" value="x" /> xRay<br/><input type="checkbox" name="test[]" id="test" value="y" /> Yellow<br/><input type="checkbox" name="test[]" id="test" value="z" /> Zebra<br/>';
        $this->assertEquals($actual, $expected);


        // key/value with preselected values
        $options = array('x'=>'xRay','y'=>'Yellow','z'=>'Zebra');
        $actual = Formbuilder\Form::multicheck('test',$options,array('x','z'));  
        $expected = '<input type="checkbox" name="test[]" id="test" value="x" checked="checked" /> xRay<br/><input type="checkbox" name="test[]" id="test" value="y" /> Yellow<br/><input type="checkbox" name="test[]" id="test" value="z" checked="checked" /> Zebra<br/>';
        $this->assertEquals($actual, $expected);
        
        // Fieldsets with simple options
        $options = array(
            'Dogs' => array('Husky','Labrador'),
            'Cats' => array('Maine Coon'),
        );
        $actual = Formbuilder\Form::multicheck('test',$options);  
        $expected = '<fieldset><legend>Dogs</legend><input type="checkbox" name="test[]" id="test" value="Husky" /> Husky<br/><input type="checkbox" name="test[]" id="test" value="Labrador" /> Labrador<br/></fieldset><fieldset><legend>Cats</legend><input type="checkbox" name="test[]" id="test" value="Maine Coon" /> Maine Coon<br/></fieldset>';
        $this->assertEquals($actual, $expected);

        // Fieldsets with complex options
        $options = array(
            'Dogs' => array('husky'=>'Husky','lab'=>'Labrador'),
            'Cats' => array('maine'=>'Maine Coon'),
        );
        $actual = Formbuilder\Form::multicheck('test',$options);  
        $expected = '<fieldset><legend>Dogs</legend><input type="checkbox" name="test[]" id="test" value="husky" /> Husky<br/><input type="checkbox" name="test[]" id="test" value="lab" /> Labrador<br/></fieldset><fieldset><legend>Cats</legend><input type="checkbox" name="test[]" id="test" value="maine" /> Maine Coon<br/></fieldset>';
        $this->assertEquals($actual, $expected);

    }
 
            
    public function testMultiselect() {

        // Simple dropdown
        $options = array('x','y','z');
        $actual = Formbuilder\Form::multiselect('test',$options);  
        $expected = '<select name="test[]" id="test" multiple="multiple" ><option value="x">x</option><option value="y">y</option><option value="z">z</option></select>';
        $this->assertEquals($actual, $expected);
        
        // key/value 
        $options = array('x'=>'xRay','y'=>'Yellow','z'=>'Zebra');
        $actual = Formbuilder\Form::multiselect('test',$options);  
        $expected = '<select name="test[]" id="test" multiple="multiple" ><option value="x">xRay</option><option value="y">Yellow</option><option value="z">Zebra</option></select>';
        $this->assertEquals($actual, $expected);

        // key/value with preselected values
        $options = array('x'=>'xRay','y'=>'Yellow','z'=>'Zebra');
        $actual = Formbuilder\Form::multiselect('test',$options,array('x','z'));  
        $expected = '<select name="test[]" id="test" multiple="multiple" ><option value="x" selected="selected">xRay</option><option value="y">Yellow</option><option value="z" selected="selected">Zebra</option></select>';
        $this->assertEquals($actual, $expected);
        
        // Option Groups with simple options
        $options = array(
            'Dogs' => array('Husky','Labrador'),
            'Cats' => array('Maine Coon'),
        );
        $actual = Formbuilder\Form::multiselect('test',$options);  
        $expected = '<select name="test[]" id="test" multiple="multiple" ><optgroup label="Dogs"><option value="Husky">Husky</option><option value="Labrador">Labrador</option></optgroup><optgroup label="Cats"><option value="Maine Coon">Maine Coon</option></optgroup></select>';
        $this->assertEquals($actual, $expected);
    }

    public function testOutput() {

        $actual = Formbuilder\Form::output('test',array('x','y'));  
        $expected = '<output name="test" id="test" for="x y" ></output>';
        $this->assertEquals($actual, $expected);

        $actual = Formbuilder\Form::output('test',array('x','y'),123);  
        $expected = '<output name="test" id="test" for="x y" >123</output>';
        $this->assertEquals($actual, $expected);
        
    }

    public function testPassword() {

        $actual = Formbuilder\Form::password('test');  
        $expected = '<input type="password" name="test" id="test" value="" />';
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

    public function testText() {

        $actual = Formbuilder\Form::text('test');  
        $expected = '<input type="text" name="test" id="test" value="" />';
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

    public function testColor() {

        $actual = Formbuilder\Form::color('test');  
        $expected = '<input type="color" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
    }

    public function testDate() {

        $actual = Formbuilder\Form::date('test');  
        $expected = '<input type="date" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
    }

    public function testDatetimeLocal() {

        $actual = Formbuilder\Form::datetime_local('test');  
        $expected = '<input type="datetime-local" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
    }
    
    public function testEmail() {
        $args = array();
        $args['id'] = 'testemail';
        $actual = Formbuilder\Form::email('test','',$args);  
        $expected = '<input type="email" name="test" id="testemail" value="" />';
        $this->assertEquals($actual, $expected);
        
    }

    public function testMonth() {
        $actual = Formbuilder\Form::month('test','',array('extra'=>'class="something"'));  
        $expected = '<input type="month" name="test" id="test" value="" class="something"/>';
        $this->assertEquals($actual, $expected);
    }

    public function testNumber() {
        $actual = Formbuilder\Form::number('test',1,100);  
        $expected = '<input type="number" name="test" id="test" min="1" max="100" value="" />';
        $this->assertEquals($actual, $expected);
    }

    public function testSearch() {

        $actual = Formbuilder\Form::search('test');  
        $expected = '<input type="search" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
    }

    public function testTime() {

        $actual = Formbuilder\Form::time('test');  
        $expected = '<input type="time" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
    }

    public function testWeek() {

        $actual = Formbuilder\Form::week('test');  
        $expected = '<input type="week" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
    }

    public function testUrl() {

        $actual = Formbuilder\Form::url('test');  
        $expected = '<input type="url" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
    }

    public function testSubmit() {

        $actual = Formbuilder\Form::submit('test');  
        $expected = '<input type="submit" name="test" id="test" value="" />';
        $this->assertEquals($actual, $expected);
        
    }
}
