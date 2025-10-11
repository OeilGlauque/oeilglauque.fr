#set page(margin: 2cm)
#set text(12pt, lang: "fr")
#set par(justify: true)
#set list(marker: [--])

#show heading: it => {
  it
  v(0.4em)
}

#let parties = json("parties.json");

#for partie in parties [
  #align(center, text(20pt)[= #partie.title])

  #eval(
    partie.description
      .replace("#", "== ")
      .replace(regex("\*\*(\w*)\*\*"), match => "#strong[" + match.captures.at(0) + "]")
      .replace(regex("\*(\w+)\*"), match => "#emph[" + match.captures.at(0) + "]")
      .replace(regex(`__(\w*)__`.text), match => "#underline[" + match.captures.at(0) + "]")
      .replace(regex("([^\r])\r([^\r])"),match => match.captures.join("\\"))
    , mode: "markup"
  )

  #if partie.tags != "" [
    _*Tags :* #partie.tags.replace(";", ", ") _
  ]

  #line(start: (10%,0%), end: (90%, 0%))
  
  #text(13pt)[Proposée par #partie.author.pseudo sur le créneau *#partie.gameSlot.text*.]

  == Participant(e)s (#partie.seats places) :
  #list(
    spacing: 1em,
    ..(partie.players.map(p => p.pseudo) + range(partie.seats - partie.players.len()).map(it => []))
  )

  #pagebreak(weak: true)
]