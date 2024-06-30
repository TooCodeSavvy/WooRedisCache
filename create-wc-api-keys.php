<?php
if (defined('WP_CLI') && WP_CLI) {
    class WC_API_Key_Command {
        function generate($args, $assoc_args) {
            WP_CLI::log("Start van het genereren van API sleutel...");

            list($user_id, $description, $permissions) = $args;
            WP_CLI::log("Gebruikers ID: $user_id, Beschrijving: $description, Permissies: $permissions");

            $data = [
                'user_id' => $user_id,
                'description' => $description,
                'permissions' => $permissions,
            ];

            $key_data = $this->create_woocommerce_api_key($data);

            if (is_wp_error($key_data)) {
                WP_CLI::error($key_data->get_error_message());
            } else {
                $successMessage = "API sleutel aangemaakt: Consumer Key: {$key_data['key']}, Consumer Secret: {$key_data['secret']}";
                WP_CLI::success($successMessage);
                
                $logFile = '/usr/share/nginx/html/output.log';
                $message = "API sleutel succesvol aangemaakt voor gebruiker $user_id.\n";
                if (file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX) === false) {
                    WP_CLI::log("Failed to write to {$logFile}");
                } else {
                    WP_CLI::log("Succesvol geschreven naar {$logFile}");
                }
            }
        }

        private function create_woocommerce_api_key($data) {
            // Ensure WooCommerce is loaded
            if (!class_exists('WooCommerce')) {
                return new WP_Error('woocommerce_not_loaded', 'WooCommerce is not loaded.');
            }

            $user_id = $data['user_id'];
            $description = $data['description'];
            $permissions = $data['permissions'];

            // Ensure WC_API class is loaded
            if (!class_exists('WC_API')) {
                include_once WP_PLUGIN_DIR . '/woocommerce/includes/class-wc-api.php';
            }

            // Include WC API Keys class if not already included
            if (!class_exists('WC_API_Keys')) {
                include_once WP_PLUGIN_DIR . '/woocommerce/includes/api/class-wc-api-keys.php';
            }

            $key_data = WC_API_Keys::create_key($user_id, $description, $permissions);

            if (is_wp_error($key_data)) {
                return $key_data;
            }

            return [
                'key' => $key_data['consumer_key'],
                'secret' => $key_data['consumer_secret'],
            ];
        }
    }

    // Hook into WordPress to ensure WooCommerce is fully loaded before adding the command
    add_action('init', function() {
        WP_CLI::add_command('wc-api-key', 'WC_API_Key_Command');
    });
}
