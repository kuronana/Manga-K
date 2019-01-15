// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!   intervale couleur sur le bouton anime !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

let as = document.querySelectorAll(".clignote");
for (let i = 0;i < as.length; i++) {
    let a = as[i];
    window.setInterval(function () {
        a.classList.toggle("orange")
    }, 1000);
}

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});