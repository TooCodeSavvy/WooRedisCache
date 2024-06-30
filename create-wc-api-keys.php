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

            $key_data = WC_Auth::create_keys($data);

            if (is_wp_error($key_data)) {
                WP_CLI::error($key_data->get_error_message());
            } else {
                $successMessage = "API sleutel aangemaakt: Consumer Key: {$key_data['consumer_key']}, Consumer Secret: {$key_data['consumer_secret']}";
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
    }

    WP_CLI::add_command('wc-api-key', 'WC_API_Key_Command');
}