<?php
/**
 * Quote Shortcodes
 *
 * @since      1.0.0
 * @package    Wpinv_Quotes
 * @subpackage Wpinv_Quotes/includes/shortcodes
 * @author     GeoDirectory Team <info@wpgeodirectory.com>
 * @link       https://wpgeodirectory.com
 */

class Wpinv_Quote_Shortcodes {
    /**
     * Init shortcodes.
     */
    public static function init() {
        $shortcodes = array(
            'wpinv_quote_history'  => __CLASS__ . '::history',
        );

        foreach ( $shortcodes as $shortcode => $function ) {
            add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
        }
    }

    /**
     * Shortcode Wrapper.
     *
     * @param string[] $function
     * @param array $atts (default: array())
     * @return string
     */
    public static function shortcode_wrapper( $function, $atts = array(), $content = null, $wrapper = array( 'class' => 'wpi-g', 'before' => null, 'after' => null ) ) {
        ob_start();

        echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
        call_user_func( $function, $atts, $content );
        echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

        return ob_get_clean();
    }

    /**
     * Quote History page shortcode.
     *
     * @param mixed $atts
     * @return string
     */
    public static function history( $atts ) {
        return self::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
    }

    /**
     * Output the shortcode.
     *
     * @param array $atts
     */
    public static function output( $atts ) {
        do_action( 'wpinv_before_user_quote_history' );
        wpinv_get_template( 'wpinv-quote-history.php', $atts, 'invoicing-quotes/', WP_PLUGIN_DIR . '/invoicing-quotes/templates/' );
        do_action( 'wpinv_after_user_quote_history' );
    }
}
