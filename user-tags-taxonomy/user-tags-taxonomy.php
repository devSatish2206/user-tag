<?php
/**
 * Plugin Name: User Tags Taxonomy
 * Description: Registers a custom taxonomy "User Tags" for users and provides an admin UI.
 * Version: 1.1.0
 * Author: Satish Patidar
 * Author URI: Your Website
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define Constants
define('USER_TAGS_VERSION', '1.0.0');
define('USER_TAGS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('USER_TAGS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include Required Files
require_once USER_TAGS_PLUGIN_DIR . 'includes/class-user-tags-taxonomy.php';
require_once USER_TAGS_PLUGIN_DIR . 'includes/class-user-tags-admin.php';
require_once USER_TAGS_PLUGIN_DIR . 'includes/class-user-tags-filter.php';
require_once USER_TAGS_PLUGIN_DIR . 'includes/class-user-tags-ajax.php';

// Initialize Plugin Classes
function user_tags_plugin_init() {
    new User_Tags_Taxonomy();
    new User_Tags_Admin();
    new User_Tags_Filter();
    new User_Tags_Ajax();
   
    add_action('admin_enqueue_scripts', 'user_tags_admin_enqueue_scripts');
     add_action('admin_footer', 'user_tags_admin_footer');


}

function user_tags_activate() {
   
}
register_activation_hook(__FILE__, 'user_tags_activate');

/*function user_tags_deactivate() {
   
}*/

register_deactivation_hook(__FILE__, 'user_tags_uninstall');

function user_tags_uninstall() {
    global $wpdb;

    // Confirm Message (This won't show in UI, but logs can be checked)
    error_log('User Tag taxonomy and all related data are being deleted.');

    // Delete all terms from 'user_tag' taxonomy
    $terms = get_terms([
        'taxonomy'   => 'user_tag',
        'hide_empty' => false,
    ]);

    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            wp_delete_term($term->term_id, 'user_tag');
        }
    }

    // Delete term relationships from DB
    $wpdb->query("DELETE FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN (SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE taxonomy = 'user_tag')");
    
    // Delete taxonomy from term_taxonomy table
    $wpdb->delete($wpdb->term_taxonomy, ['taxonomy' => 'user_tag']);

    // Delete taxonomy from terms table
    $wpdb->query("DELETE FROM {$wpdb->terms} WHERE term_id NOT IN (SELECT term_id FROM {$wpdb->term_taxonomy})");
     unregister_taxonomy('user_tag');
}


add_action('plugins_loaded', 'user_tags_plugin_init');

// Enqueue Scripts
function user_tags_admin_enqueue_scripts() {
    // Enqueue Select2 CSS & JS
    wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
    wp_enqueue_style('custom-css', USER_TAGS_PLUGIN_URL . 'assets/css/style.css');
    wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js', ['jquery'], null, true);
    // Enqueue custom script for User Tags filter
    wp_enqueue_script('user-tags-filter-js', USER_TAGS_PLUGIN_URL . 'assets/js/user-tags-filter.js', ['jquery', 'select2-js'], null, true);

    // Localize script for AJAX
    wp_localize_script('user-tags-filter-js', 'userTagsAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('get_user_tags_nonce')
    ]);
}


function user_tags_admin_footer() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#delete-user-tags-taxonomy').on('click', function(e) {
                var confirmation = confirm("Are you sure you want to delete this plugin? All your data will be deleted.");
                if (!confirmation) {
                    e.preventDefault();
                }
            });
        });
    </script>
    <?php
}