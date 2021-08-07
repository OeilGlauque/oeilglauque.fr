Array.from(document.getElementsByClassName('btn-filtre')).forEach(button => {
    button.querySelector('input').onchange = (e) => {
        const gid = document.getElementsByClassName('ghid_' + e.target.getAttribute('data'));
        if (e.target.checked) {
            button.removeAttribute('inactive');
            Array.from(gid).forEach(g => $(g).show("slow"));
        } else {
            button.setAttribute('inactive', true);
            Array.from(gid).forEach(g => {console.log(g); $(g).slideUp()});
        }
    }
});

const toggleAll = document.getElementById('selectAll');
toggleAll.querySelector('input').onchange= (e) => {
    if (e.target.checked) {
        toggleAll.querySelector('span').innerText="Tout désélectionner";
        Array.from(document.getElementsByClassName('btn-filtre')).forEach(button => {
            button.querySelector('input').checked = true;
            button.querySelector('input').dispatchEvent(new Event('change'));
        });
    } else {
        toggleAll.querySelector('span').innerText="Tout selectionner";
        Array.from(document.getElementsByClassName('btn-filtre')).forEach(button => {
            button.querySelector('input').checked = false;
            button.querySelector('input').dispatchEvent(new Event('change'));
        });
    }
};
