<?php

if (!defined('ABSPATH')) exit;

class User_Tags_Taxonomy {
    public function __construct() {
        add_action('init', [$this, 'register_user_tags_taxonomy']);
    }

    public function register_user_tags_taxonomy() {
      
        $args = array(
            'labels' => array(
                'name'          => 'User Tags',
                'singular_name' => 'User Tag',
                'menu_name'     => 'User Tags',
                'add_new_item'  => 'Add New User Tag',
            ),
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'hierarchical' => false,
            'rewrite'      => false
        );

        register_taxonomy('user_tag', 'user', $args);
    }
}
