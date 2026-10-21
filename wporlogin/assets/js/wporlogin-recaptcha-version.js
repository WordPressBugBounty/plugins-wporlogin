/** Show only the fields and documentation for the selected CAPTCHA service. */
document.addEventListener('DOMContentLoaded', () => {
    const selector = document.getElementById('recaptcha_version_wporlogin');
    if (!selector) return;

    const sections = document.querySelectorAll('[data-captcha-provider]');
    const refresh = () => {
        const selected = selector.value;
        sections.forEach((section) => {
            const providers = (section.dataset.captchaProvider || '').split(' ');
            section.hidden = !providers.includes(selected);
        });
    };

    selector.addEventListener('change', refresh);
    refresh();
});
