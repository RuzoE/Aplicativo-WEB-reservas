document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('audit-filter-form');
    const loader = document.getElementById('audit-filter-loader');
    const submitBtn = document.getElementById('filter-submit-btn');
    const feedback = document.getElementById('filter-feedback');

    if (!form || !loader || !submitBtn || !feedback) {
        return;
    }

    form.addEventListener('submit', function () {
        submitBtn.setAttribute('disabled', 'disabled');
        submitBtn.style.opacity = '0.7';
        submitBtn.style.cursor = 'not-allowed';
        feedback.style.display = 'inline';
        loader.classList.add('active');
    });
});
