<?php
namespace Formbuilder;

class Form {

    // Valid callback
    public static $parser = '\\Formbuilder\\Form::defaultParse';
    public static $idPrefix = '';
    public static $namePrefix = '';
    public static $instance;
    public static $opened = false; // Form::open() sets this to true, used to group output in method chaining
    public static $output = '';
    
    // new in HTML 5: datalist, keygen, range, output
    // Todo: create HTML 4.01 / HTML5 / XHTML variants (?)
    public static $tpls = array(
        'checkbox'      => '<input type="hidden" name="[+name+]" value="[+unchecked_value+]"/><input type="checkbox" name="[+name+]" id="[+id+]" value="[+checked_value+]" [+is_checked+][+extra+]/>',
        'color'         => '<input type="color" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',
        'datalist'      => '<input list="[+id+]" name="[+name+]" id="[+id+]" [+extra+]><datalist id="[+id+]">[+data+]</datalist>',
        'data'          => '<option value="[+value+]">',
        'date'          => '<input type="date" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',
        'datetime_local'    => '<input type="datetime-local" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',
        'dropdown'      => '<select name="[+name+]" id="[+id+]" [+extra+]>[+options+]</select>',  
        'description'   => '<p>[+description+]</p>',
        'email'         => '<input type="email" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',
        'time'         => '<input type="time" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',
        'week'         => '<input type="week" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',
        'url'         => '<input type="url" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',
        // used by the multicheck
        'fieldset'      => '<fieldset><legend>[+legend+]</legend>[+fields+]</fieldset>',
        'file'          => '<input type="file" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',  
        'hidden'        => '<input type="hidden" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',
        'label'         => '<label for="[+id+]">[+label+]</label>',
        'keygen'        => '<keygen name="[+name+]" id="[+id+]" [+extra+]>',
        'month'         => '<input type="month" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',        
        'multiselect'   => '<select name="[+name+][]" id="[+id+]" multiple="multiple" [+extra+]>[+options+]</select>',
        'multicheck'    => '<input type="checkbox" name="[+name+][]" id="[+id+]" value="[+value+]"[+is_checked+] [+extra+]/> [+label+]<br/>',
        'number'        => '<input type="number" name="[+name+]" id="[+id+]" min="[+min+]" max="[+max+]" value="[+value+]" [+extra+]/>',
        'optgroup'      => '<optgroup label="[+label+]">[+options+]</optgroup>',
        'option'        => '<option value="[+value+]"[+is_selected+]>[+label+]</option>',
        'output'        => '<output name="[+name+]" id="[+id+]" for="[+for+]" [+extra+]>[+value+]</output>',
        'password'      => '<input type="password" name="[+name+]" id="[+id+]" value="" [+extra+]/>',
        'radio'         => '<input type="radio" name="[+name+]" id="[+id+]" value="[+value+]"[+is_checked+] [+extra+]> [+label+]<br/>',
        'range'         => '<input type="range" id="[+id+]" name="[+name+] value="[+value+]" min="[+min+]" max="[+max+]" [+extra+]/>',
        'search'        => '<input type="search" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',
        'submit'        => '<input type="submit" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',
        'text'          => '<input type="text" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>',
        'textarea'      => '<textarea name="[+name+]" id="[+id+]" rows="[+rows+]" cols="[+cols+]" [+extra+]>[+value+]</textarea>',
    );

