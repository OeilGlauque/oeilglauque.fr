let games = []
Array.from(document.getElementsByClassName('gameShard')).forEach(inp => {
    data = inp.getAttribute('data').split(',')
    games.push({id: parseInt(inp.getAttribute('data'), 10), slot_id:parseInt(data[0]),  slot_active: true, slot_uptodate: false, 
                                                            tags_values:data[1],        tags_active: true, tags_uptodate: false, element: inp});
});

Array.from(document.getElementsByClassName('btn-filtre')).forEach(button => {
    button.querySelector('input').onchange = (e) => {
        const idx = parseInt(e.target.getAttribute('data'));
        if (e.target.checked) {
            button.removeAttribute('inactive');
        } else {
            button.setAttribute('inactive', true);
        }
        updateGames(idx, e.target.checked);
    }
});

function updateGames(slot_id, checked) {
    games.forEach(game => {
        if(slot_id == game.slot_id) {
            game.slot_uptodate = (game.slot_active === checked);
        }
        if(!game.slot_uptodate) {
            game.slot_active = !game.slot_active;
            game.slot_uptodate = true;
            if (game.slot_active && game.tags_active) {
                $(game.element).show("slow");
            } else {
                $(game.element).slideUp()
            }
        }
    });
}
function clearGames(checked) {
    games.forEach(game => {
        game.slot_active = checked
        if (game.slot_active && game.tags_active) {
            $(game.element).show("slow");
        } else {
            $(game.element).slideUp()
        }
    });
}

const toggleAll = document.getElementById('selectAll');
toggleAll.querySelector('input').onchange= (e) => {
    if (e.target.checked) {
        toggleAll.querySelector('span').innerText="Tout désélectionner";
        Array.from(document.getElementsByClassName('btn-filtre')).forEach(button => {
            button.querySelector('input').checked = true;
            button.removeAttribute('inactive');
        });
        clearGames(true);
    } else {
        toggleAll.querySelector('span').innerText="Tout sélectionner";
        Array.from(document.getElementsByClassName('btn-filtre')).forEach(button => {
            button.querySelector('input').checked = false;
            button.setAttribute('inactive', true);
        });
        clearGames(false);
    }
};
