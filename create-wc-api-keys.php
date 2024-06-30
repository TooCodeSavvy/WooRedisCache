<?php
if (defined('WP_CLI') && WP_CLI) {
    class WC_API_Key_Command {
        function generate($args, $assoc_args) {
            WP_CLI::log("Start van het genereren van API sleutel...");

            list($user_id, $description, $permissions) = $args;
            WP_CLI::log("Gebruikers ID: $user_id, Beschrijving: $description, Permissies: $permissions");

            if (!class_exists('WC_Auth')) {
                include_once '/usr/share/nginx/html/wp-content/plugins/woocommerce/includes/class-wc-auth.php';
            }

            if (!method_exists('WC_Auth', 'create_key')) {
                WP_CLI::error("Method 'create_key' does not exist in WC_Auth class.");
                return;
            }

            $key_data = WC_Auth::create_key(array(
                'user_id' => $user_id,
                'description' => $description,
                'permissions' => $permissions
            ));

            if (is_wp_error($key_data)) {
                WP_CLI::error($key_data->get_error_message());
            } else {
                $successMessage = "API sleutel aangemaakt: Consumer Key: {$key_data['consumer_key']}, Consumer Secret: {$key_data['consumer_secret']}";
                WP_CLI::success($successMessage);

                // Log the generated API key
                $logFile = '/usr/share/nginx/html/output.log';
                $message = "API sleutel succesvol aangemaakt voor gebruiker $user_id.\nConsumer Key: {$key_data['consumer_key']}\nConsumer Secret: {$key_data['consumer_secret']}\n";
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
