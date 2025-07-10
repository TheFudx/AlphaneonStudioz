<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlphaStudioz | Checkout</title>
</head>
<body>
    <h2>Proceed with Subscription Payment {{ $subscriptionId }}</h2>
    <!-- Add Razorpay checkout script -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        var options = {
            "key": "rzp_test_FZs1ciE3h1febU", // Razorpay key
            "subscription_id": "{{ $subscriptionId }}",  // Subscription ID for the subscription
            "handler": function (response) {
                // Create a form dynamically to send a POST request to the backend after successful payment
                var form = document.createElement("form");
                form.method = "POST";
                form.action = "{{ route('subscription.payment.success') }}";
    
                // Add CSRF token input
                var csrfToken = document.createElement("input");
                csrfToken.type = "hidden";
                csrfToken.name = "_token";
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);
    
                // Add Razorpay payment response data
                var paymentId = document.createElement("input");
                paymentId.type = "hidden";
                paymentId.name = "razorpay_payment_id";
                paymentId.value = response.razorpay_payment_id;
                form.appendChild(paymentId);
    
                var subscriptionId = document.createElement("input");
                subscriptionId.type = "hidden";
                subscriptionId.name = "razorpay_subscription_id";
                subscriptionId.value = "{{ $subscriptionId }}";
                form.appendChild(subscriptionId);
    
                var signature = document.createElement("input");
                signature.type = "hidden";
                signature.name = "razorpay_signature";
                signature.value = response.razorpay_signature;
                form.appendChild(signature);
    
                document.body.appendChild(form);
                form.submit();
            },
            "prefill": {
                "name": "{{ app('logged-in-user')->name }}",
                "email": "{{ app('logged-in-user')->email }}"
            },
            "theme": {
                "color": "#ff3a1f"
            }
        };
    
        var rzp1 = new Razorpay(options);
        rzp1.open();
    </script>
    
</body>
</html>
