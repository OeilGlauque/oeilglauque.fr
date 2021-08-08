let slots = [];
Array.from(document.getElementsByClassName('slots')).forEach(inp => {
    slots[inp.getAttribute('data')] = {status: true, uptodate: false};
});

Array.from(document.getElementsByClassName('btn-filtre')).forEach(button => {
    button.querySelector('input').onchange = (e) => {
        const idx = e.target.getAttribute('data');
        slots[idx] = { status: slots[idx].status, uptodate: slots[idx].status === e.target.checked };
        if (e.target.checked) {
            button.removeAttribute('inactive');
        } else {
            button.setAttribute('inactive', true);
        }
        updateGames();
    }
});

function updateGames() {
    for(let i = 1; i < slots.length; i++) {
        const gid = document.getElementsByClassName('ghid_' + i);
        if (!slots[i].uptodate) {
            if (slots[i].status) {
                Array.from(gid).forEach(g => {$(g).slideUp()});
            } else {
                Array.from(gid).forEach(g => $(g).show("slow"));
            }
            slots[i].status = !slots[i].status;
            slots[i].uptodate = true;
        }
    }
}

const toggleAll = document.getElementById('selectAll');
toggleAll.querySelector('input').onchange= (e) => {
    if (e.target.checked) {
        toggleAll.querySelector('span').innerText="Tout désélectionner";
        Array.from(document.getElementsByClassName('btn-filtre')).forEach(button => {
            button.querySelector('input').checked = true;
            button.removeAttribute('inactive');
        });
        slots = slots.map(() => {return {status: false, uptodate: false}});
    } else {
        toggleAll.querySelector('span').innerText="Tout selectionner";
        Array.from(document.getElementsByClassName('btn-filtre')).forEach(button => {
            button.querySelector('input').checked = false;
            button.setAttribute('inactive', true);
        });
        slots = slots.map(() => {return {status: true, uptodate: false}});
    }
    updateGames();
};
