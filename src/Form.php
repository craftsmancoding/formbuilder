<?php
/**
 * Every chainable function should output indirectly via static::chain -- 
 * this lets us either chain methods or print them out one by one.
 *
 * class, label, desc, error
 *
 */
namespace Formbuilder;

class Form {

    // Valid callback
    public static $parser = '\\Formbuilder\\Form::defaultParse';
    public static $translator = '\\Formbuilder\\Form::defaultTranslator';
    public static $idPrefix = '';
    public static $namePrefix = '';
    public static $instance;
    public static $opened = false; // Form::open() sets this to true, used to group output in method chaining
    public static $output = '';
    public static $values = array();
    public static $errors = array();
    
    /**
     * Register a callback function for any built-in form element function, or register your own
     * field types here using 
     */
    public static $callbacks = array();
    
    
    // Stores classes for various field types
    public static $class = array(
        'checkbox' => 'checkbox',
        'file' => 'file',
        'datalist' => 'datalist',
        'dropdown' => 'dropdown',
        'error' => 'error',
        'hidden' => 'hidden',
        'keygen' => 'keygen',
        'multicheck' => 'multicheck',
        'multiselect' => 'multiselect',
        'output' => 'output',
        'password' => 'password',
        'radio' => 'radio',
        'range' => 'range',
        'text' => 'text',
        'textarea' => 'textarea',
        'color' => 'color',
        'date' => 'date',
        'datetime_local' => 'datetime_local',
        'email' => 'email',
        'month' => 'month',
        'number' => 'number',
        'search' => 'search',
        'time' => 'time',
        'week' => 'week',
        'url' => 'url',
        'submit' => 'submit',
    );
    public static $attributes = array();
    // new in HTML 5: datalist, keygen, range, output
    // Todo: create HTML 4.01 / HTML5 / XHTML variants (?)
    public static $tpls = array(
        'checkbox'      => '[+error+]
            <input type="hidden" name="[+name+]" value="[+unchecked_value+]"/>
            <input type="checkbox" name="[+name+]" id="[+id+]" value="[+checked_value+]" class="[+class+]" [+is_checked+][+extra+]/> 
            [+label+]
            [+description+]',
        'color'         => '[+label+]
            [+error+]
            <input type="color" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',
        'datalist'      => '[+label+]
            [+error+]
            <input list="[+id+]" name="[+name+]" value="[+value+]" id="[+id+]" class="[+class+]" [+extra+]><datalist id="[+id+]">[+data+]</datalist>
            [+description+]',
        'data'          => '<option value="[+value+]" class="[+class+]">',
        'date'          => '[+label+]
            [+error+]
            <input type="date" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',
        'datetime_local'    => '[+label+]
            [+error+]
            <input type="datetime-local" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',
        'dropdown'      => '[+label+]
            [+error+]
            <select name="[+name+]" id="[+id+]" [+extra+]>[+options+]</select>
            [+description+]',          
        'email'         => '[+label+]
            [+error+]
            <input type="email" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',
        'error'         => '<div class="[+class+]">[+message+]</div>',
        'time'         => '[+label+]
            [+error+]
            <input type="time" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',
        'week'         => '[+label+]
            [+error+]
            <input type="week" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',
        'url'         => '[+label+]
            [+error+]
            <input type="url" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',
        // used by the multicheck
        'fieldset'      => '<fieldset><legend>[+legend+]</legend>[+fields+]</fieldset>',
        'file'          => '[+label+]
            [+error+]
            <input type="file" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',  
        'hidden'        => '<input type="hidden" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>',
        'keygen'        => '[+label+]
            [+error+]
            <keygen name="[+name+]" id="[+id+]" [+extra+]>
            [+description+]',
        'month'         => '[+label+]
            <input type="month" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',  
        'multiselect'   => '[+label+]
            [+error+]
            <select name="[+name+][]" id="[+id+]" multiple="multiple" [+extra+]>[+options+]</select>',
        'multicheck'    => '[+error+]
            <input type="checkbox" name="[+name+][]" id="[+id+]" value="[+value+]" class="[+class+]"[+is_checked+] [+extra+]/> [+option+]<br/>',
        'number'        => '[+label+]
            [+error+]
            <input type="number" name="[+name+]" id="[+id+]" min="[+min+]" max="[+max+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',
        'optgroup'      => '<optgroup label="[+label+]">[+options+]</optgroup>',
        'option'        => '<option value="[+value+]" class="[+class+]"[+is_selected+]>[+label+]</option>',
        'output'        => '[+label+]
            [+error+]
            <output name="[+name+]" id="[+id+]" for="[+for+]" [+extra+]>[+value+]</output>
            [+description+]',
        'password'      => '[+label+]
            [+error+]
            <input type="password" name="[+name+]" id="[+id+]" value="" [+extra+]/>
            [+description+]',
        'radio'         => '<input type="radio" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]"[+is_checked+] [+extra+]> [+option+]<br/>',
        'range'         => '[+label+]
            [+error+]
            <input type="range" id="[+id+]" name="[+name+] value="[+value+]" class="[+class+]" min="[+min+]" max="[+max+]" [+extra+]/>
            [+description+]',
        'search'        => '[+label+]
            [+error+]
            <input type="search" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',
        'submit'        => '[+label+]
            <input type="submit" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',
        'text'          => '[+label+]
            [+error+]
            <input type="text" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" [+extra+]/>
            [+description+]',
        'textarea'      => '[+label+]
            [+error+]
            [+description+]
            <textarea name="[+name+]" id="[+id+]" class="[+class+]" rows="[+rows+]" cols="[+cols+]" [+extra+]>[+value+]</textarea>',
        
        
        'token' => '<input type="hidden" name="[+name+]" value="[+name+]" />',
        
        'open' => '<form action="[+action+]" method="[+method+]" class="[+class+]" id="[+id+]" [+enctype+]>',
        'close' => '</form>',
        'label'         => '<label for="[+id+]" class="[+class+]">[+label+]</label>',
        'description'   => '<p class="[+class+]">[+description+]</p>',
        
    );

