function loadImage(title, idGame) {
    fetch('https://api.rawg.io/api/games?search="' + title + '"')
        .then(res => res.json())
        .then((out) => {
            console.log(out.results[0].background_image)
            return document.getElementById('dynamicImg' + idGame).src = (out.results[0].background_image);
        }).catch(err => console.error(err))
}