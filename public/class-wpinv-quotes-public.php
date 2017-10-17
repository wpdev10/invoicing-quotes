<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpgeodirectory.com
 * @since      1.0.0
 *
 * @package    Wpinv_Quotes
 * @subpackage Wpinv_Quotes/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpinv_Quotes
 * @subpackage Wpinv_Quotes/public
 * @author     GeoDirectory Team <info@wpgeodirectory.com>
 */
class Wpinv_Quotes_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wpinv_Quotes_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wpinv_Quotes_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wpinv-quotes-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wpinv_Quotes_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wpinv_Quotes_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wpinv-quotes-public.js', array('jquery'), $this->version, false);

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function wpinv_quote_print_head_styles()
    {
        wp_register_style('wpinv-quotes-single-style', plugin_dir_url(__FILE__) . 'css/wpinv-quotes-public.css', array(), $this->version, 'all');
        wp_print_styles('wpinv-quotes-single-style');
    }

    /**
     * Display accept and decline buttons in top left corner of receipt
     *
     * @since    1.0.0
     * @param object $quote quote object
     */
    public function wpinv_quote_display_left_actions($quote)
    {
        if ('wpi_quote' != $quote->post_type || empty($quote->ID)) {
            return;
        }

        $accept_msg = wpinv_get_option('accepted_quote_message');
        $decline_msg = wpinv_get_option('declined_quote_message');
        
        if ($quote->post_status == 'wpi-quote-pending') {
            remove_query_arg('wpi_action');
            $quote_id = $quote->ID;
            ?>
            <button class="btn btn-success btn-sm accept-quote"
                    title="<?php esc_attr_e('Accept This Quotation', 'wpinv-quotes'); ?>"
                    onclick="showAlert('accept')"><?php _e('Accept Quotation', 'wpinv-quotes'); ?></button> &nbsp;
            <button class="btn btn-danger btn-sm decline-quote"
                    title="<?php esc_attr_e('Decline This Quotation', 'wpinv-quotes'); ?>"
                    onclick="showAlert('decline')"><?php _e('Decline Quotation', 'wpinv-quotes'); ?></button>
            <p id="accept-alert" class="alert alert-success"><?php _e('An invoice will be generated on acceptance. ') ?>
                <a class="btn btn-success btn-xs accept-quote"
                   title="<?php esc_attr_e('Accept This Quotation', 'wpinv-quotes'); ?>"
                   href="<?php echo Wpinv_Quotes_Shared::get_accept_quote_url($quote_id); ?>"><?php _e('Continue', 'wpinv-quotes'); ?></a>
            </p>
            <p id="decline-alert" class="alert alert-danger"><?php _e('You are going to decline this quote. ') ?> <a
                    class="btn btn-danger btn-xs decline-quote"
                    title="<?php esc_attr_e('Decline This Quotation', 'wpinv-quotes'); ?>"
                    href="<?php echo Wpinv_Quotes_Shared::get_decline_quote_url($quote_id); ?>"><?php _e('Continue', 'wpinv-quotes'); ?></a>
            <script>
                function showAlert(action) {
                    var x = document.getElementById('accept-alert');
                    var y = document.getElementById('decline-alert');
                    if (action == 'accept') {
                        y.style.display = 'none';
                        x.style.display = 'block';
                    } else {
                        x.style.display = 'none';
                        y.style.display = 'block';
                    }
                }
            </script>
            <?php
        } elseif ($quote->post_status == 'wpi-quote-accepted' && !empty($accept_msg)) { ?>
            <p class="btn-success btn-sm quote-front-msg"><?php _e($accept_msg, 'wpinv-quotes'); ?></p>
        <?php } elseif ($quote->post_status == 'wpi-quote-declined' && !empty($decline_msg)) { ?>
            <p class="btn-danger btn-sm quote-front-msg"><?php _e($decline_msg, 'wpinv-quotes'); ?></p>
            <?php
        }
    }

    /**
     * Display print quote and history buttons in top right corner of receipt
     *
     * @since    1.0.0
     * @param object $quote quote object
     */
    public function wpinv_quote_display_right_actions($quote)
    {
        if ('wpi_quote' == $quote->post_type) {
            $user_id = (int)$quote->get_user_id();
            $current_user_id = (int)get_current_user_id();

            if ($user_id > 0 && $user_id == $current_user_id) {
                ?>
                <a class="btn btn-primary btn-sm" onclick="window.print();"
                   href="javascript:void(0)"><?php _e('Print Quote', 'wpinv-quotes'); ?></a> &nbsp;
                <a class="btn btn-warning btn-sm"
                   href="<?php echo esc_url(Wpinv_Quotes_Shared::wpinv_get_quote_history_page_uri()); ?>"><?php _e('Quote History', 'wpinv-quotes'); ?></a>
            <?php }
        }
    }

    /**
     * Add custom quote status in queries
     *
     * @since    1.0.0
     */
    public function wpinv_quote_pre_get_posts( $wp_query ) {
        if ( !empty( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] == 'wpi_quote' && is_user_logged_in() && is_single() && $wp_query->is_main_query() ) {
            $wp_query->query_vars['post_status'] = array_keys( Wpinv_Quotes_Shared::wpinv_get_quote_statuses() );
        }

        return $wp_query;
    }

}