    /**
     * This is what gets called each time a static standalone function is run or
     * when a single chain is executed.
     */
    public function __toString() {        
        $out = static::$output;
        static::$output = ''; // reset
        return $out;
    }
    
    /**
     * We can "catch" calls to private methods by using this public function
     *
     */
    public static function __callStatic($name, $arguments) {
        if (array_key_exists($name, static::$callbacks)) {
            return call_user_func_array(static::$callbacks[$name], $arguments);
        }

        return call_user_func_array('\\Formbuilder\\Form::'.$name, $arguments);
    }

    public function __call($name, $arguments) {
        if (array_key_exists($name, static::$callbacks)) {
            return call_user_func_array(static::$callbacks[$name], $arguments);
        }

        return call_user_func_array(array($this,$name), $arguments);
    }    
    
    /**
     * Determine if an array is associative. See http://bit.ly/1lIXeN8
     *
     * @param array $array
     * @return boolean if array is an associative array (hash)
     */
    public static function isHash($array) {
        if (!is_array($array)) return false;
        if (array_values($array) === $array) {
            return false; // simple array
        }
        return true;
    }
    
    /**
     * Determine if an array is complex or not with nested arrays
     * @param array $array
     * @return boolean
     */
    public static function isComplex($array) {
        if (!is_array($array)) return false;
        foreach ($array as $k => $v) {
            if (is_array($v)) return true;
            return false;
        }
    }

    /**
     * Register or override a function that handles generating.
     *
     * @param string $fieldtype
     * @param mixed $callback
     */
    public static function register($fieldtype,$callback) {
        // is_callable ?
        static::$callbacks[$fieldtype] = $callback;
        return static::chain();
    }

    /**
     * De-register a callback
     *
     * @param string $fieldtype
     */
    public static function unregister($fieldtype) {
        unset(static::$callbacks[$fieldtype]);
        return static::chain();
    }

    /**
     * Set global attributes for the form
     */
    public static function setAttribute($key,$value) {
        if (is_scalar($key) && is_scalar($value)) {
            self::$attributes[$key] = $value;
        }
    }
    
    /**
     * Set global attributes for the form
     */
    public static function setAttributes($array) {
        self::$attributes = $array;
        return static::chain();
    }
    
    /**
     * Set CSS class(es), keys should match field types
     */
    public static function setClass($key,$value) {
        if (is_scalar($key) && is_scalar($value)) {
            self::$class[$key] = $value;
        }
        return static::chain();
    }

    /**
     * Set a specific error for the given field identified by its name.
     *
     * @param string $fieldname
     * @param string $errormsg
     */
    public static function setError($fieldname,$errormsg='') {
        if (is_scalar($fieldname) && is_scalar($errormsg)) {
            static::$errors[$fieldname] = $errormsg;
        }
        return static::chain();
    }

