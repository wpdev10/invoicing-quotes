<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpgeodirectory.com
 * @since      1.0.0
 *
 * @package    Wpinv_Quotes
 * @subpackage Wpinv_Quotes/includes
 */

/**
 * Class with shared functions which can be used for admin and public both
 *
 * @since      1.0.0
 * @package    Wpinv_Quotes
 * @subpackage Wpinv_Quotes/includes
 * @author     GeoDirectory Team <info@wpgeodirectory.com>
 */

/**
 * Calls the class.
 */
function wpinv_quote_call_shared_class()
{
    new Wpinv_Quotes_Shared();
}

add_action('wpinv_quotes_loaded', 'wpinv_quote_call_shared_class', 2);

class Wpinv_Quotes_Shared
{
    /**
     * @var  object  Instance of this class
     */
    protected static $instance;

    private static $quote_statuses = array();

    public function __construct()
    {

        add_action('wpinv_statuses', array($this, 'wpinv_quote_statuses'), 99);
        add_action('wpinv_get_status', array($this, 'wpinv_quote_get_status'), 99, 4);
        add_action('wpinv_setup_invoice', array($this, 'wpinv_quote_setup_quote'), 10, 1);
        add_action( 'init', array( 'Wpinv_Quote_Shortcodes', 'init' ) );

        self::$quote_statuses = apply_filters('wpinv_quote_statuses', array(
            'wpi-quote-pending' => __('Pending', 'invoicing'),
            'wpi-quote-accepted' => __('Accepted', 'invoicing'),
            'wpi-quote-declined' => __('Declined', 'invoicing'),
        ));

    }

    public static function get_instance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Add statuses to the dropdown in quote details metabox
     *
     * @since    1.0.0
     * @return array quote statuses
     */
    public static function wpinv_get_quote_statuses()
    {
        return self::$quote_statuses;
    }

    /**
     * Add statuses to the dropdown in quote details metabox
     *
     * @since    1.0.0
     *
     */
    public static function wpinv_quote_statuses($quote_statuses)
    {
        global $wpinv_quote, $post;
        if (!empty($post->ID) && 'wpi_quote' == $post->post_type || !empty($wpinv_quote->ID) && 'wpi_quote' == $wpinv_quote->post_type) {
            return self::$quote_statuses;
        }
        return $quote_statuses;
    }

    /**
     * Add statuses to the dropdown in quote details metabox
     *
     * @since    1.0.0
     *
     */
    public static function wpinv_quote_get_status($status, $nicename, $quote_id, $quote)
    {
        if (!empty($quote->ID) && 'wpi_quote' === $quote->post_type) {
            if($nicename){
                return self::wpinv_quote_status_nicename($status);
            } else {
                return $status;
            }
        }
        return $status;
    }

    /**
     * Get quote status nicename
     *
     * @since    1.0.0
     * @param string $status status to get nice name of
     * @return string $status nicename of status
     */
    public static function wpinv_quote_status_nicename($status)
    {
        $statuses = self::$quote_statuses;
        $status = isset($statuses[$status]) ? $statuses[$status] : __($status, 'invoicing');

        return $status;
    }

    /**
     * set global variable to use in add-on
     *
     * @since    1.0.0
     * @param object $quote quote object
     */
    public static function wpinv_quote_setup_quote($quote)
    {
        global $wpinv_quote;
        $wpinv_quote = $quote;
        if('wpi_quote' == $wpinv_quote->post_type){
            $wpinv_quote->status_nicename = self::wpinv_quote_status_nicename( $wpinv_quote->post_status );
        }
    }

    /**
     * Get quote status label for history page
     *
     * @since    1.0.0
     * @param string $status status to get label for
     * @param string $status_display status nicename
     * @return string $label label with status name and class
     */
    public static function wpinv_quote_invoice_status_label( $status, $status_display)
    {
        if ( empty( $status_display ) ) {
            $status_display = self::wpinv_quote_status_nicename( $status );
        }

        switch ( $status ) {
            case 'wpi-quote-accepted' :
                $class = 'label-success';
                break;
            case 'wpi-quote-pending' :
                $class = 'label-primary';
                break;
            case 'wpi-quote-declined' :
                $class = 'label-danger';
                break;
            default:
                $class = 'label-default';
                break;
        }

        $label = '<span class="label label-inv-' . $status . ' ' . $class . '">' . $status_display . '</span>';

        return $label;
    }