    /**
     * Generates final output
     */
    public function __toString() {
        return static::$output;
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
     * You want to do your own parsing thang? Set a callback here?
     *
     * @param mixed $callback any valid callback.
     */
    public static function setParser($callback) {
        static::$parser = $callback;
    }
    
    /**
     * Override any of our default formatting strings 
     *
     */
    public static function setTpl($name,$str) {
        static::$tpls[$name] = $str;
    }

    /**
     * Bulk Override of our default formatting strings 
     *
     */
    public static function setTpls($array) {
        static::$tpls = $array;
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
     * Used in method chaining: we return an instance of this object
     * so we can either print its result via __toString() or chain
     * additional methods onto it.
     *
     */
    public static function returnOutput($output,$args=array()) {
        if (empty(static::$instance)) {
            static::$instance = new Form();
        }
        if (static::$opened) {
            static::$output .= $output;
        }
        else {
            static::$output = $output;
        }
        return static::$instance;
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
     * We use a simple trick for checkboxes: add a hidden form field immediately prior to the checkbox. 
     * If the checkbox is not checked, the hidden field will submit its default value.
     *
     * Default behavior is to use 1|0 as checked|unchecked values.  If you want to use something different
     * then pass "checked_value" and "unchecked_value" in the $args.
     *
     * @param string $name
     * @param string $value -- current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function checkbox($name,$value=0,$args=array(),$tpl=null) {
        
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];    
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        if (!isset($args['checked_value'])) $args['checked_value'] = 1;
        if (!isset($args['unchecked_value'])) $args['unchecked_value'] = 0;
        
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);        
        $args['is_checked'] = '';
        if($value == $args['checked_value']) {
            $args['is_checked'] = ' checked="checked"';        
        }

        return self::parse($tpl,$args);
        
    }

    /**
     * Open a form.  Testing this with method chaining (i.e. fluid interface)
     *
     *
     */
    public static function open($args=array()) {
        if (static::$opened) static::$output = ''; // reset
        static::$opened = true;
        return static::returnOutput('<form>');
    }

    /**
     * File input. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function file($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

    /**
     * Datalist (HTML 5 only)
     *
     * @param string $name
     * @param array $data your data points
     * @param string $value -- current value     
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function datalist($name,$data=array(),$value='',$args=array(),$tpl=null) {
        
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['data_tpl'])) $args['data_tpl'] = static::$tpls['data'];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['data'] = '';
        
        foreach ($data as $k) {
            $opt_args = array('value' => htmlentities($k));
            $args['data'] .= self::parse($args['data_tpl'],$opt_args);                 
        }

        return self::parse($tpl,$args);        
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
     * @param string $value -- current value
     * @param array $args additional arguments including 'option_tpl' and 'optgroup_tpl' for granular format control.
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function dropdown($name,$options=array(),$value='',$args=array(),$tpl=null) {
        
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['option_tpl'])) $args['option_tpl'] = static::$tpls['option'];
        if (!isset($args['optgroup_tpl'])) $args['optgroup_tpl'] = static::$tpls['optgroup'];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['options'] = '';
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
        
        return self::parse($tpl,$args);

    }

    /**
     * Hidden field
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function hidden($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

    /**
     * keygen field (HTML 5 only)
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function keygen($name,$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = '';

        return self::parse($tpl,$args);        
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
     * @param string $name
     * @param array $options either a simple array or key/value hash or a complex array to define optgroup
     * @param array $value -- current values
     * @param array $args additional arguments including 'field_tpl' for granular format control.
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function multicheck($name,$options=array(),$values=array(),$args=array(),$tpl=null) {
        
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!is_array($values)) $values = array($values); // <-- catch typos
        if (!isset($args['fieldset_tpl'])) $args['fieldset_tpl'] = static::$tpls['fieldset'];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $output = '';
        // Complex with Fieldsets
        if (self::isComplex($options)) {
            foreach($options as $legend => $fields) {
                // <fieldset><legend>[+legend+]</legend>[+fields+]</fieldset>
                $fieldset_args = array('legend' => $legend, 'fields'=>'');
                // key/value sub-options
                if (self::isHash($fields)) {
                    foreach ($fields as $k => $v) {
                        $args['value'] = htmlentities($k);
                        $args['label'] = $v;
                        $args['is_checked'] = (in_array($k,$values))? ' checked="checked"': '';
                        $fieldset_args['fields'] .= self::parse($tpl,$args); 
                    }
                }
                // simple sub-options
                else {
                    foreach ($fields as $k) {
                        $args['value'] = htmlentities($k);
                        $args['label'] = $k;
                        $args['is_checked'] = (in_array($k,$values))? ' checked="checked"': '';
                        $fieldset_args['fields'] .= self::parse($tpl,$args);               
                    }
                } 
                $output .= self::parse($args['fieldset_tpl'],$fieldset_args);
            }
        }
        // Unique key/values
        elseif (self::isHash($options)) {
            foreach ($options as $k => $v) {
                $args['value'] = htmlentities($k);
                $args['label'] = $v;
                $args['is_checked'] = (in_array($k,$values))? ' checked="checked"': '';
                $output .= self::parse($tpl,$args); 
            }
        }
        // Simple options
        // <input type="checkbox" name="[+name+][]" id="[+id+]" value="[+checked_value+]" [+is_checked+][+extra+]/> [+label+]<br/>
        else {
            foreach ($options as $k) {
                $args['value'] = htmlentities($k);
                $args['label'] = $k;
                $args['is_checked'] = (in_array($k,$values))? ' checked="checked"': '';
                $output .= self::parse($tpl,$args);                 
            }
        }
        
        return $output;

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
    public static function multiselect($name,$options=array(),$values=array(),$args=array(),$tpl=null) {
        
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!is_array($values)) $values = array($values); // <-- catch typos
        if (!isset($args['option_tpl'])) $args['option_tpl'] = static::$tpls['option'];
        if (!isset($args['optgroup_tpl'])) $args['optgroup_tpl'] = static::$tpls['optgroup'];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['options'] = '';
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
        
        return self::parse($tpl,$args);

    }
    
    /**
     * Output field (HTML 5 only)
     * This should correspond to an "oninput" event in the <form> tag. 
     *
     * @param string $name
     * @param mixed $for specifies the element(s) used in the calculation (separated by space). If array, will implode.
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function output($name,$for='',$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['for'] = (is_array($for)) ? implode(' ',$for) : $for;
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }
    
    /**
     * Standard password field. Like text, but we don't pass a value. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function password($name,$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = '';

        return self::parse($tpl,$args);        
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
     * @param string $value -- current value
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function radio($name,$options=array(),$value='',$args=array(),$tpl=null) {
        
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $output = '';
        
        // Unique key/values
        if (self::isHash($options)) {
            foreach ($options as $k => $v) {
                $args['value'] = htmlentities($k);
                $args['label'] = trim($v);
                $args['is_checked'] = ($k == $value)? ' checked="checked"': '';
                $output .= self::parse($tpl,$args); 
            }
        }
        // Simple options
        else {
            foreach ($options as $k) {
                $args['value'] = htmlentities($k);
                $args['label'] = trim($k);
                $args['is_checked'] = ($k == $value)? ' checked="checked"': '';
                $output .= self::parse($tpl,$args);                 
            }
        }
        
        return $output;
    }


    /**
     * Range (HTML 5 only)
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function range($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        if (!isset($args['min'])) $args['min'] = 1;
        if (!isset($args['max'])) $args['max'] = 100;

        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }


    /**
     * Let there be text. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function text($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

    public static function test($name) {
        return static::returnOutput('<input name="'.$name.'">');    
    }

    /**
     * Standard textarea field.
     *
     * We set default values for "rows" and "cols".
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class     *
     */
    public static function textarea($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);
        if (!isset($args['rows'])) $args['rows'] = 4;
        if (!isset($args['cols'])) $args['cols'] = 40;
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        
        return self::parse($tpl,$args);   
    }

