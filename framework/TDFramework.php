<?php
namespace framework;

class TDFramework
{
    function getTitleImageACF($img, $fieldName,$term_id,$taxonomy)
    {
        if(function_exists("get_field"))
            return get_field($fieldName,$taxonomy.'_'.$term_id);
        return $img;
    }
}

function TD()
{
    global $TD;

    if(!isset($TD))
    {
        $TD = new TDFramework();
    }
    return $TD;
}

$GLOBALS['TD'] = TD();
