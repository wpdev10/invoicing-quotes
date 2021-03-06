<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!($user_id = get_current_user_id())) {
    ?>
    <div class="wpinv-empty alert alert-error"><?php _e('You are not allowed to access this section', 'wpinv-quotes'); ?></div>
    <?php
    return;
}

global $current_page;
$current_page = empty($current_page) ? 1 : absint($current_page);
$query = apply_filters('wpinv_user_invoices_query', array('user' => $user_id, 'page' => $current_page, 'paginate' => true));
$user_quotes = Wpinv_Quotes_Shared::wpinv_get_quotes($query);
$has_quotes = 0 < $user_quotes->total;

do_action('wpinv_before_user_quotes', $has_quotes); ?>

<?php if ($has_quotes) { ?>
    <table class="table table-bordered table-hover table-responsive wpi-user-quotes">
        <thead>
        <tr>
            <?php foreach (Wpinv_Quotes_Shared::wpinv_get_user_quote_columns() as $column_id => $column_name) : ?>
                <th class="<?php echo esc_attr($column_id); ?> <?php echo(!empty($column_name['class']) ? $column_name['class'] : ''); ?>">
                    <span class="nobr"><?php echo esc_html($column_name['title']); ?></span></th>
            <?php endforeach; ?>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($user_quotes->quotes as $quote) {
            $quote_id = $quote->ID;
            ?>
            <tr class="wpinv-item wpinv-item-<?php echo $quote_status = $quote->post_status; ?>">
                <?php foreach (Wpinv_Quotes_Shared::wpinv_get_user_quote_columns() as $column_id => $column_name) : ?>
                    <td class="<?php echo esc_attr($column_id); ?> <?php echo(!empty($column_name['class']) ? $column_name['class'] : ''); ?>"
                        data-title="<?php echo esc_attr($column_name['title']); ?>">
                        <?php if (has_action('wpinv_user_quotes_column_' . $column_id)) : ?>
                            <?php do_action('wpinv_user_quotes_column_' . $column_id, $quote); ?>

                        <?php elseif ('quote-number' === $column_id) : ?>
                            <a href="<?php echo esc_url($quote->get_view_url()); ?>">
                                <?php echo _x('#', 'hash before quote number', 'wpinv-quotes') . $quote->get_number(); ?>
                            </a>

                        <?php elseif ('quote-date' === $column_id) : $date = wpinv_get_invoice_date($quote_id);
                            $dateYMD = wpinv_get_invoice_date($quote_id, 'Y-m-d H:i:s'); ?>
                            <time datetime="<?php echo strtotime($dateYMD); ?>"
                                  title="<?php echo $dateYMD; ?>"><?php echo $date; ?></time>

                        <?php elseif ('quote-status' === $column_id) : ?>
                            <?php echo Wpinv_Quotes_Shared::wpinv_quote_invoice_status_label($quote_status, Wpinv_Quotes_Shared::wpinv_quote_status_nicename($quote->post_status)); ?>

                        <?php elseif ('quote-total' === $column_id) : ?>
                            <?php echo $quote->get_total(true); ?>

                        <?php elseif ('quote-actions' === $column_id) : ?>
                            <?php
                            $actions = array(
                                'print' => array(
                                    'url' => $quote->get_view_url(),
                                    'name' => __('Print', 'wpinv-quotes'),
                                    'class' => 'btn-primary',
                                    'attrs' => 'target="_blank"'
                                )
                            );

                            if ($quote->post_status == 'wpi-quote-pending') {
                                $quote_actions = array(
                                    'accept' => array(
                                        'url' => esc_url(Wpinv_Quotes_Shared::get_accept_quote_url($quote_id)),
                                        'name' => __('Accept', 'wpinv-quotes'),
                                        'class' => 'btn-success'
                                    ),
                                    'decline' => array(
                                        'url' => 'javascript:void(0)',
                                        'name' => __('Decline', 'wpinv-quotes'),
                                        'class' => 'btn-danger',
                                        'attrs' => ' onclick="wpiQuiteAction(\'decline\', \'' . Wpinv_Quotes_Shared::get_decline_quote_url($quote_id) . '\', this);"'
                                    ),
                                );
                                $actions = array_merge($actions, $quote_actions);
                            }

                            if ($actions = apply_filters('wpinv_user_quotes_actions', $actions, $quote)) {
                                foreach ($actions as $key => $action) {
                                    $class = !empty($action['class']) ? sanitize_html_class($action['class']) : '';
                                    echo '<a href="' . $action['url'] . '" class="btn btn-sm ' . $class . ' ' . sanitize_html_class($key) . '" ' . (!empty($action['attrs']) ? $action['attrs'] : '') . '>' . $action['name'] . '</a>';
                                }
                            }
                            ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php do_action('wpinv_before_user_quotes_pagination'); ?>

    <?php if (1 < $user_quotes->max_num_pages) : ?>
        <div class="invoicing-Pagination">
            <?php
            $big = 999999;

            if (get_query_var('paged'))
                $current_page = get_query_var('paged');
            elseif (get_query_var('page'))
                $current_page = get_query_var('page');
            else
                $current_page = 1;

            echo paginate_links(array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, $current_page),
                'total' => $user_quotes->max_num_pages,
            ));
            ?>
        </div>
    <?php endif; ?>

<?php } else { ?>
    <div class="wpinv-empty alert-info">
        <?php _e( 'No quotes found.', 'wpinv-quotes' ); ?>
    </div>
<?php } ?>

<?php do_action('wpinv_after_user_quotes', $has_quotes); ?>
