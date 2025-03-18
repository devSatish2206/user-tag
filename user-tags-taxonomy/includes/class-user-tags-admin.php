<?php

if (!defined('ABSPATH')) exit;

class User_Tags_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_user_tags_menu']); 
        add_action('show_user_profile', [$this, 'add_user_tags_field']);
        add_action('edit_user_profile', [$this, 'add_user_tags_field']);
        add_action('user_new_form', [$this, 'add_user_tags_field']);
        add_action('personal_options_update', [$this, 'save_user_tags']);
        add_action('edit_user_profile_update', [$this, 'save_user_tags']);
        add_action('user_register', [$this, 'save_user_tags']);
    }

    public function add_user_tags_menu() {
        add_users_page(
            'User Tags',       // Page title
            'User Tags',       // Menu title
            'manage_options',  // Capability
            'edit-tags.php?taxonomy=user_tag'
        );
    }

    public function add_user_tags_field($user) {
        $user_tags = get_terms(['taxonomy' => 'user_tag', 'hide_empty' => false]);
        $user_tag_ids = is_object($user) ? wp_get_object_terms($user->ID, 'user_tag', ['fields' => 'ids']) : [];

        ?>
        <h3>User Tags</h3>
        <table class="form-table">
            <tr>
                <th><label for="user_tags">Select Tags</label></th>
                <td>
                    <select name="user_tags[]" id="user_tags" multiple style="width: 100%;">
                        <?php foreach ($user_tags as $tag) : ?>
                            <option value="<?php echo esc_attr($tag->term_id); ?>" 
                                <?php echo in_array($tag->term_id, $user_tag_ids) ? 'selected' : ''; ?>>
                                <?php echo esc_html($tag->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Assign tags to the user.</p>
                </td>
            </tr>
        </table>

        <script>
            jQuery(document).ready(function($) {
                $('#user_tags').select2();
            });
        </script>
        <?php
    }

    public function save_user_tags($user_id) {
        if (!current_user_can('edit_user', $user_id)) return;

        if (isset($_POST['user_tags'])) {
            $tag_ids = array_map('intval', $_POST['user_tags']);
            wp_set_object_terms($user_id, $tag_ids, 'user_tag', false);
        } else {
            wp_set_object_terms($user_id, [], 'user_tag', false);
        }
    }
}
