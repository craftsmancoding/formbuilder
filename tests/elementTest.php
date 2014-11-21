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
        $expected = '<input type="hidden" name="test" value="0"/> <input type="checkbox" name="test" id="test" value="1" class="checkbox" style="" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));

        // Checked 
        $actual = Formbuilder\Form::checkbox('test',1);  
        $expected = '<input type="hidden" name="test" value="0"/> <input type="checkbox" name="test" id="test" value="1"  class="checkbox" checked="checked"/>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        // on|off instead of 1|0
        $actual = Formbuilder\Form::checkbox('test','on',array('checked_value'=>'on','unchecked_value'=>'off'));  
        $expected = '<input type="hidden" name="test" value="off"/> <input type="checkbox" name="test" id="test" value="on"  class="checkbox" checked="checked"/>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }  





    public function testDatalist() {
        $actual = Formbuilder\Form::datalist('test',array('x','y','z'));  
        $expected = '<input list="test" name="test" value="" id="test" class="datalist"><datalist id="test"><option value="x"><option value="y"><option value="z"></datalist>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
    }
            
    public function testDropdown() {

        // Simple dropdown
        $options = array('x','y','z');
        $actual = Formbuilder\Form::dropdown('test',$options);  
        $expected = '<select name="test" id="test" class="dropdown" style=""><option value="x">x</option><option value="y">y</option><option value="z">z</option></select>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        // key/value 
        $options = array('x'=>'xRay','y'=>'Yellow','z'=>'Zebra');
        $actual = Formbuilder\Form::dropdown('test',$options);  
        $expected = '<select name="test" id="test" ><option value="x">xRay</option><option value="y">Yellow</option><option value="z">Zebra</option></select>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        // Option Groups with simple options
        $options = array(
            'Dogs' => array('Husky','Labrador'),
            'Cats' => array('Maine Coon'),
        );
        $actual = Formbuilder\Form::dropdown('test',$options);  
        $expected = '<select name="test" id="test" ><optgroup label="Dogs"><option value="Husky">Husky</option><option value="Labrador">Labrador</option></optgroup><optgroup label="Cats"><option value="Maine Coon">Maine Coon</option></optgroup></select>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
    }

    public function testFile() {

        $actual = Formbuilder\Form::file('test');  
        $expected = '<input type="file" name="test" id="test" value="" class="file"  style=""/>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    } 

    public function testHidden() {

        $actual = Formbuilder\Form::hidden('test');  
        $expected = '<input type="hidden" name="test" id="test" value="" class="hidden" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }

    public function testKeygen() {

        $actual = Formbuilder\Form::keygen('test');  
        $expected = '<keygen name="test" id="test" >';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    } 


    public function testMulticheck() {

        // Simple dropdown
        $options = array('x','y','z');
        $actual = Formbuilder\Form::multicheck('test',$options);  
        $expected = '<input type="checkbox" name="test[]" id="test0" value="x" class="multicheck" /> x<br/><input type="checkbox" name="test[]" id="test1" value="y" class="multicheck" /> y<br/><input type="checkbox" name="test[]" id="test2" value="z" class="multicheck" /> z<br/>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        // key/value 
        $options = array('x'=>'xRay','y'=>'Yellow','z'=>'Zebra');
        $actual = Formbuilder\Form::multicheck('test',$options);  
        $expected = '<input type="checkbox" name="test[]" id="test0" value="x" class="multicheck" /> xRay<br/><input type="checkbox" name="test[]" id="test1" value="y" class="multicheck" /> Yellow<br/><input type="checkbox" name="test[]" id="test2" value="z" class="multicheck" /> Zebra<br/>';
        $this->assertEquals(trim_html($expected), trim_html($actual));


        // key/value with preselected values
        $options = array('x'=>'xRay','y'=>'Yellow','z'=>'Zebra');
        $actual = Formbuilder\Form::multicheck('test',$options,array('x','z'));  
        $expected = '<input type="checkbox" name="test[]" id="test0" value="x" class="multicheck" checked="checked" /> xRay<br/><input type="checkbox" name="test[]" id="test1" value="y" class="multicheck" /> Yellow<br/><input type="checkbox" name="test[]" id="test2" value="z" class="multicheck" checked="checked" /> Zebra<br/>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        // Fieldsets with simple options
        $options = array(
            'Dogs' => array('Husky','Labrador'),
            'Cats' => array('Maine Coon'),
        );
        $actual = Formbuilder\Form::multicheck('test',$options);  
        $expected = '<fieldset><legend>Dogs</legend><input type="checkbox" name="test[]" id="test0" value="Husky" class="multicheck" /> Husky<br/><input type="checkbox" name="test[]" id="test1" value="Labrador" class="multicheck" /> Labrador<br/></fieldset><fieldset><legend>Cats</legend><input type="checkbox" name="test[]" id="test2" value="Maine Coon" class="multicheck" /> Maine Coon<br/></fieldset>';
        $this->assertEquals(trim_html($expected), trim_html($actual));

        // Fieldsets with complex options
        $options = array(
            'Dogs' => array('husky'=>'Husky','lab'=>'Labrador'),
            'Cats' => array('maine'=>'Maine Coon'),
        );
        $actual = Formbuilder\Form::multicheck('test',$options);  
        $expected = '<fieldset><legend>Dogs</legend><input type="checkbox" name="test[]" id="test0" value="husky" class="multicheck" /> Husky<br/><input type="checkbox" name="test[]" id="test1" value="lab" class="multicheck" /> Labrador<br/></fieldset><fieldset><legend>Cats</legend><input type="checkbox" name="test[]" id="test2" value="maine" class="multicheck" /> Maine Coon<br/></fieldset>';
        $this->assertEquals(trim_html($expected), trim_html($actual));

    }
 
            
    public function testMultiselect() {

        // Simple dropdown
        $options = array('x','y','z');
        $actual = Formbuilder\Form::multiselect('test',$options);  
        $expected = '<select name="test[]" id="test" multiple="multiple" ><option value="x">x</option><option value="y">y</option><option value="z">z</option></select>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        // key/value 
        $options = array('x'=>'xRay','y'=>'Yellow','z'=>'Zebra');
        $actual = Formbuilder\Form::multiselect('test',$options);  
        $expected = '<select name="test[]" id="test" multiple="multiple" ><option value="x">xRay</option><option value="y">Yellow</option><option value="z">Zebra</option></select>';
        $this->assertEquals(trim_html($expected), trim_html($actual));

        // key/value with preselected values
        $options = array('x'=>'xRay','y'=>'Yellow','z'=>'Zebra');
        $actual = Formbuilder\Form::multiselect('test',$options,array('x','z'));  
        $expected = '<select name="test[]" id="test" multiple="multiple" ><option value="x" selected="selected">xRay</option><option value="y">Yellow</option><option value="z" selected="selected">Zebra</option></select>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        // Option Groups with simple options
        $options = array(
            'Dogs' => array('Husky','Labrador'),
            'Cats' => array('Maine Coon'),
        );
        $actual = Formbuilder\Form::multiselect('test',$options);  
        $expected = '<select name="test[]" id="test" multiple="multiple" ><optgroup label="Dogs"><option value="Husky">Husky</option><option value="Labrador">Labrador</option></optgroup><optgroup label="Cats"><option value="Maine Coon">Maine Coon</option></optgroup></select>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
    }

    public function testOutput() {

        $actual = Formbuilder\Form::output('test',array('x','y'));  
        $expected = '<output name="test" id="test" for="x y" ></output>';
        $this->assertEquals(trim_html($expected), trim_html($actual));

        $actual = Formbuilder\Form::output('test',array('x','y'),123);  
        $expected = '<output name="test" id="test" for="x y" >123</output>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }

    public function testPassword() {

        $actual = Formbuilder\Form::password('test');  
        $expected = '<input type="password" name="test" id="test" value="" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }

    
    public function testRadio() {

        // Simple Radio
        $options = array('x','y','z');
        $actual = Formbuilder\Form::radio('test',$options);
        $expected = '<input type="radio" name="test" id="test" value="x" class="radio"> x<br/><input type="radio" name="test" id="test" value="y" class="radio"> y<br/><input type="radio" name="test" id="test" value="z" class="radio"> z<br/>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        $options = array('x','y','z');
        $actual = Formbuilder\Form::radio('test',$options,'y');
        $expected = '<input type="radio" name="test" id="test" value="x" class="radio"> x<br/><input type="radio" name="test" id="test" value="y" class="radio" checked="checked"> y<br/><input type="radio" name="test" id="test" value="z" class="radio"> z<br/>';
        $this->assertEquals(trim_html($expected), trim_html($actual));        
        
        // key/value 
        $options = array('x'=>'xRay','y'=>'Yellow','z'=>'Zebra');
        $actual = Formbuilder\Form::radio('test',$options);  
        $expected = '<input type="radio" name="test" id="test" value="x" class="radio"> xRay<br/><input type="radio" name="test" id="test" value="y" class="radio"> Yellow<br/><input type="radio" name="test" id="test" value="z" class="radio"> Zebra<br/>';
        $this->assertEquals(trim_html($expected), trim_html($actual));        
    }

    public function testText() {

        $actual = Formbuilder\Form::text('test');  
        $expected = '<input type="text" name="test" id="test" value="" class="text" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));

        $actual = Formbuilder\Form::text('test','default ""value');  
        $expected = '<input type="text" name="test" id="test" value="default &quot;&quot;value" class="text" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));

        $actual = Formbuilder\Form::text('test','',array('label'=>'Test Field'));  
        $expected = '<label for="test" class="textlabel">Test Field</label> <input type="text" name="test" id="test" value="" class="text" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        $actual = Formbuilder\Form::text('test','',array('label'=>'Test Field','description'=>'This is only a test.'));  
        $expected = '<label for="test" class="textlabel">Test Field</label> <input type="text" name="test" id="test" value="" class="text" /> <p>This is only a test.</p>';
        $this->assertEquals(trim_html($expected), trim_html($actual));

        $actual = Formbuilder\Form::text('test','',array('label'=>'Test Field','error'=>'There was a problem.')); 
        $expected = '<label for="test" class="textlabel">Test Field</label>
            <div class="error">There was a problem.</div>
            <input type="text" name="test" id="test" value="" class="text" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }    

    public function testTextarea() {

        $actual = Formbuilder\Form::textarea('test');  
        $expected = '<textarea name="test" id="test" class="textarea" rows="4" cols="40" ></textarea>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        $actual = Formbuilder\Form::textarea('test','gnarrrr');  
        $expected = '<textarea name="test" id="test" class="textarea" rows="4" cols="40" >gnarrrr</textarea>';
        $this->assertEquals(trim_html($expected), trim_html($actual));

        $args = array();
        $args['rows'] = 5;
        $args['cols'] = 15;
        $actual = Formbuilder\Form::textarea('test','gnarrrr',$args);  
        $expected = '<textarea name="test" id="test" class="textarea" rows="5" cols="15" >gnarrrr</textarea>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }

    public function testColor() {

        $actual = Formbuilder\Form::color('test');  
        $expected = '<input type="color" name="test" id="test" value="" class="color" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }

    public function testDate() {

        $actual = Formbuilder\Form::date('test');  
        $expected = '<input type="date" name="test" id="test" value="" class="date" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }

    public function testDatetimeLocal() {

        $actual = Formbuilder\Form::datetime_local('test');  
        $expected = '<input type="datetime-local" name="test" id="test" value="" class="datetime_local" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }
    
    public function testEmail() {
        $args = array();
        $args['id'] = 'testemail';
        $actual = Formbuilder\Form::email('test','',$args);  
        $expected = '<input type="email" name="test" id="testemail" value="" class="email" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }

    public function testMonth() {
        $actual = Formbuilder\Form::month('test','',array('class'=>'something'));  
        $expected = '<input type="month" name="test" id="test" value="" class="something" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
    }

    public function testNumber() {
        $actual = Formbuilder\Form::number('test',1,100);  
        $expected = '<input type="number" name="test" id="test" min="1" max="100" value="" class="number" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
    }

    public function testSearch() {

        $actual = Formbuilder\Form::search('test');  
        $expected = '<input type="search" name="test" id="test" value="" class="search" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }

    public function testTime() {

        $actual = Formbuilder\Form::time('test');  
        $expected = '<input type="time" name="test" id="test" value="" class="time" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }

    public function testWeek() {

        $actual = Formbuilder\Form::week('test');  
        $expected = '<input type="week" name="test" id="test" value="" class="week" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }

    public function testUrl() {

        $actual = Formbuilder\Form::url('test');  
        $expected = '<input type="url" name="test" id="test" value="" class="url" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }

    public function testSubmit() {

        $actual = Formbuilder\Form::submit('test');  
        $expected = '<input type="submit" name="test" id="test" value="" class="submit" />';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
    }
    
    
    public function testHTML() {
        $actual = Formbuilder\Form::html('This is only a test.');  
        $expected = 'This is only a test.';
        $this->assertEquals(trim_html($expected), trim_html($actual));

        $actual = Formbuilder\Form::html('Hello [+somebody+]', array('somebody'=>'World','ignore'=>'Me'));  
        $expected = 'Hello World';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        
    }
}