    /**
     * Get quote history columns
     *
     * @since    1.0.0
     * @return array $columns columns for displaying in quote history page
     */
    public static function wpinv_get_user_quote_columns()
    {
        $columns = array(
            'quote-number' => array('title' => __('ID', 'invoicing'), 'class' => 'text-left'),
            'quote-date' => array('title' => __('Date', 'invoicing'), 'class' => 'text-left'),
            'quote-status' => array('title' => __('Status', 'invoicing'), 'class' => 'text-center'),
            'quote-total' => array('title' => __('Total', 'invoicing'), 'class' => 'text-right'),
            'quote-actions' => array('title' => '&nbsp;', 'class' => 'text-center'),
        );

        return apply_filters('wpinv_user_quotes_columns', $columns);
    }

    /**
     * Get quote history quote data
     *
     * @since    1.0.0
     * @param array $args to retrive quotes
     * @return object post object of all matching quotes
     */
    public static function wpinv_get_quotes($args)
    {
        $args = wp_parse_args($args, array(
            'status' => array_keys(self::$quote_statuses),
            'type' => 'wpi_quote',
            'parent' => null,
            'user' => null,
            'email' => '',
            'limit' => get_option('posts_per_page'),
            'offset' => null,
            'page' => 1,
            'exclude' => array(),
            'orderby' => 'date',
            'order' => 'DESC',
            'return' => 'objects',
            'paginate' => false,
        ));

        // Handle some BW compatibility arg names where wp_query args differ in naming.
        $map_legacy = array(
            'numberposts' => 'limit',
            'post_type' => 'type',
            'post_status' => 'status',
            'post_parent' => 'parent',
            'author' => 'user',
            'posts_per_page' => 'limit',
            'paged' => 'page',
        );

        foreach ($map_legacy as $from => $to) {
            if (isset($args[$from])) {
                $args[$to] = $args[$from];
            }
        }

        if (get_query_var('paged'))
            $args['page'] = get_query_var('paged');
        else if (get_query_var('page'))
            $args['page'] = get_query_var('page');
        else if (!empty($args['page']))
            $args['page'] = $args['page'];
        else
            $args['page'] = 1;

        /**
         * Generate WP_Query args. This logic will change if orders are moved to
         * custom tables in the future.
         */
        $wp_query_args = array(
            'post_type' => 'wpi_quote',
            'post_status' => $args['status'],
            'posts_per_page' => $args['limit'],
            'meta_query' => array(),
            'date_query' => !empty($args['date_query']) ? $args['date_query'] : array(),
            'fields' => 'ids',
            'orderby' => $args['orderby'],
            'order' => $args['order'],
        );

        if (!empty($args['user'])) {
            $wp_query_args['author'] = absint($args['user']);
        }

        if (!is_null($args['parent'])) {
            $wp_query_args['post_parent'] = absint($args['parent']);
        }

        if (!is_null($args['offset'])) {
            $wp_query_args['offset'] = absint($args['offset']);
        } else {
            $wp_query_args['paged'] = absint($args['page']);
        }

        if (!empty($args['exclude'])) {
            $wp_query_args['post__not_in'] = array_map('absint', $args['exclude']);
        }

        if (!$args['paginate']) {
            $wp_query_args['no_found_rows'] = true;
        }

        // Get results.
        $quotes = new WP_Query($wp_query_args);

        if ('objects' === $args['return']) {
            $return = array_map('wpinv_get_invoice', $quotes->posts);
        } elseif ('self' === $args['return']) {
            return $quotes;
        } else {
            $return = $quotes->posts;
        }

        if ($args['paginate']) {
            return (object)array(
                'quotes' => $return,
                'total' => $quotes->found_posts,
                'max_num_pages' => $quotes->max_num_pages,
            );
        } else {
            return $return;
        }
    }

    /**
     * Get url to accept quote from front side
     *
     * @since    1.0.0
     * @param int $quote_id ID of quote
     * @return string $url url for accept quote button
     */
    public static function get_accept_quote_url($quote_id)
    {
        $nonce = wp_create_nonce('wpinv_client_accept_quote_nonce');
        $url = get_permalink($quote_id);
        $url = add_query_arg( array(
            'wpi_action' => 'quote_action',
            'action' => 'accept',
            'qid' => $quote_id,
            '_wpnonce' => $nonce,
        ), $url );
        return $url;
    }

