<?php
namespace Formbuilder\Element;
class Textarea extends \Formbuilder\Element {

    public function draw($name,$value='',$args=array()
        ,$tpl='<textarea name="[+name+]" id="[+id+]" rows="[+rows+]" cols="[+cols+]" [+extra+]>[+value+]</textarea>') {
        $args['name'] = $name;
        $args['value'] = htmlentities($value);
        if (!isset($args['rows'])) $args['rows'] = 4;
        if (!isset($args['cols'])) $args['cols'] = 40;
        if (!isset($args['id'])) {
            $args['id'] = \Formbuilder\Form::getId($name);
        }
        else {
            $args['id'] = \Formbuilder\Form::getId($args['name']);
        }
        
        return \Formbuilder\Form::parse($tpl,$args);
        
    }
}
/*EOF*/