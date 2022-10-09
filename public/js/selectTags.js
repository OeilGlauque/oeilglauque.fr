let tagList = {}

Array.from(document.getElementsByClassName('tags')).forEach(inp => {
    inp.innerHTML.split(', ').forEach(tag => tagList[tag] = true);
});

console.log(tagList)

for(tag in tagList){

    
    //$('table').children[0].appendChild($('table').children[0].childNodes[1].cloneNode(true))
    console.log(document.getElementById("filter"))
    f = document.getElementById("filter")    
    f.innerHTML += "<label inactive class=\"btn btn-tag mb-3 me-3 me-sm-0 d-flex justify-content-between align-items-center\" onmouseup=\"updateTags(this,'" +tag+"')\">" +
                     "<div class=\"d-lg-block d-none\"></div>" +
                     "<span>"+tag+"</span>" +
                     "<input type=\"checkbox\" class=\"d-lg-block d-none tag\" data=\"\" autocomplete=\"off\" />" +
                   "</label>" 
}


function updateTags(el, tag) {
    checked = !el.childNodes[2].checked // Don't ask just don't

    if (checked) {
        el.removeAttribute('inactive');
    } else {
        el.setAttribute('inactive', true);
    }
    games.forEach(game => {
        if(game.tags_values.split(";").includes(tag)) {
            game.tags_uptodate = (game.tags_active === checked);
        }
        if(!game.tags_uptodate) {
            game.tags_active = !game.tags_active;
            game.tags_uptodate = true;
            if (game.slot_active && game.tags_active) {
                $(game.element).show("slow");
            } else {
                $(game.element).slideUp()
            }
        }
    });
}

function clearTags(checked) {
    games.forEach(game => {
        game.tags_active = checked
        game.tags_uptodate = true
        if (game.slot_active && game.tags_active) {
            $(game.element).show("slow");
        } else {
            $(game.element).slideUp()
        }
    });
}


function selectAllTags(el) {
    checked = !el.childNodes[3].checked
    if (checked) {
        el.querySelector('span').innerText="Tout désélectionner";
        Array.from(document.getElementsByClassName('btn-tag')).forEach(button => {
            button.querySelector('input').checked = true;
            button.removeAttribute('inactive');
        });
        clearTags(true);
    } else {
        el.querySelector('span').innerText="Tout sélectionner";
        Array.from(document.getElementsByClassName('btn-tag')).forEach(button => {
            button.querySelector('input').checked = false;
            button.setAttribute('inactive', true);
        });
        clearTags(false);
    }
}

                   
