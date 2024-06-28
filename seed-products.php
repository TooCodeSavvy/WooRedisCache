<?php
require_once('/usr/share/nginx/html/wp-load.php');

function create_woocommerce_product() {
    $product = new WC_Product_Simple();
    $product->set_name('Test Product');
    $product->set_regular_price(20);
    $product->set_description('A simple test product');
    $product->save();
    echo "Product created with ID: " . $product->get_id() . "\n";
}

create_woocommerce_product();