    /**
     * Pass an array here identifying any errors for fields keyed off their names.
     * This merges errors.
     * @param array $array
     */
    public static function setErrors(array $array) {
        foreach ($array as $fieldname => $errormsg) {
            self::setError($fieldname, $errormsg);
        }
        
        return static::chain();
    }
    
    /**
     * Seriously?  You want to do your own parsing thang? Ok, set a callback here.
     *
     * @param mixed $callback any valid callback.
     */
    public static function setParser($callback) {
        static::$parser = $callback;
        return static::chain();
    }

    /**
     * Handle translations for field labels, descriptions, and errors
     *
     * @param mixed $callback any valid callback.
     */
    public static function setTranslator($callback) {
        static::$translator = $callback;
        return static::chain();
    }
    
    /**
     * Override any of our default formatting strings 
     *
     */
    public static function setTpl($name,$str) {
        static::$tpls[$name] = $str;
        return static::chain();
    }

    /**
     * Bulk Override of our default formatting strings 
     *
     */
    public static function setTpls($array) {
        static::$tpls = $array;
        return static::chain();
    }
    
    /**
     * Parse the template string ($tpl) replacing any placeholders
     * with values from the $args.
     *
     * @param string $tpl
     * @param array $args any key/value pairs corresponding to placeholders
     * @param string $start identifies the start of a placeholder
     * @param string $end identifies the end of a placeholder
     */
    public static function parse($tpl,$args=array(),$start='[+',$end='+]') {
        return call_user_func_array(static::$parser, array($tpl,$args,$start,$end));
    }


    /**
     * Translate a string -- override this 
     *
     * @param string $str
     * @return string
     */
    public static function translate($str) {
        return call_user_func_array(static::$translator, array($str));
    }

    /**
     * Parse the template string ($tpl) replacing any placeholders
     * with values from the $args.
     *
     * @param string $tpl
     * @param array $args any key/value pairs corresponding to placeholders
     * @param string $start identifies the start of a placeholder
     * @param string $end identifies the end of a placeholder
     */
    public static function defaultParse($tpl,$args=array(),$start='[+',$end='+]') {
        foreach ($args as $key => $value) {
            if (!is_scalar($key)) continue;
            if (!is_scalar($value)) continue;
            $tpl = str_replace($start.$key.$end, $value, $tpl);
        }
        $tpl = preg_replace('/'.preg_quote($start).'(.*?)'.preg_quote($end).'/', '', $tpl);        
        return trim($tpl);
    }

    /**
     * Override this by using the setTranslator() function to specify a valid callback for your
     * translation function.
     *
     * @param string $value to be translated
     * @return string
     */
    public static function defaultTranslator($value) {
        return $value;
    }
    
    /**
     * Used in method chaining: we return an instance of this object
     *
     * The final output occurs via __toString()
     *
     * @param string $tpl formatting string usually containing [+placeholders+]
     * @param array $args key/value pairs, keys corresponding to placeholders
     * @return object instance
     */
    public static function chain($tpl='',$args=array()) {
        if (empty(static::$instance)) {
            static::$instance = new Form();
        }
        if ($tpl) {
            static::$output .= static::parse($tpl, $args);
        }
        return static::$instance;
    }

    /**
     * Close the form and reset stuff
     *
     */
    public static function close() {
        static::$instance = null;
        static::$opened = false;
        static::$values = array();
        static $errors = array();
        
        $tpl = static::$tpls['close'];
        $args = static::$attributes;
        //$args['content'] = static::$output;
        //static::$output = '';
        return static::chain($tpl,$args);  
    }

    /**
     * Get global attributes for the form
     */
    public static function getAttribute($key,$default='') {
        if (isset(self::$attributes[$key])) {
            return self::$attributes[$key];
        }
        return $default;
    }
    
    /**
     * Get CSS class for named field type
     */
    public static function getClass($key) {
        if (isset(self::$class[$key])) {
            return self::$class[$key];
        }
    }

    /**
     * Get a description for a field. Only used internally by other field functions when
     * they are called with a "description" argument.
     *
     * @param string $id
     * @param string $label
     * @return string 
     */
    public static function getDescription($id,$desc='') {
        if (empty($desc)) return '';
        $tpl = static::$tpls['description'];
        $args = array();
        $args['description'] = self::translate($desc); // translate
        $args['class'] = self::getClass('description');
        return self::parse($tpl,$args);
    }

