<?php

/*
Plugin Name: Orders modal 
Description: Order modal for woocommerce orders enhanced with barcode system.
Author: Yousseif Ahmed
Version: 1.0
*/



function phone_order_modal_page_content()
{
?>
    <!doctype html>
    <html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <!-- Bootstrap CSS v5.2.1 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    </head>

    <body>
        <header>
        </header>
        <main></main>
        <footer>
            <?php function enqueue_modal_scripts()
            {
                wp_enqueue_script('jquery');
                wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
                wp_localize_script('jquery', 'ajaxurl', admin_url('admin-ajax.php'));
            }

            add_action('wp_enqueue_scripts', 'enqueue_modal_scripts'); ?>
        </footer>
        <!-- Bootstrap JavaScript Libraries -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>



        <div class="wrap">
            <h2>Phone Order Modal</h2>
            <label for="phone_number">Enter Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number">
            <div class="container d-flex">
                <?php    // Retrieve orders
                $args = array(
                    'limit' => -1,
                    // Add any additional parameters for filtering orders if needed
                );
                $orders = wc_get_orders($args);

                // Check if there are orders
                $orders = wc_get_orders($args);

                // Check if there are orders
                $output = '<div class="container">';
                $output .= '<div class="row">'; // Start a new row with Bootstrap grid classes

                $count = 0; // Initialize a counter
                foreach ($orders as $order) {
                    $order_id = $order->get_id();
                    $order_date = $order->get_date_created()->format('M d, Y');
                    $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                    $items_count = count($order->get_items());

                    // Start a new row after every third order
                    if ($count % 3 === 0 && $count !== 0) {
                        $output .= '</div>'; // Close previous row
                        $output .= '<div class="row">'; // Start a new row
                    }

                    // Increment the counter
                    $count++;

                    // Add order details to the output
                    $output .= '<div class="col-md-4 order-details" data-order-id="' . esc_attr($order_id) . '">'; // Add a data attribute for order ID
                    $output .= '<div class="list-group mt-5  clickable-order" onclick="showOrderDetails(' . $order_id . ')">'; // Add onclick attribute
                    $output .= '<span class="list-group-item d-flex justify-content-between">';
                    $output .= '<div class="d-flex flex-row">';
                    $output .= '<img src="https://img.icons8.com/color/100/000000/folder-invoices.png" width="60" />';
                    $output .= '<div class="ml-2 mb-0">';
                    $output .= '<h6 class="mb-0">#' . esc_html($order_id) . ' ' . esc_html($customer_name) . '</h6>';
                    $output .= '<div class="about">';
                    $output .= '<span>' . esc_html($items_count) . ' Items </span><br>';
                    $output .= '<span>' . esc_html($order_date) . '</span>';
                    $output .= '</div></div></div>';
                    $output .= '</span>';
                    $output .= '</div>';
                    $output .= '</div>';
                }

                // Close the last row
                $output .= '</div>'; // Close last row
                $output .= '</div>'; // Close container

                // Output the HTML
                echo $output;
                ?>

                <div class="modal fade" id="phoneOrderModal" tabindex="-1" role="dialog" aria-labelledby="phoneOrderModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="phoneOrderModalLabel">Order Information</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                            </div>
                            <div class="modal-body">
                                <div id="searchResults"></div>
                                <div id='alert' style="fontWeight:bold;"></div>

                                <div id="orderInfo"></div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <div id="modifyQuantity">
                                    <label for="product_id">Enter Product ID</label>
                                    <input type="text" id="product_id" name="product_id" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </body>

    </html>

<?php
}

function phone_order_modal_ajax_handler()
{
    global $woocommerce, $order_id;
    $orderid = sanitize_text_field($_POST['orderId']);

    $args = array(
        'limit' => -1,
        'id' => $orderid
    );
    $orders = wc_get_orders($args);

    if (!empty($orders)) {
        $order = reset($orders);
        $order_id = $order->get_id();
        $items = $order->get_items();
        $output = '<div class="container-fluid mt-4">'; // Use Bootstrap container

        $size = 'woocommerce_thumbnail';
        $output = '<p><strong>Order ID:</strong> ' . esc_html($order_id) . '</p>';
        $output .= '<p><strong>Order Items:</strong></p>';
        $output .= '<div class="row">';
        $count = 0;
        // Create an array to store product IDs
        $product_ids = array();


        // Loop through each item in the $items array
        foreach ($items as $item) {
            // Extract necessary information
            $product_id = $item['product_id'];
            $quantity = $item->get_quantity();
            $product = wc_get_product($product_id);
            $image_size = apply_filters('single_product_archive_thumbnail_size', $size);
            if ($count % 4 == 0) {
                $output .= '</div><div class="row">';
            }
            // Start a new product grid item with Bootstrap classes
            $output .= '<div class="col-md-3 col-sm-6 mb-4 d-flex align-items-stretch product-grid-item" data-product-id="' . $product_id . '">';
            $output .= '<div style="margin: 0; padding: 0;" class="card h-100 d-flex flex-column" data-product-id="' . $product_id . '">';

            $output .= '<div class="card-header">' . esc_html($item->get_name()) . '</div>';


            // Card body for product details
            $output .= '<div class="card-body">';
            $output .= $product->get_image($image_size, array('class' => 'card-img-top img-fluid mw-100 h-100', 'alt' => esc_attr($item->get_name()))); // Use Bootstrap card for image
            $output .= '<div class="card-text quantity"><span id="quantity_' . $product_id . '">' . $quantity . '</span></div>';
            $output .= '</div>';

            // Close the grid item
            $output .= '</div>';
            $output .= '</div>';
            $count++;
            // Check if four items have been displayed and start a new row

        }

        // Close the product grid container
        $output .= '</div>';

        // Output the generated HTML
        echo $output;

        // Convert the array to a JSON string for use in JavaScript
        $product_ids_json = json_encode($product_ids);

        // Output the styles and JavaScript

        // Pass the product IDs JSON string to the JavaScript
        echo '<script>var productIds = ' . json_encode($product_ids) . ';</script>';
        echo '<script>var ajaxResponse = true;</script>';
    } else {
        echo '<p>No order found for the given phone number.</p>';
        echo '<script>var ajaxResponse = false;</script>';
    }
    die();
}
