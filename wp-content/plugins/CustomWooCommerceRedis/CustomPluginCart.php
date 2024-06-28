<?php
namespace CustomWooCommerceRedis;

class CustomPluginCart {
    private $redisClient;

    public function __construct(Interfaces\IRedisClient $redisClient) {
        $this->redisClient = $redisClient;
        add_action('woocommerce_add_to_cart', [$this, 'syncCartToRedis']);
        add_action('woocommerce_remove_cart_item', [$this, 'syncCartToRedis']);
        add_action('woocommerce_cart_item_quantity_updated', [$this, 'syncCartToRedis']);
        add_action('woocommerce_cart_item_removed', [$this, 'syncCartToRedis']);
        add_action('wp_loaded', [$this, 'syncCartToRedis']);
    }

    public function getSessionKey() {
        if (function_exists('WC') && WC()->session) {
            // Probeer eerst de sessie-cookie te krijgen
            $session_cookie = WC()->session->get_session_cookie();
            if (is_array($session_cookie) && !empty($session_cookie[0])) {
                return $session_cookie[0];
            } else {
                // Gebruik de get_customer_id() methode van WC_Session_Handler
                $customer_id = WC()->session->get_customer_id();
                if ($customer_id) {
                    return $customer_id;
                }
            }
        }
        return null;
    }
    
    public function getCartKey() {
        $session_key = $this->getSessionKey();
        return $session_key ? 'cart_' . $session_key : null;
    }
    

    public function syncCartToRedis() {
        if (!function_exists('WC') || !WC()->cart) {
            return;
        }
    
        $cart = WC()->cart->get_cart();
        $cartData = [];
        $subtotal = 0;
    
        foreach ($cart as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            $item_subtotal = $cart_item['quantity'] * $product->get_price();
            $subtotal += $item_subtotal;
    
            $cartData['items'][$cart_item_key] = [
                'product_id' => $cart_item['product_id'],
                'variation_id' => $cart_item['variation_id'],
                'quantity' => $cart_item['quantity'],
                'name' => $product->get_name(),
                'price' => wc_price($product->get_price()),
                'currency' => get_woocommerce_currency_symbol(),
                'image_url' => $product->get_image(),
                'alt_text' => $product->get_title(),
                'product_url' => $product->get_permalink()
            ];
        }
    
        $cartData['subtotal'] = wc_price($subtotal);
        $cartData['cart_count'] = WC()->cart->get_cart_contents_count();
    
        // Render HTML for cart
        ob_start();
        $items_html = $this->renderCartItemsHtml($cartData);
        $cart_summary_html = $this->renderCartSummaryHtml($cartData);
        ob_end_clean();
    
        // Add HTML to cart data
        $cartData['items_html'] = $items_html;
        $cartData['cart_summary_html'] = $cart_summary_html;
    
        // Store in Redis
        $cartKey = $this->getCartKey();
        if ($cartKey) {
            $this->redisClient->set($cartKey, $cartData, 3600); 
        }
    }
    