    /**
     * Get and format any errors for the given field.  Errors can be 
     * passed simply by setting the "error" attribute for any field, or they
     * can be set separately during validation
     *
     * @param string $id
     * @param string $error message
     * @return string 
     */
    public static function getError($id,$error='') {
        $args = array();
        // Check for a globally set error
        if (empty($error) && isset(static::$errors[$id])) {
            $args['message'] = self::translate(static::$errors[$id]);  // translate
        }
        // Simple in-line error
        else {
            $args['message'] = self::translate($error);
        }
        if (empty($args['message'])) return '';
        
        $tpl = static::$tpls['error'];

        $args['id'] = $id;
        $args['class'] = self::getClass('error');
        return self::parse($tpl,$args);
    }
        
    /**
     * Convert a wild string into something viable as a CSS id.
     *
     * @param string $str
     * @return string (filtered)
     */
    public static function getId($str) {
        if (!is_scalar($str)) return '';
        return self::$idPrefix.preg_replace('/[^a-zA-Z0-9\-\_]/','_',$str);
    }

    /**
     * Get a label for a field. Only used internally by other field functions when
     * they are called with a "label" argument.
     *
     * @param string $id
     * @param string $label
     * @return string 
     */
    public static function getLabel($id,$label='',$extra_class='') {
        if (empty($label)) return '';
        $tpl = static::$tpls['label'];
        $args = array();
        $args['id'] = $id;
        $args['label'] = self::translate($label); // translate
        $args['class'] = self::getClass('label');
        if ($extra_class) {
            $args['class'] = trim($args['class'].' '.$extra_class);
        }
        return self::parse($tpl,$args);
    }

    /**
     * Convert a wild string into something viable as a field name attribute.
     *
     * @param string $str
     * @param boolean $is_array set to true if the name must store an array
     * @return string (filtered)
     */
    public static function getName($str) {
        if (!is_scalar($str)) return '';
        // TODO: validate the brackets?  E.g. some[thing] is ok, but some][thing is not.
        return self::$namePrefix.preg_replace('/[^a-zA-Z0-9\-\_\[\]]/','_',$str);
    }

    /**
     * Get a value for a field.  Typically this is read out of the $_POST or $_GET array
     * (e.g. when repopulating the form).
     *
     * @param string $str
     * @param string $default
     * @return string
     */
    public static function getValue($str,$default='') {
        if (is_scalar($str) && isset(self::$values[self::$namePrefix.$str])) {
            return self::$values[self::$namePrefix.$str]; 
        }
        return $default;
    }
    
