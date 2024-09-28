document.addEventListener('DOMContentLoaded', () => {
    const selectBox = document.getElementById('total-price');
    const gameList = document.getElementById('game-list');
    const optionsContainer = document.getElementsByClassName('row')[0];
    const options = optionsContainer.querySelectorAll('.form-check input');


    options.forEach(option => {
        option.addEventListener('change', () => {
            let caution = 0;
            let selectedGames = [];

            Array.from(options)
                .filter(option => option.checked)
                .map(option => {
                    const game = document.querySelector(`label[for="${option.id}"]`).children[0];

                    caution += parseInt(game.children[2].innerText.split('€')[0])
                    selectedGames.push(" " + game.children[0].innerText);
                });

            selectBox.textContent = caution;
            gameList.innerHTML = selectedGames.toString();
        });
    });
});