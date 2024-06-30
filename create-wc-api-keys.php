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
            $user_id = $data['user_id'];
            $description = $data['description'];
            $permissions = $data['permissions'];

            $key = new WC_API_Key();
            $key->set_user_id($user_id);
            $key->set_description($description);
            $key->set_permissions($permissions);
            $key->set_consumer_key(uniqid('ck_'));
            $key->set_consumer_secret(uniqid('cs_'));
            $key->set_trusted(false);
            $key->set_last_access('');
            $key->set_last_access_ip('');

            $key->save();

            return [
                'key' => $key->get_consumer_key(),
                'secret' => $key->get_consumer_secret(),
            ];
        }
    }

    WP_CLI::add_command('wc-api-key', 'WC_API_Key_Command');
}
