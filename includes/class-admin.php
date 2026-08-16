<?php

declare(strict_types=1);

final class Mm_Aggr_Admin
{
    public static function register(): void
    {
        add_action('admin_init', [self::class, 'settings']);
        add_action('admin_menu', [self::class, 'menu']);
    }

    public static function settings(): void
    {
        register_setting('mm_aggr', Mm_Aggr_Options::OPTION_KEY, [
            'type' => 'array',
            'sanitize_callback' => [Mm_Aggr_Options::class, 'sanitize'],
            'default' => [],
        ]);
    }

    public static function menu(): void
    {
        add_options_page(
            __('MainMoney', 'mm-aggr'),
            __('MainMoney', 'mm-aggr'),
            'manage_options',
            'mm-aggr',
            [self::class, 'render'],
        );
    }

    public static function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $settings = Mm_Aggr_Options::load();
        $webhookUrl = rest_url('mm-aggr/v1/webhooks');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('MainMoney', 'mm-aggr'); ?></h1>
            <p><?php echo esc_html__('Merchant API keys stay on the server. Do not put them in theme JavaScript.', 'mm-aggr'); ?></p>
            <form action="options.php" method="post">
                <?php settings_fields('mm_aggr'); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="mm_aggr_client_id"><?php echo esc_html__('Client ID', 'mm-aggr'); ?></label></th>
                        <td><input class="regular-text" id="mm_aggr_client_id" name="<?php echo esc_attr(Mm_Aggr_Options::OPTION_KEY); ?>[client_id]" type="text" value="<?php echo esc_attr($settings->clientId); ?>" autocomplete="off" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mm_aggr_secret"><?php echo esc_html__('API secret', 'mm-aggr'); ?></label></th>
                        <td>
                            <input class="regular-text" id="mm_aggr_secret" name="<?php echo esc_attr(Mm_Aggr_Options::OPTION_KEY); ?>[secret]" type="password" value="" autocomplete="new-password" />
                            <p class="description"><?php echo esc_html__('Leave blank to keep the current secret.', 'mm-aggr'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html__('Environment', 'mm-aggr'); ?></th>
                        <td>
                            <label>
                                <input name="<?php echo esc_attr(Mm_Aggr_Options::OPTION_KEY); ?>[test]" type="checkbox" value="1" <?php checked($settings->test); ?> />
                                <?php echo esc_html__('Test mode (testaggregator.mainmoney.net)', 'mm-aggr'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mm_aggr_base_uri"><?php echo esc_html__('Base URI override', 'mm-aggr'); ?></label></th>
                        <td>
                            <input class="regular-text" id="mm_aggr_base_uri" name="<?php echo esc_attr(Mm_Aggr_Options::OPTION_KEY); ?>[base_uri]" type="url" value="<?php echo esc_attr($settings->baseUri ?? ''); ?>" placeholder="https://aggregator.mainmoney.net/api/v1/" />
                            <p class="description"><?php echo esc_html__('Optional. Leave empty to use production or test hosts from the PHP SDK.', 'mm-aggr'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mm_aggr_webhook_secret"><?php echo esc_html__('Webhook secret', 'mm-aggr'); ?></label></th>
                        <td>
                            <input class="regular-text" id="mm_aggr_webhook_secret" name="<?php echo esc_attr(Mm_Aggr_Options::OPTION_KEY); ?>[webhook_secret]" type="password" value="" autocomplete="new-password" />
                            <p class="description"><?php echo esc_html(sprintf(/* translators: %s webhook URL */ __('Leave blank to keep the current secret. Aggregator webhook URL: %s', 'mm-aggr'), $webhookUrl)); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