    /**
     * We use a simple trick for checkboxes: add a hidden form field immediately prior to the checkbox. 
     * If the checkbox is not checked, the hidden field will submit its default value.
     *
     * Default behavior is to use 1|0 as checked|unchecked values.  If you want to use something different
     * then pass "checked_value" and "unchecked_value" in the $args.
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function checkbox($name,$default=0,$args=array(),$tpl=null) {
        
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];    
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        if (!isset($args['checked_value'])) $args['checked_value'] = 1;
        if (!isset($args['unchecked_value'])) $args['unchecked_value'] = 0;
        
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));        
        $args['is_checked'] = '';
        if($args['value'] == $args['checked_value']) {
            $args['is_checked'] = ' checked="checked"';        
        }
        
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');
        
        return static::chain($tpl,$args);
        
    }

    /**
     * File input. 
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function file($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        // enctype='multipart/form-data'
        self::setAttribute('enctype', 'multipart/form-data');
        self::setAttribute('method', 'post');
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');
        return static::chain($tpl,$args);  
    }

    /**
     * Datalist (HTML 5 only)
     *
     * @param string $name
     * @param array $data your data points
     * @param string $default value     
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function datalist($name,$data=array(),$default='',$args=array(),$tpl=null) {
        
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['data_tpl'])) $args['data_tpl'] = static::$tpls['data'];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        $args['data'] = '';
        
        foreach ($data as $k) {
            $opt_args = array('value' => htmlentities($k));
            $args['data'] .= self::parse($args['data_tpl'],$opt_args);                 
        }
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');
        return static::chain($tpl,$args); 
    }

    /**
     * Dropdown : for selecting a single option
     *
     * Flexible options are possible:
     *
     *  1. provide a simple array of $options if the stored option value is the same as the label, e.g.
     *      array('x') results in  <option value="x">x</option>
     *
     *  2. provide an associative array of $options if you want the visible label to differ, e.g.
     *      array('x'=>'X-men') results in <option value="x">X-men</option>
     *
     *  3. option groups are possible if you provide a more deeply nested array of $options, e.g.
     *      array(
     *       'X-men' => 
     *          array(
     *           'w' => 'Wolverine',
     *           'm' => 'Magento',
     *          ),
     *       'Marvel' =>
     *          array(
     *           's' => 'Spiderman',
     *          )
     *       )
     *      renders as 
     *          <optgroup label="X-men">
     *              <option value="w">Wolverine</option>
     *              <option value="m">Magento</option>
     *          </optgroup>
     *          <optgroup label="Marvel">
     *              <option value="s">Spiderman</option>
     *          </optgroup>
     *
     * @param string $name
     * @param array $options either a simple array or key/value hash or a complex array to define optgroup
     * @param string $default value
     * @param array $args additional arguments including 'option_tpl' and 'optgroup_tpl' for granular format control.
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function dropdown($name,$options=array(),$default='',$args=array(),$tpl=null) {
        
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['option_tpl'])) $args['option_tpl'] = static::$tpls['option'];
        if (!isset($args['optgroup_tpl'])) $args['optgroup_tpl'] = static::$tpls['optgroup'];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['options'] = '';
        $value = self::getValue($name,$default);
        
        // Complex with Option Groups
        if (self::isComplex($options)) {
            foreach($options as $optgroup_label => $subopts) {
                $optgroup_args = array('label' => $optgroup_label, 'options'=>'');
                // key/value sub-options
                if (self::isHash($subopts)) {
                    foreach ($subopts as $k => $v) {
                        $opt_args = array('value' => htmlentities($k), 'label' => htmlentities($v));
                        $opt_args['is_selected'] = ($k == $value)? ' selected="selected"': '';
                        $optgroup_args['options'] .= self::parse($args['option_tpl'],$opt_args); 
                    }
                }
                // simple sub-options
                else {
                    foreach ($subopts as $k) {
                        $opt_args = array('value' => htmlentities($k), 'label'=> htmlentities($k));
                        $opt_args['is_selected'] = ($k == $value)? ' selected="selected"': '';
                        $optgroup_args['options'] .= self::parse($args['option_tpl'],$opt_args);                 
                    }
                } 
                $args['options'] .= self::parse($args['optgroup_tpl'],$optgroup_args);             
            }
        }
        // Unique key/values
        elseif (self::isHash($options)) {
            foreach ($options as $k => $v) {
                $opt_args = array('value' => htmlentities($k), 'label' => htmlentities($v));
                $opt_args['is_selected'] = ($k == $value)? ' selected="selected"': '';
                $args['options'] .= self::parse($args['option_tpl'],$opt_args); 
            }
        }
        // Simple options
        else {
            foreach ($options as $k) {
                $opt_args = array('value' => htmlentities($k), 'label'=> htmlentities($k));
                $opt_args['is_selected'] = ($k == $value)? ' selected="selected"': '';
                $args['options'] .= self::parse($args['option_tpl'],$opt_args);                 
            }
        }
        
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');              
        return static::chain($tpl,$args);

    }

    /**
     * Hidden field
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function hidden($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        // Hidden fields shouldn't use a class (?)... but just in case
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args);  
    }

    /**
     * Used for ad-hoc additions to the form
     *
     * @param string $str any ad-hoc text, may be used as a formatting string.
     * @param array $args optional array of key/values to be used as placeholders in the $str
     */
    private static function html($str, $args=array()) {
        return static::chain($str,$args);
    }
    
