<?php
if (defined('WP_CLI') && WP_CLI) {
    class WC_API_Key_Command {
        function generate($args, $assoc_args) {
            WP_CLI::log("Start van het genereren van API sleutel...");

            list($user_id, $description, $permissions) = $args;
            WP_CLI::log("Gebruikers ID: $user_id, Beschrijving: $description, Permissies: $permissions");

            $result = WP_CLI::runcommand("wc tool generate_api_key $user_id '$description' $permissions --user=exampleuser --format=json");

            $key_data = json_decode($result, true);

            if (isset($key_data['error'])) {
                WP_CLI::error($key_data['error']);
            } else {
                $successMessage = "API sleutel aangemaakt: Consumer Key: {$key_data['consumer_key']}, Consumer Secret: {$key_data['consumer_secret']}";
                WP_CLI::success($successMessage);
            }
        }
    }

    WP_CLI::add_command('wc-api-key', 'WC_API_Key_Command');
}
