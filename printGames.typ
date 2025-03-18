#set page(margin: 2cm)
#set text(12pt, lang: "fr")
#set par(justify: true)
#set list(marker: [--])

#show heading: it => {
  it
  v(0.5em)
}

#let parties = json("parties.json");

#for partie in parties [
  #text(20pt, align(center)[= #partie.title])

  == Description :
  #eval(
    partie.description
      .replace("#", "=== ")
      .replace(regex("\*\*(\w*)\*\*"), match => "#strong[" + match.captures.at(0) + "]")
      .replace(regex("\*(\w+)\*"), match => "#emph[" + match.captures.at(0) + "]")
      .replace(regex(`__(\w*)__`.text), match => "#underline[" + match.captures.at(0) + "]")
      .replace(regex("([^\r])\r([^\r])"),match => match.captures.join("\\"))
    , mode: "markup"
  )

  #text(14.5pt)[*Créneau :*] #partie.gameSlot.text

  == Joueurs (#partie.seats places) :
  #list(
    ..partie.players.map(it => it.pseudo)
  )

  #pagebreak(weak: true)
]