<?php
namespace Formbuilder\Element;
class Checkbox extends \Formbuilder\Element {

    /**
     * We use a simple trick for checkboxes: add a hidden form field immediately prior to the checkbox. 
     * If the checkbox is not checked, the hidden field will submit its default value.
     * Pass a "default" key in your $args to set the checkbox's unchecked value.
     *
     */
    public function draw($name,$value=0,$args=array(),$tpl='<input type="hidden" name="[+name+]" value="0"/><input type="hidden" name="[+name+]" id="[+id+]" value="1" [+is_checked+][+extra+]/>') {
        $args['name'] = $name;
        $args['value'] = htmlentities($value);
        if (!isset($args['id'])) {
            $args['id'] = \Formbuilder\Form::getId($name);
        }
        else {
            $args['id'] = \Formbuilder\Form::getId($args['name']);
        }
        if (!isset($args['default'])) $args['default'] = 0;
        
        $args['is_checked'] = '';
        if($value != $args['default']) {
            $args['is_checked'] = ' checked="checked"';        
        }

        return \Formbuilder\Form::parse($tpl,$args);
        
    }
}
/*EOF*/