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
namespace Formbuilder;
class formTest extends \PHPUnit_Framework_TestCase {

    public static function customformelement($name,$default='',$args=array(),$tpl=null) {
        $out = '<BRICK></BRICK>';
        return Form::chain($out); 
    }

        
    public function testParse() {

        $tpl = 'Hello [+person+]';
        $args = array('person' => 'Milo');
        $actual = Form::defaultParse($tpl,$args);  
        $expected = 'Hello Milo';
        $this->assertEquals($actual,$expected);

        // Test that unused placeholders are removed
        $tpl = 'Hello [+person+] [+unused+]';
        $args = array('person' => 'Milo');
        $actual = Form::defaultParse($tpl,$args);  
        $expected = 'Hello Milo';
        $this->assertEquals($actual,$expected);        

        // Alternate placeholder glyphs
        $tpl = 'Hello {{person}}';
        $args = array('person' => 'Milo');
        $actual = Form::defaultParse($tpl,$args,'{{','}}');  
        $expected = 'Hello Milo';
        $this->assertEquals($actual,$expected);        


//        $actual = Form::defaultParse($tpl,$args=array(),$start='[+',$end='+]');        
    }

    /**
     * Testing using a php parsing function
     *
     */    
    public function testAlternateParser() {

        Form::setParser('my_custom_parser');
        $tpl = 'test.php';
        $args = array('person' => 'Milo');
        $actual = Form::parse($tpl,$args);  
        $expected = 'Hello Milo';
        $this->assertEquals($actual,$expected);    
    
    }
    
    
    public function testChain() {
        $actual = Form::open()->text('test')->close(); 
//        $actual = Form::open()->close(); 
        $expected = '<form action="" method="post" class="" id="" ><input type="text" name="test" id="test" value="" class="text" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));

        //$this->assertEquals(trim_html($expected), trim_html($actual));
    }

    public function testAction() {
        // Need to reset this after setParser is called
        Form::setParser('\\Formbuilder\Form::defaultParse');
        $actual = Form::open('http://somewhere.com/page/x/y?z=123')->text('test')->close();    
        $expected = '<form action="http://somewhere.com/page/x/y?z=123" method="post" class="" id="" ><input type="text" name="test" id="test" value="" class="text" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));

        //$this->assertEquals(trim_html($expected), trim_html($actual));
    }

    public function testErrors() {
        $actual = Form::open()->setErrors(array('test'=>'There is a problem'))->text('test')->close();    
        $expected = '<form action="" method="post" class="" id="" ><div class="error">There is a problem</div>
            <input type="text" name="test" id="test" value="" class="text" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));

        Form::setErrors(array('test'=>'There is a problem'));

        $actual = Form::open()->text('test')->close();    
        $expected = '<form action="" method="post" class="" id="" ><div class="error">There is a problem</div>
            <input type="text" name="test" id="test" value="" class="text" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));

        // Test merge
        Form::setErrors(array('test'=>'There is a problem'));
        Form::setErrors(array('test2'=>'There is another problem'));
        
        $actual = array('test'=>'There is a problem','test2'=>'There is another problem');
        $this->assertTrue(isset(Form::$errors['test']));
        $this->assertTrue(isset(Form::$errors['test2']));
        $this->assertTrue(Form::$errors['test'] == 'There is a problem');
        $this->assertTrue(Form::$errors['test2'] == 'There is another problem');
    }

    public function testRepopulate() {
        Form::$errors = array();
        Form::setValues(array('test'=>'something'));
        $actual = Form::open()->text('test')->close(); 

        $expected = '<form action="" method="post" class="" id="" ><input type="text" name="test" id="test" value="something" class="text" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));

        // Make sure values override
        Form::setValues(array('test'=>'something'));
        $actual = Form::open()->text('test','else')->close(); 

        $expected = '<form action="" method="post" class="" id="" ><input type="text" name="test" id="test" value="something" class="text" /></form>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        
        // Make sure values fall through if set directly in the form tag
        Form::setValues(array('test'=>'something'));
        $actual = Form::open()->text('test2','else')->close(); 
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
        $actual = Form::open()->fields($fields)->close(); 
*/

        $actual = Form::open()
            ->setTpl('description', '<p class="description-txt">[+description+]</p>')
            ->text('NameOnCard','',array('label'=>'Name on Card','description'=>'Something'))
            ->close();
        $expected = '<form action="" method="post" class="" id="" ><label for="NameOnCard" class="textlabel">Name on Card</label>
            <input type="text" name="NameOnCard" id="NameOnCard" value="" class="text" />
            <p class="description-txt">Something</p></form>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        // Reset
        Form::setTpl('description', '<p class="[+class+]">[+description+]</p>');
        
    }
    
    public function testTranslator() {
    
        Form::setTranslator(function($str){ return $str.'xxx';});
        $actual = Form::open()
            ->text('first_name','',array('label'=>'First Name','description'=>'Something'))
            ->close();
        $expected = '<form action="" method="post" class="" id="" ><label for="first_name" class="textlabel">First Namexxx</label>
            <div class="error">xxx</div>
            <input type="text" name="first_name" id="first_name" value="" class="text" />
            <p class="">Somethingxxx</p></form>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        // Reset
        Form::setTranslator('\\Formbuilder\\Form::defaultTranslator');
    }
    
    // Test overriding stuff...
    public function testCallbacks() {
        // Standalone
        Form::register('text', '\\Formbuilder\\formTest::customformelement');
        $actual = Form::text('test');
        $expected = '<BRICK></BRICK>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        Form::unregister('text');        

        // Chained
        $actual = Form::open()
            ->register('text', '\\Formbuilder\\formTest::customformelement')
            ->text('first_name','',array('label'=>'First Name','description'=>'Something'))
            ->close();
        $expected = '<form action="" method="post" class="" id="" ><BRICK></BRICK></form>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
        Form::unregister('text');
        
    }
    

    public function testStandaloneFunctions() {
        $actual = Form::open('/page.html');
        $actual .= Form::text('test', 'Test');
        $actual .= Form::submit('','Save');
        $actual .= Form::close();
        $expected = '<form action="/page.html" method="post" class="" id="" ><input type="text" name="test" id="test" value="Test" class="text" /><input type="submit" name="" id="" value="Save" class="submit" /></form>';
        $this->assertEquals(trim_html($expected), trim_html($actual));
    }


    public function testMixChainAndStandaloneFunctions() {
        Form::unregister('text');
        $actual = Form::open('/page.html')
            ->text('test', 'Test');
        $actual .= Form::submit('','Save');
        $actual .= Form::close();
        $expected = '<form action="/page.html" method="post" class="" id="" ><input type="text" name="test" id="test" value="Test" class="text" /><input type="submit" name="" id="" value="Save" class="submit" /></form>';

        $this->assertEquals(trim_html($expected), trim_html($actual));
    }

    
}
