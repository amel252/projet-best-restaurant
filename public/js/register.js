document.addEventListener('DOMContentLoaded', function () {

    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');

    const nextButton = document.getElementById('nextStep');
    const previousButton = document.getElementById('previousStep');

    const indicator1 = document.getElementById('stepIndicator1');
    const indicator2 = document.getElementById('stepIndicator2');

    nextButton.addEventListener('click', function () {

        const requiredFields = step1.querySelectorAll('input[required]');
        let valid = true;

        requiredFields.forEach(function (field) {
            if (!field.checkValidity()) {
                field.reportValidity();
                valid = false;
            }
        });
        if (!valid) {
            return;
        }

        step1.style.display = 'none';
        step2.style.display = 'block';

        indicator1.classList.replace('bg-dark', 'bg-secondary');
        indicator2.classList.replace('bg-secondary', 'bg-dark');
    });

    previousButton.addEventListener('click', function () {

        step2.style.display = 'none';
        step1.style.display = 'block';

        indicator2.classList.replace('bg-dark', 'bg-secondary');
        indicator1.classList.replace('bg-secondary', 'bg-dark');
    });
});