    private function renderCartItemsHtml($cartData) {
        $items_html = '';
    
        if (empty($cartData['items'])) {
            $items_html .= '
                <div class="flex items-start justify-between">
                    <h2 class="fs_modal-1_heading-2" id="slide-over-title">Winkelwagen</h2>
                    <div aria-expanded="true" role="button" aria-roledescription="close-modal-trigger" aria-controls="fs-modal-1-popup" aria-label="Close modal" tabindex="0" data-w-id="79dce308-14fa-4274-343f-413b1853cc64" class="fs_modal-1_close-2 close-cart">
                        <div class="fs_modal-1_close-icon-2 w-embed">
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 24 24">
                                <path fill="CurrentColor" d="M14.5,12l9-9c0.7-0.7,0.7-1.8,0-2.5c-0.7-0.7-1.8-0.7-2.5,0l-9,9l-9-9c-0.7-0.7-1.8-0.7-2.5,0 c-0.7,0.7-0.7,1.8,0,2.5l9,9l-9,9c-0.7,0.7-0.7,1.8,0,2.5c0.7,0.7,1.8,0.7,2.5,0l9-9l9,9c0.7,0.7,1.8,0.7,2.5,0 c0.7-0.7,0.7-1.8,0-2.5L14.5,12z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <li class="flex py-6">
                    <p class="text-gray-500">Je cart is leeg.</p>
                </li>';
        } else {
            $items_html .= "
                <div class='flex items-start justify-between'>
                    <h2 class='fs_modal-1_heading-2' id='slide-over-title'>Winkelwagen</h2>
                    <div aria-expanded='true' role='button' aria-roledescription='close-modal-trigger' aria-controls='fs-modal-1-popup' aria-label='Close modal' tabindex='0' data-w-id='79dce308-14fa-4274-343f-413b1853cc64' class='fs_modal-1_close-2 close-cart'>
                      <div class='fs_modal-1_close-icon-2 w-embed'><svg xmlns='http://www.w3.org/2000/svg' aria-hidden='true' viewBox='0 0 24 24'>
                          <path fill='CurrentColor' d='M14.5,12l9-9c0.7-0.7,0.7-1.8,0-2.5c-0.7-0.7-1.8-0.7-2.5,0l-9,9l-9-9c-0.7-0.7-1.8-0.7-2.5,0 c-0.7,0.7-0.7,1.8,0,2.5l9,9l-9,9c-0.7,0.7-0.7,1.8,0,2.5c0.7,0.7,1.8,0.7,2.5,0l9-9l9,9c0.7,0.7,1.8,0.7,2.5,0 c0.7-0.7,0.7-1.8,0-2.5L14.5,12z'></path>
                        </svg></div>
                    </div>
                  </div>";
            foreach ($cartData['items'] as $cart_item_key => $cart_item) {
                $items_html .= "
                <li class='flex py-6'>
                    <div class='h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200'>
                       {$cart_item['image_url']}
                    </div>
                    <div class='ml-4 flex flex-1 flex-col'>
                        <div>
                            <div class='flex justify-between text-base font-medium text-gray-900'>
                                <h3>
                                    <a href='{$cart_item['product_url']}'>{$cart_item['name']}</a>
                                </h3>
                                <p class='ml-4'>{$cart_item['price']}</p>
                            </div>
                        </div>
                        <div class='flex flex-1 items-end justify-between text-sm'>
                            <div class='flex items-center'>
                                <button style='color: #000;' type='button' class='minus font-medium text-indigo-600 hover:text-indigo-500 quantity-control' data-key='{$cart_item_key}' data-action='decrease'>-</button>
                                <input type='number' class='w-16 text-center' style='appearance: textfield;' value='{$cart_item['quantity']}' readonly>
                                <button style='color: #000;' type='button' class='plus font-medium text-indigo-600 hover:text-indigo-500 quantity-control' data-key='{$cart_item_key}' data-action='increase'>+</button>
                                <div class='relative text-xs bg-gray-700 text-white py-1 px-2 rounded bottom-full mb-2 invisible group-hover:visible'>
                                    Maximale hoeveelheid bereikt
                                </div>        
                            </div>
                            <div class='flex'>
                                <button style='color: #000;' type='button' class='remove font-medium text-indigo-600 hover:text-indigo-500' data-key='{$cart_item_key}'>Verwijder</button>
                            </div>
                        </div>
                    </div>
                </li>";
            }
        }
    
        return $items_html;
    }
    
    private function renderCartSummaryHtml($cartData) {
        $cart_summary_html = '';
    
        if (empty($cartData['items'])) {
            $cart_summary_html .= '
            <div class="mt-6">
                <a href="/all-products/" style="background-color: #000 !important;" class="flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">Verder winkelen</a>
            </div>';
        } else {
            $cart_summary_html .= '
            <div class="border-gray-200 px-4 py-6 sm:px-6">
                <div class="flex justify-between text-base font-medium text-gray-900">
                    <p>Subtotaal</p>
                    <p>' . $cartData['subtotal'] . '</p>
                </div>
                <p class="mt-0.5 text-sm text-gray-500">Verzendkosten en belastingen berekend bij het afrekenen.</p>
                <div class="mt-6">
                    <a href="/afrekenen" style="background-color: #000;" class="flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">Afrekenen</a>
                </div>
                <div class="mt-6 flex justify-center text-center text-sm text-gray-500">
                    <p>
                        of
                        <button style="color: #000;" type="button" class="font-medium text-indigo-600 hover:text-indigo-500 shop-button">
                            Verder winkelen
                            <span aria-hidden="true"> →</span>
                        </button>
                    </p>
                </div>
            </div>';
        }
    
        return $cart_summary_html;
    }
    
}
?>