    /**
     * HTML5 color input. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function color($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

    /**
     * HTML5 date input. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function date($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

    /**
     * HTML5 datetime_local input. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function datetime_local($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

    /**
     * HTML5 email input. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function email($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

    /**
     * HTML5 month input. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function month($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

    /**
     * HTML5 number input. 
     *
     * @param string $name
     * @param string $value current value
     * @param int $min current min
     * @param int $max current max
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function number($name,$min=0,$max=10,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);
        $args['min'] = (int) $min;
        $args['max'] = (int) $max;

        return self::parse($tpl,$args);        
    }

    /**
     * HTML5 search input. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function search($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

    /**
     * HTML5 time input. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function time($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

    /**
     * HTML5 week input. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function week($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

    /**
     * HTML5 url input. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function url($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

    /**
     * HTML5 submit input. 
     *
     * @param string $name
     * @param string $value current value
     * @param array $args additional arguments
     * @param string $tpl defaults to tpl provided by the class
     */
    public static function submit($name,$value='',$args=array(),$tpl=null) {
        if (!$tpl) $tpl = static::$tpls[__FUNCTION__];
        if (!isset($args['id'])) $args['id'] = self::getId($name);
        $args['name'] = self::getName($name);
        $args['value'] = htmlentities($value);

        return self::parse($tpl,$args);        
    }

}
/*EOF*/