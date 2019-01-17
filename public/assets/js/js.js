

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!   Tooltips sur les icone du menu   !!!!!!!!!!!!!!!!!!!!!!!!!!


$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!   Changement de couleur pour le burger menu   !!!!!!!!!!!!!!!!!!!!!!!!!!

$(window).scroll(function() {
    let scroll = $(window).scrollTop();
    if (scroll > 50) {
        $(".burgerMenu").removeClass("bg-dark").addClass("bg-primary");
    } else {
        $(".burgerMenu").removeClass("bg-primary").addClass("bg-dark");
    }
});