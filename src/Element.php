<?php
namespace Formbuilder;

abstract class Element {

    /**
     * Draw the form element, e.g. a text field
     *
     * @param string $name
     * @param string $value
     * @param array $args any key/value pairs to pass to the $tpl
     * @param string $tpl formatting string
     */
    abstract public function draw($name,$value='',$args=array(),$tpl='');
}
/*EOF*/