    /**
     * Get url to decline quote from front side
     *
     * @since    1.0.0
     * @param int $quote_id ID of quote
     * @return string $url url for decline quote button
     */
    public static function get_decline_quote_url($quote_id)
    {
        $nonce = wp_create_nonce('wpinv_client_decline_quote_nonce');
        $url = get_permalink($quote_id);
        $url = add_query_arg( array(
            'wpi_action' => 'quote_action',
            'action' => 'decline',
            'qid' => $quote_id,
            '_wpnonce' => $nonce,
        ), $url );
        return $url;
    }

    /**
     * Get url of quote history page
     *
     * @since    1.0.0
     * @return string $url url of quote history page
     */
    public static function wpinv_get_quote_history_page_uri() {
        $page_id = wpinv_get_option( 'quote_history_page', 0 );
        $page_id = absint( $page_id );

        return apply_filters( 'wpinv_get_quote_page_uri', get_permalink( $page_id ) );
    }

    /**
     * Check sequential number or not for quote.
     *
     * @since    1.0.1
     *
     * @return   bool True if active else False.
     */
    public static function wpinv_sequential_number_active() {
        return wpinv_get_option( 'sequential_quote_number' );
    }

    public static function wpinv_create_quote($args = array(), $wp_error = false){
        $default_args = array(
            'status'        => '',
            'user_id'       => null,
            'user_note'     => null,
            'quote_id'      => 0,
            'created_via'   => '',
            'parent'        => 0
        );

        $args         = wp_parse_args( $args, $default_args );
        $quote_data   = array();
        $quote_data['post_type']  = 'wpi_quote';

        if ( $args['quote_id'] > 0 ) {
            $updating                 = true;
            $quote_data['ID']         = $args['quote_id'];
        } else {
            $updating                     = false;
            $quote_data['post_status']    = apply_filters( 'wpinv_default_quote_status', 'wpi-quote-pending' );
            $quote_data['ping_status']    = 'closed';
            $quote_data['post_author']    = !empty( $args['user_id'] ) ? $args['user_id'] : get_current_user_id();
            $quote_data['post_title']     = '';
            $quote_data['post_parent']    = absint( $args['parent'] );
            if ( !empty( $args['created_date'] ) ) {
                $quote_data['post_date']      = $args['created_date'];
                $quote_data['post_date_gmt']  = get_gmt_from_date( $args['created_date'] );
            }
        }

        if ( $args['status'] ) {
            if ( ! in_array( $args['status'], array_keys( self::$quote_statuses ) ) ) {
                return new WP_Error( 'wpinv_invalid_quote_status', wp_sprintf( __( 'Invalid quote status: %s', 'wpinv-quotes' ), $args['status'] ) );
            }
            $quote_data['post_status']    = $args['status'];
        }

        if ( ! is_null( $args['user_note'] ) ) {
            $quote_data['post_excerpt']   = $args['user_note'];
        }

        if ( $updating ) {
            $quote_id = wp_update_post( $quote_data, true );
        } else {
            $quote_id = wp_insert_post( apply_filters( 'wpinv_new_quote_data', $quote_data ), true );
        }

        if ( is_wp_error( $quote_id ) ) {
            return $wp_error ? $quote_id : 0;
        }

        $quote = wpinv_get_invoice( $quote_id );

        if ( !$updating ) {
            update_post_meta( $quote_id, '_wpinv_key', apply_filters( 'wpinv_generate_quote_key', uniqid( 'wpinv_' ) ) );
            update_post_meta( $quote_id, '_wpinv_currency', wpinv_get_currency() );
            update_post_meta( $quote_id, '_wpinv_include_tax', get_option( 'wpinv_prices_include_tax' ) );
            update_post_meta( $quote_id, '_wpinv_user_ip', wpinv_get_ip() );
            update_post_meta( $quote_id, '_wpinv_user_agent', wpinv_get_user_agent() );
            update_post_meta( $quote_id, '_wpinv_created_via', sanitize_text_field( $args['created_via'] ) );

            // Add quote note
            $quote->add_note( wp_sprintf( __( 'Quote created with status %s.', 'wpinv-quotes' ), wpinv_status_nicename( $quote->status ) ) );
        }

        update_post_meta( $quote_id, '_wpinv_version', WPINV_VERSION );

        return $quote;
    }

