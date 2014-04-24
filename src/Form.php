<?php
namespace Formbuilder;

class Form {

    public static $parser = '\\Formbuilder\\Form::defaultParse';
    
    /**
     * For cleaner UI.
     *
     *
     *
     */
    public static function __callStatic($name,$args) {
        try {

            $classname = '\\Formbuilder\\Element\\'.$name;
            $Element = new $classname;
            
            $x = call_user_func_array(array($Element,'draw'), $args);
//            print $x; exit;
            return $x;
        }
        catch (Exception $e){
            return $e->getMessage();
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
     * Convert a wild string into something viable as a CSS id.
     *
     * @param string $str
     * @return string (filtered)
     */
    public static function getId($str) {
        if (!is_scalar($str)) return '';
        return preg_replace('/[^a-zA-Z0-9\-\_]/','_',$str);
    }
    

}
/*EOF*/