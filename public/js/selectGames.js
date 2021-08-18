let slots = [];
Array.from(document.getElementsByClassName('slots')).forEach(inp => {
    slots[inp.getAttribute('data')] = {id: parseInt(inp.getAttribute('data'), 10), status: true, uptodate: false};
});

Array.from(document.getElementsByClassName('btn-filtre')).forEach(button => {
    button.querySelector('input').onchange = (e) => {
        const idx = parseInt(e.target.getAttribute('data'));
        slots[idx] = { id: idx, status: slots[idx].status, uptodate: slots[idx].status === e.target.checked };
        if (e.target.checked) {
            button.removeAttribute('inactive');
        } else {
            button.setAttribute('inactive', true);
        }
        updateGames();
    }
});

function updateGames() {
    for(slotid in slots) {
        const gid = document.getElementsByClassName('ghid_' + slotid);
        if (!slots[slotid].uptodate) {
            if (slots[slotid].status) {
                Array.from(gid).forEach(g => {$(g).slideUp()});
            } else {
                Array.from(gid).forEach(g => $(g).show("slow"));
            }
            slots[slotid].status = !slots[slotid].status;
            slots[slotid].uptodate = true;
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
        slots = slots.map(slot => {return {id: slot.id, status: false, uptodate: false}});
    } else {
        toggleAll.querySelector('span').innerText="Tout selectionner";
        Array.from(document.getElementsByClassName('btn-filtre')).forEach(button => {
            button.querySelector('input').checked = false;
            button.setAttribute('inactive', true);
        });
        slots = slots.map(slot => {return {id: slot.id, status: true, uptodate: false}});
    }
    updateGames();
};
