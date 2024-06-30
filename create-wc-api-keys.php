<?php
if (defined('WP_CLI') && WP_CLI) {
    class WC_API_Key_Command {
        /**
         * Genereert een nieuwe WooCommerce API sleutel.
         *
         * ## OPTIONS
         *
         * <user_id>
         * : De ID van de gebruiker voor wie de sleutel wordt aangemaakt.
         *
         * <description>
         * : Een beschrijving voor de API sleutel.
         *
         * <permissions>
         * : De permissies voor de API sleutel ('read', 'write', 'read_write').
         *
         * ## EXAMPLES
         *
         *     wp wc-api-key generate 1 "Mijn API Sleutel" read_write
         *
         */
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
                WP_CLI::success("API sleutel aangemaakt: Consumer Key: {$key_data['consumer_key']}, Consumer Secret: {$key_data['consumer_secret']}");
            }
        }
    }

    WP_CLI::add_command('wc-api-key', 'WC_API_Key_Command');
}