    /**
     * keygen field (HTML 5 only)
     *
     * @param string $name
     * @param string $value default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function keygen($name,$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = '';
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args); 
    }


    /**
     * Multicheck : functionally this is equivalent to the multiselect, but a series of checkboxes
     * offers an alternative view.  We stack together the parsed instances of the multicheck tpl or 
     * fieldset tpls (similar to how we stack parsed radio tpls).  Note that for the multi-check, 
     * there is no need for the trickery we use in the regular checkbox where a hidden field is 
     * paired with each checkbox to ensure a value.
     *
     * Note that the "name" parameter must reference an array!
     * E.g. name="characters[]" -- checkout the 'multicheck' tpl to see where this is done.
     *
     * Flexible options are possible:
     *
     *  1. provide a simple array of $options if the stored option value is the same as the label, e.g.
     *      array('x') results in  <option value="x">x</option>
     *
     *  2. provide an associative array of $options if you want the visible label to differ, e.g.
     *      array('x'=>'X-men') results in <option value="x">X-men</option>
     *
     *  3. fieldsets with legends are possible if you provide a more deeply nested array of $options, e.g.
     *      array(
     *       'X-men' => 
     *          array(
     *           'w' => 'Wolverine',
     *           'm' => 'Magento',
     *          ),
     *       'Marvel' =>
     *          array(
     *           's' => 'Spiderman',
     *          )
     *       )
     *      renders as 
     *          <fieldset>
     *              <legend>X-men</legend>
     *              <!-- checkboxes... -->
     *          </fieldset>
     *          <fieldset>
     *              <legend>Marvel</legend>
     *              <!-- checkboxes... -->
     *          </fieldset>
     *
     * TODO: this is problematic because we are parsing prior to the final __toString() parsing.
     *
     * @param string $name
     * @param array $options either a simple array or key/value hash or a complex array to define optgroup
     * @param array $value -- current values
     * @param array $args additional arguments including 'field_tpl' for granular format control.
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function multicheck($name,$options=array(),$values=array(),$args=array(),$tpl=null) {
        
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!is_array($values)) $values = array($values); // <-- catch typos
        if (!isset($args['fieldset_tpl'])) $args['fieldset_tpl'] = static::$tpls['fieldset'];
        $base_id = (isset($args['id'])) ? $args['id'] : self::getId($name);
        $args['name'] = self::getName($name);
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');              
        $output = '';
        $values = self::getValue($name,$values);
        
        // Complex with Fieldsets
        $i = 0;
        if (self::isComplex($options)) {
            foreach($options as $legend => $fields) {
                // <fieldset><legend>[+legend+]</legend>[+fields+]</fieldset>
                $fieldset_args = array('legend' => $legend, 'fields'=>'');
                // key/value sub-options
                if (self::isHash($fields)) {
                    foreach ($fields as $k => $v) {
                        $args['id'] = $base_id.$i;
                        $args['value'] = htmlentities($k);
                        $args['option'] = $v;
                        $args['is_checked'] = (in_array($k,$values))? ' checked="checked"': '';
                        $fieldset_args['fields'] .= self::parse($tpl,$args); 
                        $i++;
                    }
                }
                // simple sub-options
                else {
                    foreach ($fields as $k) {
                        $args['id'] = $base_id.$i;
                        $args['value'] = htmlentities($k);
                        $args['option'] = $k;
                        $args['is_checked'] = (in_array($k,$values))? ' checked="checked"': '';
                        $fieldset_args['fields'] .= self::parse($tpl,$args);               
                        $i++;
                    }
                } 
                $output .= self::parse($args['fieldset_tpl'],$fieldset_args);
            }
        }
        // Unique key/values
        elseif (self::isHash($options)) {
            foreach ($options as $k => $v) {
                $args['id'] = $base_id.$i;
                $args['value'] = htmlentities($k);
                $args['option'] = $v;
                $args['is_checked'] = (in_array($k,$values))? ' checked="checked"': '';
                $output .= self::parse($tpl,$args); 
                $i++;
            }
        }
        // Simple options
        // <input type="checkbox" name="[+name+][]" id="[+id+]" value="[+checked_value+]" [+is_checked+][+extra+]/> [+label+]<br/>
        else {
            foreach ($options as $k) {
                $args['id'] = $base_id.$i;
                $args['value'] = htmlentities($k);
                $args['option'] = $k;
                $args['is_checked'] = (in_array($k,$values))? ' checked="checked"': '';
                $output .= self::parse($tpl,$args);                 
                $i++;
            }
        }
        
        return static::chain($output);

    }

    /**
     * Multiselect : for selecting multiple options from a list. See also multicheck.
     * This is a very close copy of the dropdown function, but instead of one current value, this
     * supports an array of values. Note that the "name" parameter must reference an array!
     * E.g. name="characters[]" -- checkout the 'multiselect' tpl.
     *
     * Flexible options are possible:
     *
     *  1. provide a simple array of $options if the stored option value is the same as the label, e.g.
     *      array('x') results in  <option value="x">x</option>
     *
     *  2. provide an associative array of $options if you want the visible label to differ, e.g.
     *      array('x'=>'X-men') results in <option value="x">X-men</option>
     *
     *  3. option groups are possible if you provide a more deeply nested array of $options, e.g.
     *      array(
     *       'X-men' => 
     *          array(
     *           'w' => 'Wolverine',
     *           'm' => 'Magento',
     *          ),
     *       'Marvel' =>
     *          array(
     *           's' => 'Spiderman',
     *          )
     *       )
     *      renders as 
     *          <optgroup label="X-men">
     *              <option value="w">Wolverine</option>
     *              <option value="m">Magento</option>
     *          </optgroup>
     *          <optgroup label="Marvel">
     *              <option value="s">Spiderman</option>
     *          </optgroup>
     *
     * @param string $name
     * @param array $options either a simple array or key/value hash or a complex array to define optgroup
     * @param array $value -- current values
     * @param array $args additional arguments including 'option_tpl' and 'optgroup_tpl' for granular format control.
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function multiselect($name,$options=array(),$values=array(),$args=array(),$tpl=null) {
        
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!is_array($values)) $values = array($values); // <-- catch typos
        if (!isset($args['option_tpl'])) $args['option_tpl'] = static::$tpls['option'];
        if (!isset($args['optgroup_tpl'])) $args['optgroup_tpl'] = static::$tpls['optgroup'];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['options'] = '';
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');              
        $values = self::getValue($name,$values);
        
        // Complex with Option Groups
        if (self::isComplex($options)) {
            foreach($options as $optgroup_label => $subopts) {
                $optgroup_args = array('label' => $optgroup_label, 'options'=>'');
                // key/value sub-options
                if (self::isHash($subopts)) {
                    foreach ($subopts as $k => $v) {
                        $opt_args = array('value' => htmlentities($k), 'label' => htmlentities($v));
                        $opt_args['is_selected'] = (in_array($k,$values))? ' selected="selected"': '';
                        $optgroup_args['options'] .= self::parse($args['option_tpl'],$opt_args); 
                    }
                }
                // simple sub-options
                else {
                    foreach ($subopts as $k) {
                        $opt_args = array('value' => htmlentities($k), 'label'=> htmlentities($k));
                        $opt_args['is_selected'] = (in_array($k,$values))? ' selected="selected"': '';
                        $optgroup_args['options'] .= self::parse($args['option_tpl'],$opt_args);                 
                    }
                } 
                $args['options'] .= self::parse($args['optgroup_tpl'],$optgroup_args);             
            }
        }
        // Unique key/values
        elseif (self::isHash($options)) {
            foreach ($options as $k => $v) {
                $opt_args = array('value' => htmlentities($k), 'label' => htmlentities($v));
                $opt_args['is_selected'] = (in_array($k,$values))? ' selected="selected"': '';
                $args['options'] .= self::parse($args['option_tpl'],$opt_args); 
            }
        }
        // Simple options
        else {
            foreach ($options as $k) {
                $opt_args = array('value' => htmlentities($k), 'label'=> htmlentities($k));
                $opt_args['is_selected'] = (in_array($k,$values))? ' selected="selected"': '';
                $args['options'] .= self::parse($args['option_tpl'],$opt_args);                 
            }
        }
        
        return static::chain($tpl,$args);

    }

    /**
     * Open a form and initialize settings/values/errors
     * We rely on the "global" setAttributes here -- we don't put anything substantial onto the stack.
     *
     * @param string $action URL where form gets submitted.
     * @param array $args any parameters corresponding to placeholders in the tpl
     * @param boolean $secure
     * @param string $tpl template string
     */
    private static function open($action='',$args=array(),$secure=true,$tpl=null) {
        static::$instance = null; // new Form();
        static::$opened = true;
        static::$output = '';
        self::setParser('\\Formbuilder\\Form::defaultParse');
        
        $args['action'] = htmlentities($action);
        $args['secure'] = $secure;
        if (!$tpl) $tpl = static::$tpls['open'];
        if (!isset($args['method'])) $args['method'] = 'post';
        $args['tpl'] = $tpl; // store as an attribute
        static::setAttributes($args);

        return static::chain($tpl,$args);
    }
    
