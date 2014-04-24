<?php
namespace Formbuilder\Element;
class Text extends \Formbuilder\Element {

    public function draw($name,$value='',$args=array(),$tpl='<input type="text" name="[+name+]" id="[+id+]" value="[+value+]" [+extra+]/>') {
        $args['name'] = $name;
        $args['value'] = htmlentities($value);
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