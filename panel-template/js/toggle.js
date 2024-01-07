
var toggle = document.querySelector('nav').querySelectorAll('.toggle');

if (document.querySelectorAll(".active").length) {
    var active = document.querySelector("nav .active");
    active.querySelector(".toggle").classList.toggle('open');

    if (active.querySelectorAll('ul').length) {

        active.querySelector('ul').style.display = 'block';

    }
}


toggle.forEach(r => {
    r.addEventListener("click", () => {
        var ul = r.parentElement.parentElement.querySelectorAll('ul');
        r.classList.toggle('open');
        ul.forEach(rw => {
            if (rw.style.display == "block") {

                rw.style.display = 'none';

            } else {

                rw.style.display = 'block';

            }
        });
    });
})


// Toggle filter
var filtr_button = document.querySelectorAll(".filtr_button");

filtr_button.forEach(item => {
    item.addEventListener("click", () => {

        document.querySelector("#filter_inputs").classList.toggle("show");
        document.querySelector("#sort_inputs").classList.remove("show");

    })
})

// Toggle sort
var filtr_button = document.querySelectorAll(".sort_button");

filtr_button.forEach(item => {
    item.addEventListener("click", () => {

        document.querySelector("#sort_inputs").classList.toggle("show");
        document.querySelector("#filter_inputs").classList.remove("show");

    })
})

// Close popup
var close_popup = document.querySelectorAll('.close');
close_popup.forEach(item => {
    item.addEventListener("click", () => {
        item.closest('.popup').style.display = "none";
    });
    setTimeout(() => {
        item.closest('.popup').style.display = "none";
    }, 10000)
})

// toggle menu
var menu_hamburger = document.querySelectorAll(".menu_hamburger");
menu_hamburger.forEach(element => {
    element.addEventListener("click", () => {
        element.closest(".container").classList.toggle("hidden");
    })
});