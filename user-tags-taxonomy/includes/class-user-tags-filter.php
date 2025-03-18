<?php

if (!defined('ABSPATH')) exit;

class User_Tags_Filter {
    public function __construct() {
        add_action('restrict_manage_users', [$this, 'add_user_tag_filter_dropdown']);
        add_filter('pre_get_users', [$this, 'filter_users_by_tag']); // Fixed function name
        add_action('wp_ajax_get_user_tags', [$this, 'get_user_tags_ajax']);
    }

    // Add AJAX-powered Select2 dropdown
    public function add_user_tag_filter_dropdown($which) {
        if ($which !== 'top') return;

        $selected = isset($_GET['user_tag']) ? sanitize_text_field($_GET['user_tag']) : '';

        // Fetch all User Tags
        $terms = get_terms([
            'taxonomy'   => 'user_tag',
            'hide_empty' => false,
        ]);

        echo '<select name="user_tag" id="user_tag_filter" class="user-tag-dropdown" style="width: 200px;">';
        echo '<option value="">Select User Tag</option>'; // Default option

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                printf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($term->slug),
                    selected($selected, $term->slug, false),
                    esc_html($term->name)
                );
            }
        }

        echo '</select>';
        echo '<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">';

    }
    
  public function filter_users_by_tag($query) {
   if (empty($_GET['user_tag'])) {
          return;
      }

      $user_tag = sanitize_text_field($_GET['user_tag']);
      $term = get_term_by('slug', $user_tag, 'user_tag');

      if ($term) {
          $user_ids = get_objects_in_term($term->term_id, 'user_tag');
          error_log('User IDs: ' . implode(',', $user_ids));

          if (!empty($user_ids) && !is_wp_error($user_ids)) {
              $query->set('include', $user_ids);
          } else {
              $query->set('include', [0]);
          }
      }
  }



    // AJAX Handler to fetch user tags dynamically
    public function get_user_tags_ajax() {
        check_ajax_referer('get_user_tags_nonce', 'nonce');

        $search = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
        $args = [
            'taxonomy'   => 'user_tag',
            'hide_empty' => false,
            'search'     => $search
        ];

        $tags = get_terms($args);
        $results = [];

        if (!empty($tags) && !is_wp_error($tags)) {
            foreach ($tags as $tag) {
                $results[] = [
                    'id'   => $tag->slug,
                    'text' => $tag->name
                ];
            }
        }

        wp_send_json($results);
        wp_die(); // Ensure clean AJAX response
    }
}
