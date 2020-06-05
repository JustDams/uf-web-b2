function loadImage(title, idGame) {
    fetch('https://api.rawg.io/api/games?search="' + title + '"')
        .then(res => res.json())
        .then((out) => {
            return document.getElementById('dynamicImg' + idGame).src = (out.results[0].background_image);
        }).catch(err => console.error(err))
}

function function7days() {
    let x = document.getElementById("recent");
    let y = document.getElementById("7days");
    if (x.style.display === "none") {
        x.style.display = "flex";
        y.style.display = "none";
    } else {
        y.style.display = "flex";
        x.style.display = "none";
    }
}

