/* global grecaptcha */
/**
 * WPOrLogin: v2 widget and v3 just-in-time tokens on native form submission.
 * Security decisions remain on the server.
 */
(function () {
    'use strict';

    var settings = window.wporloginRecaptchaSettings || {};
    var initialized = false;

    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            callback();
        }
    }

    function showError(form) {
        var message = form.querySelector('.wporlogin-recaptcha-error');
        if (!message) {
            message = document.createElement('p');
            message.className = 'wporlogin-recaptcha-error';
            message.setAttribute('role', 'alert');
            message.style.color = '#b32d2e';
            form.appendChild(message);
        }
        message.textContent = settings.error || 'reCAPTCHA is unavailable. Please try again.';
    }

    function installV2() {
        var containers = document.querySelectorAll('[data-wporlogin-recaptcha="v2"]');
        for (var i = 0; i < containers.length; i++) {
            if (containers[i].getAttribute('data-wporlogin-rendered')) {
                continue;
            }
            try {
                window.grecaptcha.render(containers[i], {sitekey: settings.siteKey});
                containers[i].setAttribute('data-wporlogin-rendered', '1');
            } catch (error) {
                var form = containers[i].closest('form');
                if (form) {
                    showError(form);
                }
            }
        }
    }

    function installV3() {
        var inputs = document.querySelectorAll('[data-wporlogin-recaptcha="v3"]');
        for (var i = 0; i < inputs.length; i++) {
            (function (input) {
                var form = input.form || input.closest('form');
                if (!form || form.getAttribute('data-wporlogin-recaptcha-bound')) {
                    return;
                }
                form.setAttribute('data-wporlogin-recaptcha-bound', '1');
                var pending = false;
                var readyToSubmit = false;

                form.addEventListener('submit', function (event) {
                    if (readyToSubmit) {
                        readyToSubmit = false;
                        return;
                    }

                    event.preventDefault();
                    event.stopImmediatePropagation();
                    if (pending) {
                        return;
                    }

                    pending = true;
                    input.value = '';
                    var submitter = event.submitter || null;
                    if (!window.grecaptcha || typeof window.grecaptcha.ready !== 'function') {
                        pending = false;
                        showError(form);
                        return;
                    }

                    window.grecaptcha.ready(function () {
                        try {
                            Promise.resolve(window.grecaptcha.execute(settings.siteKey, {
                                action: input.getAttribute('data-context')
                            })).then(function (token) {
                                pending = false;
                                if (typeof token !== 'string' || !token) {
                                    showError(form);
                                    return;
                                }
                                input.value = token;
                                readyToSubmit = true;
                                if (typeof form.requestSubmit === 'function') {
                                    if (submitter && submitter.form === form) {
                                        form.requestSubmit(submitter);
                                    } else {
                                        form.requestSubmit();
                                    }
                                } else {
                                    // Compatibility fallback for older browsers.
                                    if (submitter && submitter.name) {
                                        var marker = document.createElement('input');
                                        marker.type = 'hidden';
                                        marker.name = submitter.name;
                                        marker.value = submitter.value;
                                        form.appendChild(marker);
                                    }
                                    form.submit();
                                }
                            }).catch(function () {
                                pending = false;
                                readyToSubmit = false;
                                showError(form);
                            });
                        } catch (error) {
                            pending = false;
                            readyToSubmit = false;
                            showError(form);
                        }
                    });
                }, true);
            }(inputs[i]));
        }
    }

    function initialize() {
        if (initialized) {
            return;
        }
        initialized = true;
        if (settings.version === 'v2' && window.grecaptcha) {
            installV2();
        } else if (settings.version === 'v3') {
            installV3();
        }
    }

    // Google triggers this for v2, potentially before the form exists.
    window.wporloginRecaptchaOnload = function () {
        onReady(initialize);
    };

    if (settings.version === 'v3') {
        onReady(initialize);
    }
}());
