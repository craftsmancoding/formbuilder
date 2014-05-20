Formbuilder
===========

In memory of my dumb friends...

I grew tired of repeating myself myself.  This library is built to generate HTML forms via a reusable library.
I got tired of Symfonic noise with no docs.  Why you no document your code?  This is inspired from the Laravel
Formbuilder library, but I needed a low-brow solution that was not dependent on an underlying framework.  

Without further ado...


## Supported Inputs

You can use this class to generate the following types of inputs.  Most of these are verbatim implementations of
the supported HTML input types, but some (like "dropdown" or "multicheck") offer convenient interfaces.

* [[checkbox]] - you can customize the values sent when the box is checked or unchecked.
* [[color]] - (HTML 5)
* [[datalist]] - a typeahead type dropdown (HTML 5)
* [[date]] - (HTML 5)
* [[datetime]] - (HTML 5)
* [[datetime_local]] - (HTML 5)
* [[dropdown]] - a select field, used to select a single option. 
* [[email]] - (HTML 5)
* [[file]] - used to choose a local file for uploading
* [[hidden]] - hidden fields
* [[keygen]] - (HTML 5)
* [[month]]  - (HTML 5)
* [[multiselect]] - a select field used to select multiple options
* [[multicheck]] - functionally the same as multiselect, but formatted as multiple checkboxes
* [[number]]  - (HTML 5)
* [[output]] - used to store calculated values (HTML 5)
* [[password]] - a standard password field
* [[radio]] - functionally equivalent to a dropdown, but uses radio options
* [[range]] - displays a slider for selecting number within a range (HTML 5)
* [[search]] - displays a search form (HTML 5)
* [[submit]] - a standard submit button
* [[text]] - the original
* [[textarea]] - a standard textarea
* [And more](custom) - you can define custom callbacks for your own field types

## Creating Form Elements

In the simplest invocation, you just need to call the function corresponding to an input type.
Each function has its own signature; some fields require different types of data, so review
the documentation for each function.

**Code:**

    <?php
    print \Formbuilder\Form::text('my_field');
    ?>

**Output:**

    <input type="text" name="my_field" id="my_field" value="" />


Just make sure you've included the autoloader:

    <?php
    use Formbuilder;
    require_once '/path/to/formbuilder/vendor/autoload.php';
    print Form::text('my_field');
    ?>


That's a bit cleaner if it works for you.


### Special Arguments

Each type of field can be passed an array of arguments.  Mostly, these will simply correspond to any placeholders 
in the field's formatting template, but there are several special arguments that trigger special behavior.  These are:

* **label**: if present, the label will get translated and formatted using the label template
* **description**: if present, the description will get translated and formatted using the description template.
* **error**: if present, the error message will get translated and formatted with the error template.
* **value** : this gets ignored!  The field value comes from either the default passed to the field (usually the 2nd argument) or via the value set via setValues();

---------------------

# Input Types

## Text

**Syntax:** `text(string $name, string $default='',array $args=array(),string $tpl=null)`

* `$name` _string_ the name of the field (required)
* `$default` _string_ the default value for the field. This will get overridden by setValues(). (optional)
* `$args` _array_ any additional arguments to pass to the field. (optional)
* `$tpl` _string_ formatting string.

### Examples

    <?php print \Formbuilder\Form::text('first_name'); ?>

Set a default value:

    <?php print \Formbuilder\Form::text('first_name', 'Bob'); ?> 

Set other parameters via the $args array:

    <?php 
    print \Formbuilder\Form::text('first_name', 'Bob', 
        array('label'=>'First Name','description'=>'Your given name','class'=>'important')
    ); 
    ?> 



## Textarea

**Syntax:** `textarea(string $name,string $default='',array $args=array(),string $tpl=null)`

Simple example:

    <?php print \Formbuilder\Form::textarea('bio'); ?>

More beefy:

    <?php print \Formbuilder\Form::textarea('saga','Tell your tale here...',array('rows'=>'60','cols'=>'80')); ?>

Textarea fields support a placeholders for "rows" and "cols".


## Checkbox

A standard checkbox will pass either a 1 or 0 value:

    <?php Form::checkbox('mycheckbox', 1); ?>

