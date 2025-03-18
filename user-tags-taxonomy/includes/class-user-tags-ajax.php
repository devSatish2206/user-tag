<?php

if (!defined('ABSPATH')) exit;

class User_Tags_Ajax {
    public function __construct() {
        add_action('wp_ajax_search_user_tags', [$this, 'search_user_tags']);
    }



    public function search_user_tags() {
        $search = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
        $tags = get_terms(['taxonomy' => 'user_tag', 'search' => $search, 'hide_empty' => false]);

        $results = [];
        foreach ($tags as $tag) {
            $results[] = ['id' => $tag->slug, 'text' => $tag->name];
        }

        wp_send_json($results);
    }
}
