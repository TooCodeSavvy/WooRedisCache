<?php
if (defined('WP_CLI') && WP_CLI) {
    class WC_API_Key_Command {
        function generate($args, $assoc_args) {
            WP_CLI::log("Start van het genereren van API sleutel...");

            list($user_id, $description, $permissions) = $args;
            WP_CLI::log("Gebruikers ID: $user_id, Beschrijving: $description, Permissies: $permissions");

            // Include necessary WooCommerce files
            if (!class_exists('WC_Data_Store')) {
                include_once '/usr/share/nginx/html/wp-content/plugins/woocommerce/includes/class-wc-data-store.php';
            }
            if (!class_exists('WC_API')) {
                include_once '/usr/share/nginx/html/wp-content/plugins/woocommerce/includes/class-wc-api.php';
            }

            // Load the API keys data store
            $data_store = WC_Data_Store::load('api_key');

            // Create the API key
            $key = $data_store->create(array(
                'user_id' => $user_id,
                'description' => $description,
                'permissions' => $permissions,
            ));

            if (is_wp_error($key)) {
                WP_CLI::error($key );
            } else {
                $successMessage = "API sleutel aangemaakt: Consumer Key: {$key['consumer_key']}, Consumer Secret: {$key['consumer_secret']}";
                WP_CLI::success($successMessage);

                // Log the generated API key
                $logFile = '/usr/share/nginx/html/output.log';
                $message = "API sleutel succesvol aangemaakt voor gebruiker $user_id.\nConsumer Key: {$key['consumer_key']}\nConsumer Secret: {$key['consumer_secret']}\n";
                if (file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX) === false) {
                    WP_CLI::log("Failed to write to {$logFile}");
                } else {
                    WP_CLI::log("Succesvol geschreven naar {$logFile}");
                }
            }
        }
    }

    WP_CLI::add_command('wc-api-key', 'WC_API_Key_Command');
}
