$.get( "/parties/slots", ( data ) => {
    data.forEach((i) => {
        $("#filtre").append('<label class="btn btn-secondary btn-filtre active"><input type="checkbox" checked autocomplete="off">'+i.text+'</label>');
    });

    $(".btn-filtre").click((e) => {
        var creneau = $(e.delegateTarget).text();
        

        if(!$(e.delegateTarget).hasClass("active")) {
            $(".gameShard").each((i) => {
                if($($($(".gameShard").get(i)).find(".slot").get(0)).text() == creneau) {
                    $($(".gameShard").get(i)).show("slow");
                }
            });
        }
        else{
            $(".gameShard").each((i) => {
                if($($($(".gameShard").get(i)).find(".slot").get(0)).text() == creneau) {
                    $($(".gameShard").get(i)).slideUp();
                }
            });
        }
    });
}); 