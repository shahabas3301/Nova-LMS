<html>

<head>
    <title><?php echo e(__('Moneroo Payment Gateway')); ?></title>
</head>

<body>
    <div class="moneroo-payment-wrapper">
        <div class="moneroo-payment-inner-wrapper">
            <input type="hidden" name="order_id" id="order_id_input" value="<?php echo e($moneroo_data['order_id']); ?>" />

            <form id="moneroo_form">
                <?php $__currentLoopData = $moneroo_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field_name => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <input type="hidden" name="<?php echo e($field_name); ?>" value="<?php echo e($value); ?>" />
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                submitBtn.innerText = "<?php echo e(__('Redirecting..')); ?>";
                submitBtn.disabled = true;

                var form = document.getElementById('moneroo_form');
                var formData = new FormData(form);

                fetch("<?php echo e(route('payease.moneroo')); ?>", {
                        headers: {
                            "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>",
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
                            alert("<?php echo e(__('Unable to initiate Moneroo payment.')); ?>");
                            var cancelUrlInput = document.querySelector('input[name="cancel_url"]');
                            if (cancelUrlInput) {
                                window.location = cancelUrlInput.value;
                            }
                        }
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                        alert("<?php echo e(__('Something went wrong. Please try again.')); ?>");
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
<?php /**PATH /home/nidheesh/workspace/Nova-LMS/Modules/LaraPayease/resources/views/moneroo.blade.php ENDPATH**/ ?>