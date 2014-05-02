Formbuilder
===========

In memory of my dumb friends...

I grew tired of repeating myself myself.  This library is built to generate HTML forms in ways that are re-usable.
I got tired of Symfonic noise with no docs.  Why you no document your code?  Without further ado...

## Reality Check

Just to center yourself as to what we're up to here, let's review the landscape.  An HTML form is created of 
fields (a.k.a. form elements).  Each element *requires* a name to identify its value in the $_POST or $_GET array.
E.g. a field like <input type="text" name="insane" value="membrane"/> will populate $_POST['insane'] when posted.

In order for us to create a valid form element, we need to know the following:

1. What kind of element is it?  (required: text, textarea, password, etc.)
2. What is the name of the element? (required)
3. What other parameters do you want this element to have? (special sauce like a CSS class or an onclick etc.)
4. How should the element be formatted?  Do you want to specify a custom formatting string?  (optional)


## Supported Inputs

You can use this class to generate the following types of inputs.  Most of these are verbatim implementations of
the supported HTML input types, but some (like "dropdown" or "multicheck") offer convenient interfaces.

* checkbox - you can customize the values sent when the box is checked or unchecked.
* color - (HTML 5)
* datalist - a typeahead type dropdown (HTML 5)
* date - (HTML 5)
* datetime - (HTML 5)
* datetime-local - (HTML 5)
* dropdown - a select field, used to select a single option. 
* email - (HTML 5)
* file - used to choose a local file for uploading
* hidden - hidden fields
* keygen - (HTML 5)
* month  - (HTML 5)
* multiselect - a select field used to select multiple options
* multicheck - functionally the same as multiselect, but formatted as multiple checkboxes
* number  - (HTML 5)
* output - used to store calculated values (HTML 5)
* password - a standard password field
* radio - functionally equivalent to a dropdown, but uses radio options
* range - displays a slider for selecting number within a range (HTML 5)
* search - displays a search form (HTML 5)
* submit - a standard submit button
* text - the original
* textarea - a standard textarea


## Creating Form Elements

In the simplest invocation, you just need to call the function corresponding to an input type.
Each function has its own signature; some fields require different types of data, so review
the documentation for each function.

*Code:*
    <?php
    print \Formbuilder\Form::text('my_field');
    ?>

*Output:*

    <input type="text" name="my_field" id="my_field" value="" />


I should back up... you need to ensure that you are including the relevant classes.  This is 
done by including the vendor/autoload.php first:

    <?php
    require_once '/path/to/formbuilder/vendor/autoload.php';
    ?>

Once you've done that, the autoloader should find the classes for you without any other include or 
require statements.

If you've got your namespace-fu going, you can simplify your call to something like this: 

    <?php
    use Formbuilder;
    require_once '/path/to/formbuilder/vendor/autoload.php';
    print Form::text('my_field');
    ?>

That's a bit cleaner if it works for you.


Text
====

Textarea
========

Checkbox
========

Form::checkbox('mycheckbox', 1);


## Creating a Form

The library here was designed to useable in various circumstances, including simple and advanced development flows.

Here's an example of some simple usage:

    <form>
        <?php print \Formbuilder\Form::text('first_name'); ?>
        <?php print \Formbuilder\Form::text('last_name'); ?>
        <input type="submit" value="Save"/>        
    </form>

Here's a more advanced example:

    <?php
    print \Formbuilder\Form::open(array('action'=>'/my/page'))
        ->text('first_name')
        ->text('last_name')
        ->submit('Save')
        ->close();
    ?>


    <?php
    print \Formbuilder\Form::open(array('action'=>'/my/page'))
        ->text('first_name','',array('label'=>'First Name','description'=>'Enter your first name.'))
        ->submit('Save')
        ->close();    
    ?>


Repopulating form values.

To repopulate values...


Customizing HTML
================

If the built-in HTML is not meeting your needs, you can override it with your own. You can do this in one of two ways:
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

See the class for various templates available.



Customizing Parsing Function (ADVANCED)
=======================================

This represents advanced modification: most users should not need to monkey with these behaviors.

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
    