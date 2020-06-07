document.onload = apiCall("https://www.speedrun.com/api/v1/games?offset=" + getUrlParameter('offset'));

function apiCall(url) {
    fetch(url)
        .then(response => response.json())
        .then(json => {
            json.data.forEach(game => {
                createCardGame(game)
            })
            offset = json.pagination.offset
            json.pagination.links.forEach(page => {
                createPagination(page, offset)
            })
        })
}

function createCardGame(game) {
    let gamesIndex = document.getElementById('games-index')

    let divCard = document.createElement('div')
    divCard.className = "col-md-3 mb-5"

    let card = document.createElement('div')
    card.className = "card"

    let cardBody = document.createElement('div')
    cardBody.className = "card-body"

    let cardTitle = document.createElement('h5')
    cardTitle.className = "card-title"
    cardTitle.appendChild(document.createTextNode(game.names.international))

    let cardSubtitle = document.createElement('h6')
    cardSubtitle.className = "card-subtitle mb-2 text-muted position-relative"
    cardSubtitle.appendChild(document.createTextNode("Unknown - " + (game.created !== null ? game.created.substring(0, 4) : 'Unknown')))

    let img = new Image()
    img.className = "card-img game-image"
    img.style = "background-color: grey;"
    if (game.assets["cover-large"].uri === null) {
        img.src = "../images/default.jpg"
    } else {
        img.src = game.assets["cover-large"].uri
    }

    let description = document.createElement("p")
    description.className = "card-text"
    description.appendChild(document.createTextNode("No description for the moment..."))

    let externLink = document.createElement("a")
    externLink.className = "btn btn-secondary btn-buy"
    externLink.appendChild(document.createTextNode("Read more"))
    externLink.href = game.weblink

    card.appendChild(cardBody)
    cardBody.appendChild(cardTitle)
    cardBody.appendChild(cardSubtitle)
    cardBody.appendChild(img)
    cardBody.appendChild(description)
    card.appendChild(externLink)
    divCard.appendChild(card)

    gamesIndex.appendChild(divCard)
}


function createPagination(page, offset) {
    let paginationIndex = document.getElementById('pagination')

    let li = document.createElement('li')
    let a = document.createElement('a')
    let span = document.createElement('span')


    if (page.rel == "prev") {
        li.className = "page-item"
        a.className = "page-link"
        a.href = "?offset=" + (offset - 20)
        span.appendChild(document.createTextNode("«"))

        li.appendChild(a)
        a.appendChild(span)

        document.getElementById('pagination').appendChild(li)

    } else if (page.rel == "next") {
        liPage = document.createElement('li')
        liPage.className = "page-item active"
        aPage = document.createElement('a')
        aPage.className = "page-link"
        spanPage = document.createElement('span')
        spanPage = document.createTextNode((offset / 20) + 1)

        aPage.appendChild(spanPage)
        liPage.appendChild(aPage)

        li.className = "page-item"
        a.className = "page-link"
        a.href = "?offset=" + (offset + 20)
        span.idName = "next"
        span.appendChild(document.createTextNode("»"))

        a.appendChild(span)
        li.appendChild(a)

        document.getElementById('pagination').appendChild(liPage)
        document.getElementById('pagination').appendChild(li)
    }
}

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};