    public static function wpinv_insert_quote($quote_data = array(), $wp_error = false){
        if ( empty( $quote_data ) ) {
            return false;
        }

        if ( !( !empty( $quote_data['cart_details'] ) && is_array( $quote_data['cart_details'] ) ) ) {
            return $wp_error ? new WP_Error( 'wpinv_invalid_items', __( 'Quote must have at least one item.', 'wpinv-quotes' ) ) : 0;
        }

        if ( empty( $quote_data['user_id'] ) ) {
            $quote_data['user_id'] = get_current_user_id();
        }

        $quote_data['quote_id'] = !empty( $quote_data['quote_id'] ) ? (int)$quote_data['quote_id'] : 0;

        if ( empty( $quote_data['status'] ) ) {
            $quote_data['status'] = 'wpi-quote-pending';
        }

        if ( empty( $quote_data['ip'] ) ) {
            $quote_data['ip'] = wpinv_get_ip();
        }

        $quote = self::wpinv_create_quote( $quote_data, true );

        if ( is_wp_error( $quote ) ) {
            return $wp_error ? $quote : 0;
        }

        // User info
        $default_user_info = array(
            'user_id'               => '',
            'first_name'            => '',
            'last_name'             => '',
            'email'                 => '',
            'company'               => '',
            'phone'                 => '',
            'address'               => '',
            'city'                  => '',
            'country'               => wpinv_get_default_country(),
            'state'                 => wpinv_get_default_state(),
            'zip'                   => '',
            'vat_number'            => '',
            'vat_rate'              => '',
            'adddress_confirmed'    => '',
            'discount'              => array(),
        );

        if ( $user_id = (int)$quote->get_user_id() ) {
            if ( $user_address = wpinv_get_user_address( $user_id ) ) {
                $default_user_info = wp_parse_args( $user_address, $default_user_info );
            }
        }

        if ( empty( $quote_data['user_info'] ) ) {
            $quote_data['user_info'] = array();
        }

        $user_info = wp_parse_args( $quote_data['user_info'], $default_user_info );

        if ( empty( $user_info['first_name'] ) ) {
            $user_info['first_name'] = $default_user_info['first_name'];
            $user_info['last_name'] = $default_user_info['last_name'];
        }

        if ( empty( $user_info['country'] ) ) {
            $user_info['country'] = $default_user_info['country'];
            $user_info['state'] = $default_user_info['state'];
            $user_info['city'] = $default_user_info['city'];
            $user_info['address'] = $default_user_info['address'];
            $user_info['zip'] = $default_user_info['zip'];
            $user_info['phone'] = $default_user_info['phone'];
        }

        if ( !empty( $user_info['discount'] ) && !is_array( $user_info['discount'] ) ) {
            $user_info['discount'] = (array)$user_info['discount'];
        }

        // Payment details
        $payment_details = array();
        if ( !empty( $quote_data['payment_details'] ) ) {
            $default_payment_details = array(
                'gateway'           => 'manual',
                'gateway_title'     => '',
                'currency'          => wpinv_get_default_country(),
                'transaction_id'    => '',
            );

            $payment_details = wp_parse_args( $quote_data['payment_details'], $default_payment_details );

            if ( empty( $payment_details['gateway_title'] ) ) {
                $payment_details['gateway_title'] = wpinv_get_gateway_checkout_label( $payment_details['gateway'] );
            }
        }

        $quote->set( 'status', ( !empty( $quote_data['status'] ) ? $quote_data['status'] : 'wpi-quote-pending' ) );

        if ( !empty( $payment_details ) ) {
            $quote->set( 'currency', $payment_details['currency'] );
            $quote->set( 'gateway', $payment_details['gateway'] );
            $quote->set( 'gateway_title', $payment_details['gateway_title'] );
            $quote->set( 'transaction_id', $payment_details['transaction_id'] );
        }

        $quote->set( 'user_info', $user_info );
        $quote->set( 'first_name', $user_info['first_name'] );
        $quote->set( 'last_name', $user_info['last_name'] );
        $quote->set( 'address', $user_info['address'] );
        $quote->set( 'company', $user_info['company'] );
        $quote->set( 'vat_number', $user_info['vat_number'] );
        $quote->set( 'phone', $user_info['phone'] );
        $quote->set( 'city', $user_info['city'] );
        $quote->set( 'country', $user_info['country'] );
        $quote->set( 'state', $user_info['state'] );
        $quote->set( 'zip', $user_info['zip'] );
        $quote->set( 'discounts', $user_info['discount'] );
        $quote->set( 'ip', ( !empty( $quote_data['ip'] ) ? $quote_data['ip'] : wpinv_get_ip() ) );
        $quote->set( 'mode', ( wpinv_is_test_mode() ? 'test' : 'live' ) );
        $quote->set( 'parent_invoice', ( !empty( $quote_data['parent'] ) ? absint( $quote_data['parent'] ) : '' ) );

        if ( !empty( $quote_data['cart_details'] ) && is_array( $quote_data['cart_details'] ) ) {
            foreach ( $quote_data['cart_details'] as $key => $item ) {
                $item_id        = !empty( $item['id'] ) ? $item['id'] : 0;
                $quantity       = !empty( $item['quantity'] ) ? $item['quantity'] : 1;
                $name           = !empty( $item['name'] ) ? $item['name'] : '';
                $item_price     = isset( $item['item_price'] ) ? $item['item_price'] : '';

                $post_item  = new WPInv_Item( $item_id );
                if ( !empty( $post_item ) ) {
                    $name       = !empty( $name ) ? $name : $post_item->get_name();
                    $item_price = $item_price !== '' ? $item_price : $post_item->get_price();
                } else {
                    continue;
                }

                $args = array(
                    'name'          => $name,
                    'quantity'      => $quantity,
                    'item_price'    => $item_price,
                    'custom_price'  => isset( $item['custom_price'] ) ? $item['custom_price'] : '',
                    'tax'           => !empty( $item['tax'] ) ? $item['tax'] : 0.00,
                    'discount'      => isset( $item['discount'] ) ? $item['discount'] : 0,
                    'meta'          => isset( $item['meta'] ) ? $item['meta'] : array(),
                    'fees'          => isset( $item['fees'] ) ? $item['fees'] : array(),
                );

                $quote->add_item( $item_id, $args );
            }
        }

        $quote->increase_tax( wpinv_get_cart_fee_tax() );

        if ( isset( $quote_data['post_date'] ) ) {
            $quote->set( 'date', $quote_data['post_date'] );
        }

        // Invoice due date
        if ( isset( $quote_data['valid_until'] ) ) {
            update_post_meta($quote->ID, 'wpinv_quote_valid_until', $quote_data['valid_until']);
        }

        $quote->save();

        // Add notes
        if ( !empty( $quote_data['private_note'] ) ) {
            $quote->add_note( $quote_data['private_note'] );
        }
        if ( !empty( $quote_data['user_note'] ) ) {
            $quote->add_note( $quote_data['user_note'], true );
        }

        do_action( 'wpinv_insert_quote', $quote->ID, $quote_data );

        if ( ! empty( $quote->ID ) ) {
            global $wpi_userID, $wpinv_ip_address_country;

            $checkout_session = wpinv_get_checkout_session();

            $data_session                   = array();
            $data_session['invoice_id']     = $quote->ID;
            $data_session['cart_discounts'] = $quote->get_discounts( true );

            wpinv_set_checkout_session( $data_session );

            $wpi_userID         = (int)$quote->get_user_id();

            $_POST['country']   = !empty( $quote->country ) ? $quote->country : wpinv_get_default_country();
            $_POST['state']     = $quote->state;

            $quote->set( 'country', sanitize_text_field( $_POST['country'] ) );
            $quote->set( 'state', sanitize_text_field( $_POST['state'] ) );

            $wpinv_ip_address_country = $quote->country;

            $quote = $quote->recalculate_totals( true );

            wpinv_set_checkout_session( $checkout_session );

            return $quote;
        }

        if ( $wp_error ) {
            if ( is_wp_error( $quote ) ) {
                return $quote;
            } else {
                return new WP_Error( 'wpinv_insert_quote_error', __( 'Error while inserting quote.', 'wpinv-quotes' ) );
            }
        } else {
            return 0;
        }
    }
}