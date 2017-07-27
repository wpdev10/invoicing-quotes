<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpgeodirectory.com
 * @since      1.0.0
 *
 * @package    Wpinv_Quotes
 * @subpackage Wpinv_Quotes/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpinv_Quotes
 * @subpackage Wpinv_Quotes/admin
 * @author     GeoDirectory Team <info@wpgeodirectory.com>
 */
class Wpinv_Quotes_Admin
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
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wpinv-quotes-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        global $pagenow, $post;

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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wpinv-quotes-admin.js', array('jquery'), $this->version, false);

        $localize = array();
        if (isset($post->ID) && $post->post_type == 'wpi_quote' && ($pagenow == 'post-new.php' || $pagenow == 'post.php')) {
            wp_enqueue_script('jquery-ui-datepicker');
            $localize['convert_quote'] = __('Are you sure you want to convert from Quote to Invoice?', 'invoicing');
            $localize['save_quote'] = __('Save Quote', 'invoicing');
        }
        wp_localize_script($this->plugin_name, 'wpinv_quotes_admin', $localize);

    }

    /**
     * Creates a new custom post type
     *
     * @since    1.0.0
     */
    public function wpinv_quote_new_cpt()
    {

        $cap_type = 'post';
        $plural = __('Quotes', 'invoicing');
        $single = __('Quote', 'invoicing');
        $menu_icon = WPINV_PLUGIN_URL . '/assets/images/favicon.ico';
        $menu_icon = apply_filters('wpinv_menu_icon_quotes', $menu_icon);

        $opts['can_export'] = TRUE;
        $opts['capability_type'] = $cap_type;
        $opts['description'] = '';
        $opts['exclude_from_search'] = TRUE;
        $opts['has_archive'] = FALSE;
        $opts['hierarchical'] = TRUE;
        $opts['map_meta_cap'] = TRUE;
        $opts['menu_icon'] = $menu_icon;
        $opts['public'] = TRUE;
        $opts['publicly_querable'] = TRUE;
        $opts['query_var'] = TRUE;
        $opts['register_meta_box_cb'] = '';
        $opts['rewrite'] = FALSE;
        $opts['show_in_admin_bar'] = TRUE;
        $opts['show_in_menu'] = "wpinv";
        $opts['show_in_nav_menu'] = TRUE;
        $opts['show_ui'] = TRUE;
        $opts['supports'] = array('title', 'comments');
        $opts['taxonomies'] = array('quote_status');

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

        $opts['labels']['add_new'] = __("Add New {$single}", 'invoicing');
        $opts['labels']['add_new_item'] = __("Add New {$single}", 'invoicing');
        $opts['labels']['all_items'] = __($plural, 'invoicing');
        $opts['labels']['edit_item'] = __("Edit {$single}", 'invoicing');
        $opts['labels']['menu_name'] = __($plural, 'invoicing');
        $opts['labels']['name'] = __($plural, 'invoicing');
        $opts['labels']['name_admin_bar'] = __($single, 'invoicing');
        $opts['labels']['new_item'] = __("New {$single}", 'invoicing');
        $opts['labels']['not_found'] = __("No {$plural} Found", 'invoicing');
        $opts['labels']['not_found_in_trash'] = __("No {$plural} Found in Trash", 'invoicing');
        $opts['labels']['parent_item_colon'] = __("Parent {$plural} :", 'invoicing');
        $opts['labels']['search_items'] = __("Search {$plural}", 'invoicing');
        $opts['labels']['singular_name'] = __($single, 'invoicing');
        $opts['labels']['view_item'] = __("View {$single}", 'invoicing');

        $opts['rewrite']['slug'] = FALSE;
        $opts['rewrite']['with_front'] = FALSE;
        $opts['rewrite']['feeds'] = FALSE;
        $opts['rewrite']['pages'] = FALSE;

        $opts = apply_filters('wpinv_quote_params', $opts);

        register_post_type('wpi_quote', $opts);

    }

    /**
     * Display columns in admin side quote listing
     *
     * @since    1.0.0
     */
    function wpinv_quote_columns($columns)
    {
        $columns = array(
            'cb' => $columns['cb'],
            'ID' => __('ID', 'invoicing'),
            'details' => __('Details', 'invoicing'),
            'customer' => __('Customer', 'invoicing'),
            'amount' => __('Amount', 'invoicing'),
            'quote_date' => __('Date', 'invoicing'),
            'status' => __('Status', 'invoicing'),
            'wpi_actions' => __('Actions', 'invoicing'),
        );

        return apply_filters('wpi_quote_table_columns', $columns);
    }

    /**
     * Remove bulk edit option from admin side quote listing
     *
     * @since    1.0.0
     */
    function wpinv_quote_bulk_actions($actions)
    {
        if (isset($actions['edit'])) {
            unset($actions['edit']);
        }

        return $actions;
    }

    function wpinv_quote_sortable_columns($columns)
    {
        $columns = array(
            'ID' => array('ID', true),
            'amount' => array('amount', false),
            'quote_date' => array('date', false),
            'customer' => array('customer', false),
            'status' => array('status', false),
        );

        return apply_filters('wpi_quote_table_sortable_columns', $columns);
    }

    /**
     * Display custom columns for admin side quote listing
     *
     * @since    1.0.0
     */
    function wpinv_quote_posts_custom_column($column_name, $post_id = 0)
    {
        global $post, $wpi_invoice;

        if (empty($wpi_invoice) || (!empty($wpi_invoice) && $post->ID != $wpi_invoice->ID)) {
            $wpi_invoice = new WPInv_Invoice($post->ID);
        }

        $value = NULL;

        switch ($column_name) {
            case 'email' :
                $value = $wpi_invoice->get_email();
                break;
            case 'customer' :
                $customer_name = $wpi_invoice->get_user_full_name();
                $customer_name = $customer_name != '' ? $customer_name : __('Customer', 'invoicing');
                $value = '<a href="' . esc_url(get_edit_user_link($wpi_invoice->get_user_id())) . '">' . $customer_name . '</a>';
                if ($email = $wpi_invoice->get_email()) {
                    $value .= '<br><a class="email" href="mailto:' . $email . '">' . $email . '</a>';
                }
                break;
            case 'amount' :
                echo $wpi_invoice->get_total(true);
                break;
            case 'quote_date' :
                $date_format = get_option('date_format');
                $time_format = get_option('time_format');
                $date_time_format = $date_format . ' ' . $time_format;

                $t_time = get_the_time($date_time_format);
                $m_time = $post->post_date;
                $h_time = mysql2date($date_format, $m_time);

                $value = '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
                break;
            case 'status' :
                $value = $wpi_invoice->get_status(true) . ($wpi_invoice->is_recurring() && $wpi_invoice->is_parent() ? ' <span class="wpi-suffix">' . __('(r)', 'invoicing') . '</span>' : '');
                break;
            case 'details' :
                $edit_link = get_edit_post_link($post->ID);
                $value = '<a href="' . esc_url($edit_link) . '">' . __('View Quote Details', 'invoicing') . '</a>';
                break;
            case 'wpi_actions' :
                $value = '';
                if (!empty($post->post_name)) {
                    $value .= '<a title="' . esc_attr__('Print quote', 'invoicing') . '" href="' . esc_url(get_permalink($post->ID)) . '" class="button ui-tip column-act-btn" title="" target="_blank"><span class="dashicons dashicons-print"><i style="" class="fa fa-print"></i></span></a>';
                }

                if ($email = $wpi_invoice->get_email()) {
                    $value .= '<a title="' . esc_attr__('Send quote to customer', 'invoicing') . '" href="' . esc_url(add_query_arg(array('wpi_action' => 'send_quote', 'quote_id' => $post->ID))) . '" class="button ui-tip column-act-btn"><span class="dashicons dashicons-email-alt"></span></a>';
                }

                break;
            default:
                $value = isset($post->$column_name) ? $post->$column_name : '';
                break;

        }
        $value = apply_filters('wpinv_payments_table_column', $value, $post->ID, $column_name);

        if ($value !== NULL) {
            echo $value;
        }
    }

    /**
     * Remove all post row actions for quote post type
     *
     * @since    1.0.0
     */
    function wpinv_quote_post_row_actions($actions, $post)
    {
        if ($post->post_type == 'wpi_quote' && !empty($post->ID)) {
            $actions = array();
        }
        return $actions;
    }

    /**
     * Add metaboxes for quote post type
     *
     * @since    1.0.0
     */
    function wpinv_quoute_add_meta_boxes($post_type, $post)
    {
        global $wpi_mb_invoice;
        if ($post_type == 'wpi_quote' && !empty($post->ID)) {
            $wpi_mb_invoice = wpinv_get_invoice($post->ID);
            add_meta_box('wpinv-details', __('Quote Details', 'invoicing'), 'WPInv_Meta_Box_Details::output', 'wpi_quote', 'side', 'default');
            add_meta_box('wpinv-address', __('Billing Details', 'invoicing'), 'WPInv_Meta_Box_Billing_Details::output', 'wpi_quote', 'normal', 'high');
            add_meta_box('wpinv-items', __('Quote Items', 'invoicing'), 'WPInv_Meta_Box_Items::output', 'wpi_quote', 'normal', 'high');
            add_meta_box('wpinv-notes', __('Quote Notes', 'invoicing'), 'WPInv_Meta_Box_Notes::output', 'wpi_quote', 'normal', 'high');
            if (!empty($wpi_mb_invoice) && !$wpi_mb_invoice->has_status(array('draft', 'auto-draft'))) {
                add_meta_box('wpinv-mb-resend-invoice', __('Resend Quote', 'invoicing'), 'WPInv_Meta_Box_Details::resend_invoice', 'wpi_quote', 'side', 'high');
            }
        }
    }

    /**
     * Change resend quote metabox params text values
     *
     * @since    1.0.0
     */
    function wpinv_quote_resend_quote_metabox_text($text)
    {
        global $post;
        if ($post->post_type == 'wpi_quote' && !empty($post->ID)) {
            $text = array(
                'message' => esc_attr__('This will send a copy of the quote to the customer&#8217;s email address.', 'invoicing'),
                'button_text' => __('Resend Quote', 'invoicing'),
            );
        }
        return $text;
    }

    /**
     * Change resend quote button url
     *
     * @since    1.0.0
     */
    function wpinv_quote_resend_quote_email_actions($email_actions)
    {
        global $post;
        if ($post->post_type == 'wpi_quote' && !empty($post->ID)) {
            $email_actions['email_url'] = add_query_arg(array('wpi_action' => 'send_quote', 'quote_id' => $post->ID));
        }
        return $email_actions;
    }

    /**
     * Change quote details metabox input labels
     *
     * @since    1.0.0
     */
    function wpinv_quote_detail_metabox_titles($title, $post)
    {
        if ($post->post_type == 'wpi_quote' && !empty($post->ID)) {
            $title['status'] = __('Quote Status:', 'invoicing');
            $title['number'] = __('Quote Number:', 'invoicing');
        }
        return $title;
    }

    /**
     * Change quote details metabox mail notice
     *
     * @since    1.0.0
     */
    function wpinv_quote_metabox_mail_notice($mail_notice, $post)
    {
        if ($post->post_type == 'wpi_quote' && !empty($post->ID)) {
            $mail_notice = __('After saveing quote this will send a copy of the quote to the user&#8217;s email address.', 'invoicing');
        }
        return $mail_notice;

    }

    /**
     * Register new statuses for quote
     *
     * @since    1.0.0
     */
    function wpinv_quote_register_post_status()
    {
        register_post_status('wpi-quote-declined', array(
            'label' => _x('Declined', 'Quote status', 'invoicing'),
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Declined <span class="count">(%s)</span>', 'Declined <span class="count">(%s)</span>', 'invoicing')
        ));
        register_post_status('wpi-quote-cancelled', array(
            'label' => _x('Cancelled', 'Quote status', 'invoicing'),
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'invoicing')
        ));
        register_post_status('wpi-quote-sent', array(
            'label' => _x('Sent', 'Quote status', 'invoicing'),
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Sent <span class="count">(%s)</span>', 'Sent <span class="count">(%s)</span>', 'invoicing')
        ));
    }

    /**
     * Add statuses to the dropdwn in quote details metabox
     *
     * @since    1.0.0
     */
    function wpinv_quote_statuses($quote_statuses)
    {
        global $post;
        if ($post->post_type == 'wpi_quote' && !empty($post->ID)) {
            $quote_statuses = array(
                'pending' => __('Draft', 'invoicing'),
                'publish' => __('Accepted', 'invoicing'),
                'wpi-quote-declined' => __('Declined', 'invoicing'),
                'wpi-quote-cancelled' => __('Cancelled', 'invoicing'),
                'wpi-quote-sent' => __('Sent', 'invoicing')
            );
            $quote_statuses = apply_filters('wpinv_quote_statuses', $quote_statuses);
        }
        return $quote_statuses;
    }

    /**
     * Add customer quote email settings
     *
     * @since    1.0.0
     */
    function wpinv_quote_mail_settings($emails)
    {
        $user_quote = array(
            'email_user_quote_header' => array(
                'id' => 'email_user_quote_header',
                'name' => '<h3>' . __('Customer Quote', 'invoicing') . '</h3>',
                'desc' => __('Customer Quote email can be sent to customers containing their quote information.', 'invoicing'),
                'type' => 'header',
            ),
            'email_user_quote_active' => array(
                'id' => 'email_user_quote_active',
                'name' => __('Enable/Disable', 'invoicing'),
                'desc' => __('Enable this email notification', 'invoicing'),
                'type' => 'checkbox',
                'std' => 1
            ),
            'email_user_quote_subject' => array(
                'id' => 'email_user_quote_subject',
                'name' => __('Subject', 'invoicing'),
                'desc' => __('Enter the subject line for the quote receipt email.', 'invoicing'),
                'type' => 'text',
                'std' => __('[{site_title}] Your quote from {invoice_date}', 'invoicing'),
                'size' => 'large'
            ),
            'email_user_quote_heading' => array(
                'id' => 'email_user_quote_heading',
                'name' => __('Email Heading', 'invoicing'),
                'desc' => __('Enter the the main heading contained within the email notification for the quote receipt email.', 'invoicing'),
                'type' => 'text',
                'std' => __('Your quote {invoice_number} details', 'invoicing'),
                'size' => 'large'
            ),
            'email_user_quote_admin_bcc' => array(
                'id' => 'email_user_quote_admin_bcc',
                'name' => __('Enable Admin BCC', 'invoicing'),
                'desc' => __('Check if you want to send this notification email to site Admin.', 'invoicing'),
                'type' => 'checkbox',
                'std' => 1
            ),
        );

        $emails['user_quote'] = $user_quote;

        return $emails;
    }

    /**
     * Send customer quote email notification if Send Quote is selected "yes"
     *
     * @since    1.0.0
     */
    function wpinv_send_quote_after_save($post_id)
    {

        if (wp_is_post_revision($post_id)) {
            return;
        }

        if (!current_user_can('manage_options') || get_post_type($post_id) != 'wpi_quote') {
            return;
        }

        if (!empty($_POST['wpi_save_send'])) {
            $this->wpinv_user_quote_notification($post_id);
        }
    }

    /**
     * Function to send customer quote email notification to customer
     *
     * @since    1.0.0
     */
    function wpinv_user_quote_notification($quote_id)
    {
        global $wpinv_email_search, $wpinv_email_replace;

        $email_type = 'user_quote';

        $quote = wpinv_get_invoice($quote_id);
        if (empty($quote)) {
            return false;
        }

        $recipient = wpinv_email_get_recipient($email_type, $quote_id, $quote);
        if (!is_email($recipient)) {
            return false;
        }

        $search = array();
        $search['invoice_number'] = '{invoice_number}';
        $search['invoice_date'] = '{invoice_date}';
        $search['name'] = '{name}';

        $replace = array();
        $replace['invoice_number'] = $quote->get_number();
        $replace['invoice_date'] = $quote->get_invoice_date();
        $replace['name'] = $quote->get_user_full_name();

        $wpinv_email_search = $search;
        $wpinv_email_replace = $replace;

        $subject = wpinv_email_get_subject($email_type, $quote_id, $quote);
        $email_heading = wpinv_email_get_heading($email_type, $quote_id, $quote);
        $headers = wpinv_email_get_headers($email_type, $quote_id, $quote);
        $attachments = wpinv_email_get_attachments($email_type, $quote_id, $quote);

        $args = array(
            'invoice' => $quote,
            'email_type' => $email_type,
            'email_heading' => $email_heading,
            'sent_to_admin' => false,
            'plain_text' => false,
        );
        $content = wpinv_get_template_html(
            'emails/wpinv-email-user_quote.php',
            $args,
            'wpinv-quotes/',
            WP_PLUGIN_DIR . '/wpinv-quotes/templates/'
        );

        $sent = wpinv_mail_send($recipient, $subject, $content, $headers, $attachments);

        if ($sent) {
            $note = __('Quote has been emailed to the user.', 'invoicing');
        } else {
            $note = __('Fail to send quote to the user!', 'invoicing');
        }
        $quote->add_note($note); // Add private note.

        if (wpinv_mail_admin_bcc_active($email_type)) {
            $recipient = wpinv_get_admin_email();
            $subject .= ' - ADMIN BCC COPY';
            wpinv_mail_send($recipient, $subject, $content, $headers, $attachments);
        }

        return $sent;
    }

}