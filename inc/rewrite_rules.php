<?php
add_filter("product_cat_rewrite_rules","td_product_category_rewrite");


function td_product_category_rewrite($rules)
{

    $args = array(
        "hide_empty"=>0,
        'taxonomy'=> 'product_cat',
        //'parent'=>0,
    );

    $categories = get_categories($args);
    if(is_array($categories) && !empty($categories))
    {
        $categs_slugs=array();



        /** @var WP_Term $categs */
        foreach($categories as $categs)
        {
            if(is_object($categs) && !is_wp_error($categs)) {


                    $slug = get_term_parents_list($categs->term_id,'product_cat',array(
                        'separator' => '/',
                        'link'      => false,
                        'format'    => 'slug',
                    ));

                $categs_slugs[] = !empty($slug)?preg_replace('/\/$/','',$slug):$categs->slug;
            }


        }

        if(!empty($categs_slugs))
        {
            $rules = array();
            foreach($categs_slugs as $slug)
            {
               // $rules['('.$slug.')'.'/([^/]+)/?$'] = 'index.php?product_cat=$matches[1]&place=$matches[2]';
                $rules['('.$slug.')'.'/feed/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?product_cat=$matches[1]&feed=$matches[2]';
                $rules['('.$slug.')'.'/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?product_cat=$matches[1]&feed=$matches[2]';
                $rules['('.$slug.')'.'/embed/?$'] = 'index.php?product_cat=$matches[1]&embed=true';
                $rules['('.$slug.')'.'/page/?([0-9]{1,})/?$'] = 'index.php?product_cat=$matches[1]&paged=$matches[2]';
                $rules['('.$slug.')'.'/?$'] = 'index.php?product_cat=$matches[1]';

            }
        }
    }

    return $rules;
}



add_filter("post_link",'td_rewrite_post_title',10,3);
add_filter('post_type_link','td_rewrite_post_title',10,3);

function td_rewrite_post_title($permalink, $post, $leavename )
{


   if($post->post_type=='product') {
        if (get_query_var('product_cat')==null) {
            $term = get_the_terms($post->ID, 'product_cat');
            if($term) {
                foreach ($term as $t) {
                    $matches = array();
                    if (preg_match('/' . $t->slug . '/', $permalink, $matches) == 1) {
                        $permalink = preg_replace('/' . $t->slug . '/', $t->slug, $permalink);
                        break;
                    }

                }
            }
        }
        else
        {
            $permalink = home_url()."/".$post->post_type."/".$post->post_name;
            //var_dump($permalink);
        }
        return $permalink;
    }


    return $permalink;
}

add_filter("wp_get_nav_menu_items",'td_rewrite_menu_items',10,3);

function td_rewrite_menu_items($items,$menu,$args)
{

    /** @var WP_Post $t */
    foreach($items as $t)
    {
        if($t->post_type=='nav_menu_item' && $t->object=='product_cat' && $t->type=='taxonomy')
        {
            $term = get_term($t->object_id,$t->object);
            $term->description = '';
            $slig = get_term_parents_list($t->object_id,$t->object,array('separator' => '/',
                'link'      => false,
                'format'    => 'slug'));
            $t->url = home_url().'/'.$slug;

        }
    }
    return $items;
}

function td_filter_product_categories($output,$args)
{
    return $output;
}
add_filter('wp_list_categories','td_filter_product_categories',10,2);
function td_filter_product_Category_link($link,$post_type)
{

    return $link;
}
add_filter('post_type_archive_link','td_filter_product_category_link',10,2);

function td_filter_product_category_link2($termlink, $term, $taxonomy)
{

    if($taxonomy=='product_cat')
        $termlink = str_replace("product-category/",'',$termlink);


    return $termlink;
}
add_filter('term_link','td_filter_product_category_link2',10,3);
