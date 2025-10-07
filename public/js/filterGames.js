let active_slots = {}
let active_tags = {}

let btn_slot = []
let btn_tag = []

let games = []
let tags = new Set()

let searchTimeout;

let searchInput = document.getElementById('search-input');
let fullCheckbox = document.getElementById('full-checkbox');
let emptyResult = document.getElementById('empty-result');


fullCheckbox.addEventListener('change', () => {
    updateGamesList();
})

for (let el of document.getElementsByClassName('gameShard')) {
    data = el.getAttribute('data').split('|#')
    games.push({
        id: data[0],
        title: data[4],
        slot_id: data[1],
        tags: data[2].split(';'),
        freeSeats: data[3],
        element: el
    })
    for (let tag of data[2].split(';')) {
        tags.add(tag)
    }
}


var tag_container = document.getElementsByClassName('btn-filters').item(1)

for (let tag of tags) {
    if (tag !== "") {
        active_tags[tag] = false
        let lb = document.createElement("label")
        lb.setAttribute('inactive','true')
        lb.setAttribute('class',"btn btn-filter") //btn-tag mb-3 flex-grow-1 d-flex justify-content-between align-items-center
        lb.appendChild(document.createElement("div"))
        let span = document.createElement('span')
        span.innerText = tag
        lb.appendChild(span)
        let input = document.createElement('input')
        input.setAttribute('type','checkbox')
        input.setAttribute('autocomplete',"off")
        input.setAttribute('data',tag)
        lb.appendChild(input)

        btn_tag.push({label: lb, input: input})
        
        tag_container.appendChild(lb)

        input.onchange = (e) => {
            if (e.target.checked) {
                lb.removeAttribute('inactive')
            } else {
                lb.setAttribute('inactive','true')
            }
            let tag = e.target.getAttribute('data')
            active_tags[tag] = e.target.checked
            updateGamesList()
        }
    }
}

for (let button of document.getElementsByClassName('btn-slot')) {
    let input = button.querySelector('input')

    btn_slot.push({label: button, input: input})

    active_slots[input.getAttribute('data')] = false

    input.onchange = (e) => {
        if (e.target.checked) {
            button.removeAttribute('inactive')
        } else {
            button.setAttribute('inactive','true')
        }
        let slot = e.target.getAttribute('data')
        active_slots[slot] = e.target.checked
        updateGamesList()
    }
}

function searchGames() {
    let query = searchInput.value.trim().toLowerCase();
    games.forEach(game => {
        if (!game.title.toLowerCase().includes(query.toLowerCase())) {
            game.element.style.display = "none"
        }
    });
}

searchInput.addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        updateGamesList();
    }, 300);
});

function updateGamesList() {
    if (Object.values(active_slots).every((el) => el == false) && Object.values(active_tags).every((el) => el == false)) {
        games.forEach(game => {
            game.element.style.display = ""
        })
    } else if (Object.values(active_slots).every((el) => el == false)) {
        games.forEach(game => {
            let active_tag = false
    
            for (let tag of game.tags) {
                if (active_tags[tag]) {
                    active_tag = true
                    break
                }
            }

            if (active_tag) {
                game.element.style.display = ""
            } else {
                game.element.style.display = "none"
            }
        })
    } else if (Object.values(active_tags).every((el) => el == false)) {
        games.forEach(game => {
            if (active_slots[game.slot_id]) {
                game.element.style.display = ""
            } else {
                game.element.style.display = "none"
            }
        })
    } else {
        games.forEach(game => {
            let active_tag = false
    
            for (let tag of game.tags) {
                if (active_tags[tag]) {
                    active_tag = true
                    break
                }
            }
            if (active_slots[game.slot_id] && active_tag) {
                game.element.style.display = ""
            } else {
                game.element.style.display = "none"
            }
        })
    }

    searchGames();

    if (fullCheckbox.checked) {
        games.forEach(game => {
            if (game.freeSeats < 1) {
                game.element.style.display = "none"
            }
        })
    }

    let match = 0;
    games.forEach(game => {
        if (game.element.style.display !== "none") {
            match++;
        }
    })

   if (match === 0) {
        emptyResult.style.display = "";
   }else{
       emptyResult.style.display = "none";
    }
}

let toggleAllSlots = document.getElementById("selectAllSlots")
toggleAllSlots.querySelector('input').onchange = (e) => {
    for (let slot in active_slots) {
        active_slots[slot] = e.target.checked
    }
    if (e.target.checked) {
        toggleAllSlots.querySelector('span').innerText = "Tout désélectionner"
        btn_slot.forEach(btn => {
            btn.input.checked = e.target.checked
            btn.label.removeAttribute('inactive')
        })
    } else {
        toggleAllSlots.querySelector('span').innerText = "Tout sélectionner"
        btn_slot.forEach(btn => {
            btn.input.checked = e.target.checked
            btn.label.setAttribute('inactive','true')
        })
    }
    updateGamesList()
}


let toggleAllTags = document.getElementById('selectAllTags')
toggleAllTags.querySelector('input').onchange = (e) => {
    for (let tag in active_tags) {
        active_tags[tag] = e.target.checked
    }
    if (e.target.checked) {
        toggleAllTags.querySelector('span').innerText = "Tout désélectionner"
        btn_tag.forEach(btn => {
            btn.input.checked = e.target.checked
            btn.label.removeAttribute('inactive')
        })
    } else {
        toggleAllTags.querySelector('span').innerText = "Tout sélectionner"
        btn_tag.forEach(btn => {
            btn.input.checked = e.target.checked
            btn.label.setAttribute('inactive','true')
        })
    }
    updateGamesList()
}

updateGamesList()