let boardGames = null;
let games = {};
const totalCaution = document.getElementById('total-price');
document.addEventListener('DOMContentLoaded', function() {
    boardGames = new TomSelect('.tom-select', {
        valueField: 'id',
        labelField: 'label',
        searchField: 'label',
        onChange: function (value){
            let caution = 0;
            value.map((game) => {
                caution += games[game];
            });
            totalCaution.textContent = caution;
        },
        render: {
            option: function(data, escape) {
                let elements = data.label.split('|');

                games[elements[0]] = parseInt(elements[3]);

                return `<div><span class="title">${escape(elements[0])}<span class="state ${escape(elements[2])}"> (${escape(elements[1])})</span></span><span class="price"> Caution: ${escape(elements[3])}€</span></div>`;
            },
            item: function (data, escape) {
                return `<div>${escape(data.id)}<a onclick="onDeleteItem('${escape(data.id)}')" style="border-left: 1px solid #dee2e6; margin-left: 5px; padding: 0 5px;">×</a></div>`
            }
        }
    });
});

function onDeleteItem(id){
    boardGames.removeItem(id);
}