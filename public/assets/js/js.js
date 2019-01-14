// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!   intervale couleur sur le bouton anime !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

let as = document.querySelectorAll(".clignote");
for (let i = 0;i < as.length; i++) {
    let a = as[i];
    window.setInterval(function () {
        a.classList.toggle("orange")
    }, 1000);
}




// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!   test js addEnventListener   !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/*
let ps = document.querySelectorAll('p');

for (let i = 0; i < ps.length; i++) {
    let p = ps[i];
    let rougit = function () {
        p.classList.toggle('red')
    };
    p.addEventListener('click', rougit);
}
*/

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!   test js spoiler   !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

/*
let button = document.querySelector('.spoiler button');
button.addEventListener('click', function () {
    button.nextElementSibling.classList.add('visible')
    button.parentNode.removeChild(button)
});
*/
/*
let elements = document.querySelectorAll('.spoiler');

let createSpoilerButton = function (element) {

    // On crée le span.spoiler-content
    let span = document.createElement('span');
    span.className = 'spoiler-content';
    span.innerHTML = element.innerHTML;

    // On crée le bouton
    let button = document.createElement('button');
    button.textContent = 'Afficher le spoil';

    //On ajoute les élément au DOM
    element.innerHTML = '';
    element.appendChild(button);
    element.appendChild(span);

    //On met l'écouteur d'événement
    button.addEventListener('click', function () {
        span.classList.toggle('visible');
    })

};

for (let i = 0; i < elements.length; i++) {
    createSpoilerButton(elements[i])
}
*/