/**
 * LogixPulse — General scripts
 */

document.addEventListener('DOMContentLoaded', function () {

    var otpInputs = document.querySelectorAll('.otp-input');
    otpInputs.forEach(function (input, index) {
        input.addEventListener('input', function () {
            // Move to next input
            if (input.value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', function (e) {
            // Move to previous on backspace
            if (e.key === 'Backspace' && input.value === '' && index > 0) {
                otpInputs[index - 1].focus();
            }
        });

        input.addEventListener('focus', function () {
            input.select();
        });
    });

});