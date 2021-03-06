<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wpgeodirectory.com
 * @since      1.0.0
 *
 * @package    Wpinv_Quotes
 * @subpackage Wpinv_Quotes/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wpinv_Quotes
 * @subpackage Wpinv_Quotes/includes
 * @author     GeoDirectory Team <info@wpgeodirectory.com>
 */
class Wpinv_Quotes_Activator
{

    /**
     * Actions on add-on activated.
     *
     * @since    1.0.0
     */
    public static function activate($network_wide = false)
    {
        global $wpdb;

        $cap_type = 'wpi_quote';
        $plural = __('Quotes', 'wpinv-quotes');
        $single = __('Quote', 'wpinv-quotes');
        $menu_icon = WPINV_QUOTES_URL . '/assets/images/favicon.ico';
        $menu_icon = apply_filters('wpinv_menu_icon_quotes', $menu_icon);

        $opts['can_export'] = TRUE;
        $opts['capability_type'] = $cap_type;
        $opts['description'] = '';
        $opts['exclude_from_search'] = TRUE;
        $opts['has_archive'] = FALSE;
        $opts['hierarchical'] = FALSE;
        $opts['map_meta_cap'] = TRUE;
        $opts['menu_icon'] = $menu_icon;
        $opts['public'] = TRUE;
        $opts['publicly_querable'] = TRUE;
        $opts['query_var'] = TRUE;
        $opts['register_meta_box_cb'] = '';
        $opts['rewrite'] = FALSE;
        $opts['show_in_admin_bar'] = TRUE;
        $opts['show_in_menu'] = current_user_can( 'manage_invoicing' ) ? 'wpinv' : true;
        $opts['show_in_nav_menu'] = TRUE;
        $opts['show_ui'] = TRUE;
        $opts['supports'] = array('title');
        $opts['taxonomies'] = array('');

        $opts['capabilities']['delete_others_posts'] = "delete_others_{$cap_type}s";
        $opts['capabilities']['delete_post'] = "delete_{$cap_type}";
        $opts['capabilities']['delete_posts'] = "delete_{$cap_type}s";
        $opts['capabilities']['delete_private_posts'] = "delete_private_{$cap_type}s";
        $opts['capabilities']['delete_published_posts'] = "delete_published_{$cap_type}s";
        $opts['capabilities']['edit_others_posts'] = "edit_others_{$cap_type}s";
        $opts['capabilities']['edit_post'] = "edit_{$cap_type}";
        $opts['capabilities']['edit_posts'] = "edit_{$cap_type}s";
        $opts['capabilities']['edit_private_posts'] = "edit_private_{$cap_type}s";
        $opts['capabilities']['edit_published_posts'] = "edit_published_{$cap_type}s";
        $opts['capabilities']['publish_posts'] = "publish_{$cap_type}s";
        $opts['capabilities']['read_post'] = "read_{$cap_type}";
        $opts['capabilities']['read_private_posts'] = "read_private_{$cap_type}s";

        $opts['labels']['add_new'] = __("Add New {$single}", 'wpinv-quotes');
        $opts['labels']['add_new_item'] = __("Add New {$single}", 'wpinv-quotes');
        $opts['labels']['all_items'] = __($plural, 'wpinv-quotes');
        $opts['labels']['edit_item'] = __("Edit {$single}", 'wpinv-quotes');
        $opts['labels']['menu_name'] = __($plural, 'wpinv-quotes');
        $opts['labels']['name'] = __($plural, 'wpinv-quotes');
        $opts['labels']['name_admin_bar'] = __($single, 'wpinv-quotes');
        $opts['labels']['new_item'] = __("New {$single}", 'wpinv-quotes');
        $opts['labels']['not_found'] = __("No {$plural} Found", 'wpinv-quotes');
        $opts['labels']['not_found_in_trash'] = __("No {$plural} Found in Trash", 'wpinv-quotes');
        $opts['labels']['parent_item_colon'] = __("Parent {$plural} :", 'wpinv-quotes');
        $opts['labels']['search_items'] = __("Search {$plural}", 'wpinv-quotes');
        $opts['labels']['singular_name'] = __($single, 'wpinv-quotes');
        $opts['labels']['view_item'] = __("View {$single}", 'wpinv-quotes');

        $opts['rewrite']['slug'] = FALSE;
        $opts['rewrite']['with_front'] = FALSE;
        $opts['rewrite']['feeds'] = FALSE;
        $opts['rewrite']['pages'] = FALSE;

        $opts = apply_filters('wpinv_quote_params', $opts);

        register_post_type('wpi_quote', $opts);

        flush_rewrite_rules();

        if ( is_multisite() && $network_wide ) {
            foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {
                switch_to_blog( $blog_id );
                update_option( 'activated_quotes', 'wpinv-quotes' );
                restore_current_blog();
            }
        } else {
            update_option( 'activated_quotes', 'wpinv-quotes' );
        }

        do_action( 'wpinv_quote_activated' );
    }

}