    /**
     * Output field (HTML 5 only)
     * This should correspond to an "oninput" event in the <form> tag. 
     *
     * @param string $name
     * @param mixed $for specifies the element(s) used in the calculation (separated by space). If array, will implode.
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function output($name,$for='',$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['for'] = (is_array($for)) ? implode(' ',$for) : $for;
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args); 
    }
    
    /**
     * Standard password field. Like text, but we don't pass a value. 
     *
     * @param string $name
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function password($name,$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = '';
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args); 
    }

    /**
     * Radio buttons
     *
     * Functionally these are the same as a dropdown, but the formatting here is more problematic.
     * We stack instances of the 'radio' tpl, one on top of the other.
     *
     * @param string $name
     * @param array $options either a simple array or key/value hash
     * @param array $args additional arguments
     * @param string $default value
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function radio($name,$options=array(),$default='',$args=array(),$tpl=null) {
        
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');              
        $output = '';
        $value = self::getValue($name,$default); 
        // Unique key/values
        if (self::isHash($options)) {
            foreach ($options as $k => $v) {
                $args['value'] = htmlentities($k);
                $args['option'] = trim($v);
                $args['is_checked'] = ($k == $value)? ' checked="checked"': '';
                $output .= self::parse($tpl,$args); 
            }
        }
        // Simple options
        else {
            foreach ($options as $k) {
                $args['value'] = htmlentities($k);
                $args['option'] = trim($k);
                $args['is_checked'] = ($k == $value)? ' checked="checked"': '';
                $output .= self::parse($tpl,$args);                 
            }
        }
        
        return static::chain($output);
    }


    /**
     * Range (HTML 5 only)
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function range($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        if (!isset($args['min'])) $args['min'] = 1;
        if (!isset($args['max'])) $args['max'] = 100;

        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args); 
    }


    /**
     * Let there be text. 
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function text($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');
        return static::chain($tpl,$args); 
    }


    /**
     * Standard textarea field.
     *
     * We set default values for "rows" and "cols".
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class     *
     */
    private static function textarea($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['rows'])) $args['rows'] = 4;
        if (!isset($args['cols'])) $args['cols'] = 40;
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');              
        return static::chain($tpl,$args);
    }

    /**
     * Create a nonce single-use form token
     */
    private static function token() {
        $tpl = static::$tpls['token'];
        
    }
    
    /**
     * HTML5 color input. 
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function color($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args);
    }

    /**
     * HTML5 date input. 
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function date($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args);
    }

    /**
     * HTML5 datetime_local input. 
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function datetime_local($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args);
    }

    /**
     * HTML5 email input. 
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function email($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args);
    }

    /**
     * HTML5 month input. 
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function month($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args);
    }

    /**
     * HTML5 number input. 
     *
     * @param string $name
     * @param string $default value
     * @param int $min current min
     * @param int $max current max
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function number($name,$min=0,$max=10,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        $args['min'] = (int) $min;
        $args['max'] = (int) $max;
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args);
    }

    /**
     * HTML5 search input. 
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function search($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args);
    }

    /**
     * HTML5 time input. 
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function time($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args);
    }

    /**
     * HTML5 week input. 
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function week($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args);
    }

    /**
     * HTML5 url input. 
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function url($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args);
    }

    /**
     * HTML5 submit input. 
     *
     * @param string $name
     * @param string $default value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    private static function submit($name,$default='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities(self::getValue($name,$default));
        if (!isset($args['class'])) $args['class'] = htmlentities(self::getClass(__FUNCTION__));
        if (isset($args['label'])) $args['label'] = self::getLabel($args['id'],$args['label'],__FUNCTION__.'label');
        if (isset($args['description'])) $args['description'] = self::getDescription($args['id'],$args['description']);
        $args['error'] = self::getError($args['id'],(isset($args['error']))?$args['error']:'');      
        return static::chain($tpl,$args);
    }


    /**
     * You can set values for fields.  
     *
     * @param string $fieldname
     * @param mixed $value
     */
    public static function setValue($fieldname,$value) {
        if (is_scalar($fieldname)) {
            static::$values[$fieldname] = $value;
        }
        return static::chain();
    }

    /**
     * Pass $_POST or $_GET to this function to have the form fields repopulated
     *
     * @param array $array
     */
    public static function setValues($array) {
        foreach ($array as $fieldname => $value) {
            self::setValue($fieldname,$value);
        }
        return static::chain();
    }
    
    
    
}
/*EOF*/