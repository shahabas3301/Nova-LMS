<html>

<head>
    <title>{{ __('Moneroo Payment Gateway') }}</title>
</head>

<body>
    <div class="moneroo-payment-wrapper">
        <div class="moneroo-payment-inner-wrapper">
            <input type="hidden" name="order_id" id="order_id_input" value="{{ $moneroo_data['order_id'] }}" />

            <form id="moneroo_form">
                @foreach ($moneroo_data as $field_name => $value)
                    <input type="hidden" name="{{ $field_name }}" value="{{ $value }}" />
                @endforeach
            </form>

            <div class="btn-wrapper">
                <button id="payment_submit_btn"></button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            "use strict";

            var submitBtn = document.getElementById('payment_submit_btn');

            document.addEventListener('DOMContentLoaded', function() {
                submitBtn.dispatchEvent(new Event('click'));
            }, false);

            submitBtn.addEventListener('click', function() {
                submitBtn.innerText = "{{ __('Redirecting..') }}";
                submitBtn.disabled = true;

                var form = document.getElementById('moneroo_form');
                var formData = new FormData(form);

                fetch("{{ route('payease.moneroo') }}", {
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        method: 'POST',
                        body: formData
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(session) {
                        if (session.hasOwnProperty('msg')) {
                            alert(session.msg);
                            var cancelUrlInput = document.querySelector('input[name="cancel_url"]');
                            if (cancelUrlInput) {
                                window.location = cancelUrlInput.value;
                            }
                            return;
                        }

                        if (session.checkout_url) {
                            window.location = session.checkout_url;
                        } else {
                            alert("{{ __('Unable to initiate Moneroo payment.') }}");
                            var cancelUrlInput = document.querySelector('input[name="cancel_url"]');
                            if (cancelUrlInput) {
                                window.location = cancelUrlInput.value;
                            }
                        }
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                        alert("{{ __('Something went wrong. Please try again.') }}");
                        var cancelUrlInput = document.querySelector('input[name="cancel_url"]');
                        if (cancelUrlInput) {
                            window.location = cancelUrlInput.value;
                        }
                    });
            });
        })();
    </script>
</body>

</html>
