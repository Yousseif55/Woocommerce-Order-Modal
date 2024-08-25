function searchOrderByPhoneNumber() {
    var phoneNumber = document.getElementById('phone_number').value;

    if (phoneNumber) {
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'phone_order_modal_search',
                phone_number: phoneNumber,
            },
            success: function(response) {
                // Check if the response is not empty
                if (response.trim() !== '') {
                    // Display the result in the Bootstrap modal
                    jQuery('#orderInfo').html(response);
                    jQuery('#phoneOrderModal').modal('show');
                } else {
                    // If no order found, display a message
                    jQuery('#searchResults').html('<p>No order found for the given phone number.</p>');
                }
            },
        });
    } else {
        alert('Please enter a phone number.');
    }
}
/* till here */
function decreaseProductQuantity() {
    var productId = document.getElementById('product_id').value;

    // Check if the entered product ID is in the list
    var productElement = document.querySelector('[data-product-id="' + productId + '"]');

    if (productElement) {
        // Fetch and update the displayed quantity
        var currentQuantityElement = productElement.querySelector('span');
        var currentQuantity = parseInt(currentQuantityElement.textContent);

        // Decrease the displayed quantity by 1 (update only in the modal, not the actual quantity)
        if (currentQuantity > 0) {
            currentQuantityElement.textContent = currentQuantity - 1;

            // Disable the quantity div and card when quantity becomes zero
            if (currentQuantity === 1) {
                var pluginDirPath = '<?php echo plugin_dir_url(__FILE__); ?>';
                productElement.classList.add('disabled-card');
                productElement.querySelector('.quantity').classList.add('disabled-quantity');
                productElement.innerHTML += '<div class="overlay"><img src="' + pluginDirPath + 'checkmark.png' + '" alt="Checkmark Icon"></div>';
            }
        } else {
            document.getElementById('alert').innerHTML = '<b>Quantity cannot be less than 0.</b>';
            setTimeout(function() {
                document.getElementById('alert').innerHTML = '';
            }, 1000);
        }
    } else {
        document.getElementById('alert').innerHTML = '<b>Product ID not exist in this order!</b>';
        setTimeout(function() {
            document.getElementById('alert').innerHTML = '';
        }, 1000);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Event listener for "Enter" key press on phone number input
    document.getElementById('phone_number').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            searchOrderByPhoneNumber();
        }
    });

    // Event listener for "Enter" key press on product ID input
    document.getElementById('product_id').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            decreaseProductQuantity();
        }
    });
})
document.addEventListener("DOMContentLoaded", function() {
    // Focus on the input field when the modal is displayed
    document.getElementById("phoneOrderModal").addEventListener("shown.bs.modal", function() {
        document.getElementById("product_id").focus();
    });

    // Event listener for input event on product ID input
    document.getElementById('product_id').addEventListener('input', function(event) {
        // Check if the input value is not empty
        if (event.target.value.trim() !== '') {
            // Create a new "Enter" key press event
            var enterEvent = new KeyboardEvent('keypress', {
                'key': 'Enter',
                'code': 'Enter',
                'which': 13,
                'keyCode': 13,
                'charCode': 13,
                'bubbles': true
            });

            // Dispatch the "Enter" key press event on the product ID input
            event.target.dispatchEvent(enterEvent);
        }
    });
});
