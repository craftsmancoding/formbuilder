Formbuilder
===========

In memory of of my dumb friends...

I grew tired of repeating myself myself.  This library is built to generate HTML forms in ways that are re-usable.
I got tired of Symfonic noise with no docs.  What good is code if you don't document it?  Without further ado...

Reality Check
=============

Just to center yourself as to what we're up to here, let's review the landscape.  An HTML form is created of 
fields (a.k.a. form elements).  Each element *requires* a name to identify its value in the $_POST or $_GET array.
E.g. a field like <input type="text" name="insane" value="membrane"/> will populate $_POST['insane'] when posted.

In order for us to create a valid form element, we need to know the following:

1. What kind of element is it?  (required: text, textarea, password, etc.)
2. What is the name of the element? (required)
3. What other parameters do you want this element to have? (special sauce like a CSS class or an onclick etc.)
4. How should the element be formatted?  Do you want to specify a custom formatting string?  (optional)



Creating Form Elements
======================

Text
====

Textarea
========

Checkbox
========

Form::checkbox('mycheckbox', 1);


Creating a Form
===============


Customizing HTML
================


Customizing Parsing Function
============================