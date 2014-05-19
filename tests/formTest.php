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

    public static function customformelement() {
    
    }

        
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

        $actual = Formbuilder\Form::open()->text('test')->close(); 
        $expected = '<form action="" method="post" class="" id="" ><input type="text" name="test" id="test" value="" class="text" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));

        //$this->assertEquals(trim_html($expected), trim_html($actual));
    }

    public function testAction() {
        // Need to reset this after setParser is called
        Formbuilder\Form::setParser('\\Formbuilder\\Form::defaultParse');
        $actual = Formbuilder\Form::open('http://somewhere.com/page/x/y?z=123')->text('test')->close();    
        $expected = '<form action="http://somewhere.com/page/x/y?z=123" method="post" class="" id="" ><input type="text" name="test" id="test" value="" class="text" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));

        //$this->assertEquals(trim_html($expected), trim_html($actual));
    }

    public function testErrors() {
        $actual = Formbuilder\Form::open()->setErrors(array('test'=>'There is a problem'))->text('test')->close();    
        $expected = '<form action="" method="post" class="" id="" ><div class="error">There is a problem</div>
            <input type="text" name="test" id="test" value="" class="text" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));

        Formbuilder\Form::setErrors(array('test'=>'There is a problem'));

        $actual = Formbuilder\Form::open()->text('test')->close();    
        $expected = '<form action="" method="post" class="" id="" ><div class="error">There is a problem</div>
            <input type="text" name="test" id="test" value="" class="text" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));

        // Test merge
        Formbuilder\Form::setErrors(array('test'=>'There is a problem'));
        Formbuilder\Form::setErrors(array('test2'=>'There is another problem'));
        
        $actual = array('test'=>'There is a problem','test2'=>'There is another problem');
        $this->assertTrue(isset(Formbuilder\Form::$errors['test']));
        $this->assertTrue(isset(Formbuilder\Form::$errors['test2']));
        $this->assertTrue(Formbuilder\Form::$errors['test'] == 'There is a problem');
        $this->assertTrue(Formbuilder\Form::$errors['test2'] == 'There is another problem');
    }

    public function testRepopulate() {
        Formbuilder\Form::$errors = array();
        Formbuilder\Form::setValues(array('test'=>'something'));
        $actual = Formbuilder\Form::open()->text('test')->close(); 

        $expected = '<form action="" method="post" class="" id="" ><input type="text" name="test" id="test" value="something" class="text" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));

        // Make sure values override
        Formbuilder\Form::setValues(array('test'=>'something'));
        $actual = Formbuilder\Form::open()->text('test','else')->close(); 

        $expected = '<form action="" method="post" class="" id="" ><input type="text" name="test" id="test" value="something" class="text" /></form>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        // Make sure values fall through if set directly in the form tag
        Formbuilder\Form::setValues(array('test'=>'something'));
        $actual = Formbuilder\Form::open()->text('test2','else')->close(); 
        $expected = '<form action="" method="post" class="" id="" ><input type="text" name="test2" id="test2" value="else" class="text" /></form>';
        $this->assertEquals(trim_html($expected), trim_html($actual));

    }
    
    
    /**
     * Sometimes we need to generate a form completely on the fly via a single array
     */
    public function testTpls() {
/*
        $fields = array(
            'text' => array()
        );
        $actual = Formbuilder\Form::open()->fields($fields)->close(); 
*/

        $actual = Formbuilder\Form::open()
            ->setTpl('description', '<p class="description-txt">[+description+]</p>')
            ->text('NameOnCard','',array('label'=>'Name on Card','description'=>'Something'))
            ->close();
        $expected = '<form action="" method="post" class="" id="" ><label for="NameOnCard" class="textlabel">Name on Card</label>
            <input type="text" name="NameOnCard" id="NameOnCard" value="" class="text" />
            <p class="description-txt">Something</p></form>';
        $this->assertEquals(trim_html($expected), trim_html($actual));

    }
    
    
    
}
