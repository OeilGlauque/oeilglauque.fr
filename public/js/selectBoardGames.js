document.addEventListener('DOMContentLoaded', () => {
    const selectBox = document.getElementById('total-price');
    const optionsContainer = document.getElementsByClassName('row')[0];
    const options = optionsContainer.querySelectorAll('.form-check input');

    options.forEach(option => {
        option.addEventListener('change', () => {
            let caution = 0;
            Array.from(options)
                .filter(option => option.checked)
                .map(option => caution += parseInt(document.querySelector(`label[for="${option.id}"]`).innerText.split('-')[2].split('€')[0]));
            selectBox.textContent = caution;
        });
    });
});