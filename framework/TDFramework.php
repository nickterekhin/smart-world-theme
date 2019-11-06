<?php
namespace framework;

class TDFramework
{
    function getTitleImageACF($img, $fieldName,$term_id,$taxonomy)
    {
        $res = false;
        if(function_exists("get_field")) {
            $res = get_field($fieldName, $taxonomy . '_' . $term_id);


        }
        return $res?$res:$img;
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
