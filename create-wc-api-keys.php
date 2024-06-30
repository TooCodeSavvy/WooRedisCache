<?php
if (defined('WP_CLI') && WP_CLI) {
    class WC_API_Key_Command {
        function generate($args, $assoc_args) {
            list($user_id, $description, $permissions) = $args;

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
                $message = "Your log message here.\n";
                if (file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX) === false) {
                    // Log error or take action if file writing fails
                    error_log("Failed to write to {$logFile}");
                }
            }
        }
    }

    WP_CLI::add_command('wc-api-key', 'WC_API_Key_Command');
}