<?php

declare(strict_types=1);

use MainMoney\WordPressPlugin\AdminApiHelper;

final class Mm_Aggr_Admin_Wallets
{
    public static function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $query = [];
        if (isset($_GET['currency']) && is_string($_GET['currency']) && $_GET['currency'] !== '') {
            $query['currency'] = strtoupper(sanitize_text_field($_GET['currency']));
        }
        if (isset($_GET['country']) && is_string($_GET['country']) && $_GET['country'] !== '') {
            $query['country'] = strtoupper(sanitize_text_field($_GET['country']));
        }
        if (isset($_GET['status']) && is_string($_GET['status']) && $_GET['status'] !== '') {
            $query['status'] = sanitize_text_field($_GET['status']);
        }

        $client = AdminApiHelper::clientOrNull();
        $error = null;
        $rows = [];

        if ($client === null) {
            $error = __('Configure merchant credentials under MainMoney → Settings.', 'mm-aggr');
        } else {
            $result = AdminApiHelper::safeCall(fn () => $client->wallets->list($query));
            if (!$result['ok']) {
                $error = $result['message'];
            } else {
                $rows = AdminApiHelper::normalizeList($result['data'])['results'];
            }
        }

        echo '<div class="wrap mm-aggr-admin">';
        echo '<h1>'.esc_html__('Wallets', 'mm-aggr').'</h1>';
        if ($error !== null) {
            echo '<div class="notice notice-error"><p>'.esc_html($error).'</p></div>';
        }
        echo '<form method="get" class="mm-aggr-filters">';
        echo '<input type="hidden" name="page" value="mm-aggr-wallets" />';
        echo '<label>'.esc_html__('Currency', 'mm-aggr').'<input name="currency" type="text" value="'.esc_attr((string) ($query['currency'] ?? '')).'" /></label>';
        echo '<label>'.esc_html__('Country', 'mm-aggr').'<input name="country" type="text" value="'.esc_attr((string) ($query['country'] ?? '')).'" /></label>';
        echo '<label>'.esc_html__('Status', 'mm-aggr');
        echo '<select name="status"><option value="">'.esc_html__('Any', 'mm-aggr').'</option>';
        echo '<option value="active"'.selected((string) ($query['status'] ?? ''), 'active', false).'>'.esc_html__('Active', 'mm-aggr').'</option>';
        echo '<option value="inactive"'.selected((string) ($query['status'] ?? ''), 'inactive', false).'>'.esc_html__('Inactive', 'mm-aggr').'</option>';
        echo '</select></label>';
        submit_button(__('Filter', 'mm-aggr'), 'secondary', '', false);
        echo '</form>';
        echo '<table class="widefat striped mm-aggr-table"><thead><tr>';
        echo '<th>'.esc_html__('Country', 'mm-aggr').'</th>';
        echo '<th>'.esc_html__('Currency', 'mm-aggr').'</th>';
        echo '<th>'.esc_html__('Balance', 'mm-aggr').'</th>';
        echo '<th>'.esc_html__('Reserved', 'mm-aggr').'</th>';
        echo '<th>'.esc_html__('Available', 'mm-aggr').'</th>';
        echo '<th>'.esc_html__('Status', 'mm-aggr').'</th>';
        echo '</tr></thead><tbody>';
        if ($rows === []) {
            echo '<tr><td colspan="6">'.esc_html__('No wallets found.', 'mm-aggr').'</td></tr>';
        } else {
            foreach ($rows as $row) {
                echo '<tr>';
                echo '<td>'.esc_html((string) ($row['country'] ?? '')).'</td>';
                echo '<td>'.esc_html((string) ($row['currency'] ?? '')).'</td>';
                echo '<td>'.esc_html((string) ($row['balance'] ?? '')).'</td>';
                echo '<td>'.esc_html((string) ($row['reserved_balance'] ?? '')).'</td>';
                echo '<td>'.esc_html((string) ($row['available_balance'] ?? '')).'</td>';
                echo '<td>'.esc_html(!empty($row['is_active']) ? __('Active', 'mm-aggr') : __('Inactive', 'mm-aggr')).'</td>';
                echo '</tr>';
            }
        }
        echo '</tbody></table></div>';
    }
}