If you want to pass values other than 1 and 0, then pass "checked_value" and "unchecked_value" as arguments:

    <?php Form::checkbox('mycheckbox', 'Yes', array('checked_value'=>'Yes','unchecked_value'=>'No')); ?>


## Dropdown

A dropdown implements a `<select>` field and allows you to select a single value.

**Syntax:** `dropdown(string $name, array $options=array(), string $default='', array $args=array(), string $tpl=null)`

### Simple Options

Simple options can be supplied via a simple array:

    <?php
    print \Formbuilder\Form::dropdown('mydropdown',array('Yes','No','Maybe'));
    ?>

If you require distinct options/values, then use an associative array:

    <?php
    print \Formbuilder\Form::dropdown('mydropdown',array('y'=>'Yes','n'=>'No','m'=>'Maybe'));
    ?>

When using an associative array, the array key is what is passed as the field value and the array value is used as the option label.
E.g. in the above example, print $_POST['mydropdown'] would print "y" if "Yes" had been selected.


### Example: Creating a range

Use the [range](http://www.php.net/manual/en/function.range.php) function to generate numbers for you, e.g. 1 to 100 in increments of 5:

    <?php
    print \Formbuilder\Form::dropdown('mydropdown',range(1,100,5));
    ?>

### Example: Option Groups

By supplying a nested array as your options, you can generate option groups:

    <?php
    $options = array(
        'Birds' => array(
            'bluebird'  => 'Sad Bluebird',
            'crow'      => 'Black Crow',
        ),
        'Mammals' => array(
            'cow'   => 'Mute Cow',
            'dog'   => 'Good Dog',
        )
        'Reptiles' => array(
            'croc'  => 'Crocodile',
            'turtle' => 'Slow Turtle',
        )
    );
    print \Formbuilder\Form::dropdown('mydropdown',$options);
    ?>

## File

A file input will force the form to use the post method and the "enctype" to "multipart/form-data" so that the form 
can be submitted and processed properly.

**Syntax:** `file(string $name, string $default='', array $args=array(), string $tpl=null)`

## Radio

Radio fields are functionally equivalent to dropdown fields, but the formatting is more problematic because
of how the formatting strings need to be stacked on top of one another.

**Syntax**: `radio($name,$options=array(),$default='',$args=array(),$tpl=null)`

Simple options:

    <?php
    print \Formbuilder\Form::radio('attending',array('Yes','No','Maybe'));
    ?>

Complex options:

    <?php
    print \Formbuilder\Form::radio('attending',array('y'=>'Yes','n'=>'No','m'=>'Maybe'));
    ?>

## Multiselect

Similar to the **dropdown** field element, but this element allows users to select an array of options.

**Syntax:** `multiselect($name,$options=array(),$values=array(),$args=array(),$tpl=null)`

Note that the default formatting template (tpl) for multiselect fields defines an array input: `name="[+name+][]"` 
so you do not need to include "[]" as part of your $name argument.

### Simple Multiselect

    <?php
    print \Formbuilder\Form::multiselect('genre',array('Scifi','Fiction','Horror','Romance'));
    ?>

### Option Groups

Just as with the **dropdown()** fields, you can pass nested data to a multiselect field to specify option groups:

    <?php
    $options = array(
        'Birds' => array(
            'bluebird'  => 'Sad Bluebird',
            'crow'      => 'Black Crow',
        ),
        'Mammals' => array(
            'cow'   => 'Mute Cow',
            'dog'   => 'Good Dog',
        )
        'Reptiles' => array(
            'croc'  => 'Crocodile',
            'turtle' => 'Slow Turtle',
        )
    );
    print \Formbuilder\Form::multiselect('categories',$options);
    ?>


## Multicheck

The multicheck field is functionally equivalent to the multiselect field, but using multiple checkboxes offers an 
alternate user interface for the element.

**Syntax:** `multicheck($name,$options=array(),$values=array(),$args=array(),$tpl=null)`

Note that the default formatting template (tpl) for multicheck fields defines an array input: `name="[+name+][]"` 
so you do not need to include "[]" as part of your $name argument.

See the multiselect field for examples.

## Hidden

Same signature as a text field.

## Submit

Same signature as a text field.  Don't forget to add a submit button to your form -- Formbuilder will not add it for you.


------------

# Creating a Form

## Opening a Form

**Syntax:** `open($action='',$args=array(),$secure=true,$tpl=null)`

This handles creating the `<form>` tag.


## Building a simple Form

This package was designed to useable in various circumstances, including simple and advanced development flows.

Here's an example of some simple usage:

    <form>
        <?php print \Formbuilder\Form::text('first_name'); ?>
        <?php print \Formbuilder\Form::text('last_name'); ?>
        <input type="submit" value="Save"/>        
    </form>

Here's a more advanced example:

    <?php
    print \Formbuilder\Form::open('/my/page')
        ->text('first_name')
        ->text('last_name')
        ->submit('Save')
        ->close();
    ?>


    <?php
    print \Formbuilder\Form::open('/my/page')
        ->text('first_name','',array('label'=>'First Name','description'=>'Enter your first name.'))
        ->submit('Save')
        ->close();    
    ?>


### Repopulating form values

To populate values, use the *setValues* method.  This is useful if you are editing a database record or if you are repopulating
the form after failed validation.  It is important to set the values **before** you create your fields.

    <?php
    print \Formbuilder\Form::open('/my/page')
        ->setValues($_POST)
        ->text('first_name','',array('label'=>'First Name','description'=>'Enter your first name.'))
        ->submit('Save')
        ->close();    
    ?>

Or sometimes you may need to do this in non-contiguous parts on a page:

    <?php
    \Formbuilder\Form::setValues($_POST);
    // ... 
    print \Formbuilder\Form::open('/my/page')
        ->text('first_name','',array('label'=>'First Name','description'=>'Enter your first name.'))
        ->submit('Save')
        ->close();    
    ?>

The principle works the same if you want to populate a form with values from a database record, just make sure the keys/values in your
array line up with the names used in your form:

    <?php
    $data = get_record_from_database();
    
    \Formbuilder\Form::setValues($data);
    
    print \Formbuilder\Form::open('/update/my/record')
        ->text('first_name','',array('label'=>'First Name','description'=>'Enter your first name.'))
        ->submit('Save')
        ->close();    
    ?>

-----------------

## Ad Hoc Text

The simplest option for injecting custom text/html into your form is to use your own formatting templates.  
Consider the following low-brow hack:

    <?php
    print \Formbuilder\Form::open()
        ->text('adhoc','',array(),'<p>A long time ago in a galaxy far, far away...</p>')
        ->close(); 
    ?>

By overriding the default template, you effectively neuter the method of any functionality. (Don't do this, by the way: please use the **html()** method instead).

Use the **html()** method to inject random bits of HTML into a form.  You may find this useful when using method-chaining.

**Syntax:** `html($str,$args=array())`

    <?php
    print \Formbuilder\Form::open()
        ->text('act1')
        ->html('<p>I can inject any HTML I want here using the html method.</p>')
        ->text('act2')
        ->html('<p>Dear [+first_name+], I can also use placeholders in my text.</p>', array('first_name'=>'Padawan'))
        ->close(); 
    ?>

If desired, you can use placeholders in your ad-hoc HTML.

-----------------

## Registering Custom Fields

Although a majority of cases can be handled via custom formatting templates, CSS classes, and custom Javascript to go along with them,
the need may arise to create your own types of fields or use your own logic in determining how they are drawn and repopulated. 
For this task, there is the **register()** and **unregister()** functions.

### Defining your Own Field Type

**Syntax:** `register(string $fieldtype, mixed $callback)`

* `$fieldtype` _string_ the name of your type of field. Must be valid as a function name.
* `$callback` _mixed_ any valid callback

For example, let's say your form must sign a form submission with a value obtained from an API lookup.

    <?php
    function api_lookup($api_key,$datestamp) {
        $value = rand(1,1000); // <-- do something cool here
        $out = '<input type="hidden" name="api_validation" value="'.$value.'" />';
        return Formbuilder\Form::chain($out); 
    }
        
    // Register the callback
    \Formbuilder\Form::register('api', 'api_lookup');
    // ... 
    print \Formbuilder\Form::open('https://somesite.com/api/endpoint')
        ->text('first_name')
        ->text('last_name')
        ->api('AbCdEf98765',date('Y-m-d H:i:s'))
        ->submit('Save')
        ->close();    
    ?>

The signature for your callback can be whatever you want it to be: the arguments are passed in exactly as they are called.
Make sure your custom function returns the output via `return Formbuilder\Form::chain()` -- this is what allows a method 
to be chainable. 

## Overriding an existing Field Type

You can use the **register()** function to override built-in behavior for any field type. For example, if you wish to 
use Javascript to handle your date fields you could either specify a custom formatting template for your date fields using the **setTpl()** function or by registering your own custom callback for the date field:


    <?php
    function my_date($name,$default='',$args=array()) {
        // Do calculations or something
        $out = '<input type="text" class="" name="'.$name.'" value="'.$value.'" />';
        return Formbuilder\Form::chain($out); 
    }
        
    // Register the callback
    \Formbuilder\Form::register('date', 'my_date');
    // ... 
    print \Formbuilder\Form::open()
        ->text('first_name')
        ->text('last_name')
        ->date('birthday')
        ->submit('Save')
        ->close();    
    ?>

When overriding built-in functionality, it can be a good idea to review the function you are overriding to make sure your 
new functionality covers the expected use cases.

REMEMBER: most of these types of customizations can be accomplished by using custom field templates.  For example, an easier 
way to leverage a Javascript date selector might be to set an appropriate CSS class for the date fields in question:

    <!-- See http://jqueryui.com/datepicker/ -->
    <script>
        $(function() {
            $( ".datepicker" ).datepicker();
        });
    </script>
    
    <?php
    // This replaces the tpl with an HTML 4 friendly text field and hard-codes the CSS class 
    \Formbuilder\Form::setTpl('date', '[+label+]
            [+error+]
            <input type="text" name="[+name+]" id="[+id+]" value="[+value+]" class="datepicker" [+extra+]/>
            [+description+]');
     
    print \Formbuilder\Form::open()
        ->date('birthday')
        ->date('anniversary')
        ->submit('Save')
        ->close();    
    ?>



## Unregistering a Callback

If you need to unregister a callback at any time, you can use the **unregister()** method:

**Syntax:** `unregister(string $fieldtype)`

This will cause the specified fieldtype to revert to its default behavior.

-----------------

## Translations

If you are localizing the messages used in your forms, you can register a translation function using the **setTranslator()** function.  It just needs a valid callback.  

**Syntax:** `setTranslator(mixed $callback)`

For each field, the following parameters are passed through the translator function:

* **label**
* **description**
* **error**


### Wordpress

Wordpress's translation function is normally `__()`, but it requires a second argument for the text-domain.  To accomplish this, 
we must wrap the callback a bit:

    <?php
    \Formbuilder\Form::setTranslator(function($str){ return __($str,'my-text-domain');});
    ?>

### MODX

MODX uses object-oriented lexicons, so the callback can be an object/method array:

    <?php
    // Usually the topic would be loaded somewhere at the start of your script
    $modx->lexicon->load('topicname');
    
    \Formbuilder\Form::setTranslator(array($modx,'lexicon'));
    ?>


Unfortunately, the Formbuilder's translator callback accepts only one parameter, so it cannot take advantage of MODX's 
placeholders for translation strings.

See http://rtfm.modx.com/revolution/2.x/developing-in-modx/advanced-development/internationalization

------------

## Validation

IN PROGRESS....

You want validation... Formbuilder attempts to cover you with a couple patterns that should cover most of your needs.

Validation rules are defined as key value pairs pegged to each field by its name.  Formbuilder offers common validation
rules for you for convenience.  For all other rules, you can define your own callback function.

    <?php
    $rules = array(
        'first_name' => 'required|alpha',
        'foozlebaum' => function ($val) { 
            if($val=='xyz') {
                return;
            } 
        }
    );
    ?>

### Custom Validation Callbacks

Your custom function should accept a string and return true if validation passes or an error message string if it fails.

## Errors

If you need to keep things really simple and do custom error checking, then you can set an error message
by passing an "error" attribute to any field:

        <?php print \Formbuilder\Form::text('first_name','',array('error'=>'Something went wrong')); ?>

More often, however, you'll want to pass an array of key/value pairs corresponding to field names.  The Validator class
helps automate this.

    <?php
    if ($errors = \Formbuilder\Validator::check($rules,$_POST)) {
        // handle the form, do something, then redirect etc.
    }
    // draw form
    print \Formbuilder\Form::open('/my/page')
        ->setValues($_POST)
        ->setErrors($errors)
        ->text('first_name','',array('label'=>'First Name','description'=>'Enter your first name.'))
        ->submit('Save')
        ->close();    
    ?>



## Customizing CSS Classes

You can pass a "class" argument to visible fields:

    <?php
    \Formbuilder\Form::text('first_name', '', array('class'=>'my_css_class'));
    ?>

For more centralized control, you can set a CSS style for any field type using the setClass() method:

    <?php
    \Formbuilder\Form::setClass('radio', 'selector-class my-radio-class');
    ?>

Or do this globally for all instances of a particular field type:

    <?php
    print \Formbuilder\Form::open('/my/page')
        ->setClass('text', 'my-text-class')
        ->text('first_name')
        ->text('last_name')
        ->text('title')
        ->submit('Save')
        ->close(); 
    ?>

The CSS class must be set *before* you use the given type of field.  To demonstrate the importance of order, in the 
following example, the first text field has a class of "x" whereas the second a class of "y":

    <?php
    print \Formbuilder\Form::open('/my/page')
        ->setClass('text', 'x')
        ->text('only')
        ->setClass('text', 'y')
        ->text('an_example')
        ->close();    
    ?>

Note: This is just an example!  Please don't write code that inefficient!  If you need to set different classes on different instances of fields then pass a "class" attribute to the field(s) you need to customize.


## Customizing HTML

If the HTML generated by these functions does not meet your needs, you can override it in one of two ways:
you can either pass a formatting string as an argument to each function, or you can set a global template.

To override a single instance of a field, you can pass a formatting template to the function:

    <?php print \Formbuilder\Form::text('first_name','Bob',array(),
        '<input type="text" name="[+name+]" id="[+id+]" value="[+value+]" class="myclass" onclick="javascript:do_something();"/>'); ?>

Remember: it's critical that you keep the placeholders in your formatting strings! Some of them you may be able to hard-code without
any ill effects, but they are meant to be variables.

To override a global instance of a formatting template, you can specify a new value using the setTpl() method:

    <?php
    \Formbuilder\Form::setTpl('text', '<input type="text" name="[+name+]" id="[+id+]" value="[+value+]" class="myclass"/>');
    ?>
    
Or you can do this inline, but it must be done before the relevant tpl is needed:

    <?php
    \Formbuilder\Form::open()
        ->text('this_field_uses_the_default_tpl')
        ->setTpl'text', '<input type="text" name="[+name+]" id="[+id+]" value="[+value+]" class="myclass"/>')
        ->text('this_field_uses_the_custom_one');
    ?>


See the class for various templates available.  Remember that most templates include placeholders for [+label+], [+description+], and [+error+] to support labeling, descriptions, and error messages.



## Customizing Parsing Function (ADVANCED)

Why are you here?  Do you seriously have issues with the parser?  Sigh.  Well, I don't understand it, but it is possible
to monkey with the parser behavior if absolutely necessary.  NOTE: This represents advanced modification: most users should never need to mess with this.

Formbuilder parses static text to render its output: it does not parse PHP in its templates by default. 
This behavior, however, is customizable.  If you wish to use your own parsing function, pass a valid callback
to the setParser() method. 


    <?php
    // reference a function by name
    Formbuilder\Form::setParser('my_custom_parser');
    
    // reference a class method
    $Parser = new MyCustomParser();
    Formbuilder\Form::setParser(array($Parser, 'mymethod'));

    // Pass an anonymous function closure
    Formbuilder\Form::setParser(function($tpl,$args){
        
    });
    ?>
    
The function you reference should accept 2 values:
    $tpl : the template string (usually a formatting string defined in )
    $args : array of key/value pairs 
    

If you wish to use PHP parsing instead of the default string-replace parsing, you can 
modify your templates and the parsing function:

    <?php
    $custom_tpls = array(
        'checkbox'      => 'checkbox.php',
        'color'         => 'color.php',
        // ... etc...
        'text'          => 'text.php',
        'textarea'      => 'textarea.php',
    );
    
    Formbuilder\Form::setTpls($custom_tpls);

    Formbuilder\Form::setParser(function($tpl,$args){
        $file = '/path/to/'.$tpl;
    
    	if (is_file($file)) {
    		ob_start();
    		extract($args);
    		include $file;
    		return ob_get_clean();
    	}        
    });

    ?>
    