document.addEventListener("DOMContentLoaded", onLoad());

function onLoad() {

}

// Move element


var dataToMove;

function newActive(element) {

    var container = element.querySelectorAll(".delete");
    container.forEach(item => {
        item.addEventListener("click", (event) => deleteElement(item));
    });

    var addButton = element.querySelectorAll(".add");
    addButton.forEach(item => {
        item.addEventListener('click', (event) => add(event));
    });

    var setHeader = element.querySelectorAll(".select-box.h1");
    setHeader.forEach(item => item.addEventListener("click", (event) => setHeader1(item)));

    var setHeader = element.querySelectorAll(".select-box.h2");
    setHeader.forEach(item => item.addEventListener("click", (event) => setHeader2(item)));

    var setHeader = element.querySelectorAll(".select-box.h3");
    setHeader.forEach(item => item.addEventListener("click", (event) => setHeader3(item)));

    var setHeader = element.querySelectorAll(".select-box.p");
    setHeader.forEach(item => item.addEventListener("click", (event) => setParagraf(item)));

    var content = element.querySelectorAll(".content");
    content.forEach(item => {
        item.addEventListener('keydown', (event) => handleKeyPress(event, item));
        item.addEventListener('focus', () => {
            // let boxList = item.closest("#boxs").querySelectorAll(".box");
            // boxList.forEach(boxListItem => {
            //     boxListItem.querySelector('.select').classList.remove("show");
            // })
            // item.closest(".box").querySelector('.select').classList.add("show");
        });
        item.addEventListener('blur', () => {
            // item.closest(".box").querySelector('.select').classList.remove("show");
        });
    });

    var content = element.querySelectorAll(".option");
    content.forEach(item => {
        item.addEventListener('click', () => {
            let isShow = item.closest(".box").querySelector('.select').classList.contains("show");

            let boxList = item.closest("#boxs").querySelectorAll(".box");
            boxList.forEach(boxListItem => {
                boxListItem.querySelector('.select').classList.remove("show");
            })

            if (!isShow) {
                item.closest(".box").querySelector('.select').classList.toggle("show");
            }
        });
    });

    var draggable = element.querySelectorAll(".move");
    draggable.forEach(item => {
        item.addEventListener('dragstart', (event) => {
            dataToMove = item.closest(".box");
        });
    });

    var dropZone = element.querySelectorAll(".drop");
    dropZone.forEach(item => {
        move(item);
    });


}

function deleteElement(item) {
    var box = document.querySelectorAll('.box');
    if (box.length > 1)
        item.closest(".box").remove()
}

function handleDragOver(event) {
    event.preventDefault();
}

// Add new 


async function getContent(link) {
    return fetch(link)
        .then(response => response.text())
        .then(html => {
            return html;
        })
        .catch(error => {
            console.error('Wystąpił błąd podczas pobierania pliku HTML:', error);
        });
}

function handleKeyPress(event, item) {

    if (event.keyCode === 13) {
        event.preventDefault();
        add(event)
    }

    if (event.keyCode === 38) { // cursor up
        event.preventDefault();
        moveUp();
    }

    if (event.keyCode === 40) { // cursor down
        event.preventDefault();
        if (!moveDown()) {
            add(event);
        }
    }

    if (event.keyCode === 46) { // delete
        event.preventDefault();
        moveUp();
        deleteElement(item);
    }

}

function moveUp() {
    var sel = window.getSelection();
    var range = sel.getRangeAt(0);
    var currentElement = range.commonAncestorContainer.parentElement.closest(".box");

    if (currentElement.previousElementSibling) {
        var prevSibling = currentElement.previousElementSibling.querySelector('.content');
        prevSibling.focus();
    }
}

function moveDown() {
    var sel = window.getSelection();
    var range = sel.getRangeAt(0);
    var currentElement = range.commonAncestorContainer.parentElement.closest(".box");
    if (currentElement.nextElementSibling) {
        var prevSibling = currentElement.nextElementSibling.querySelector('.content');
        prevSibling.focus();
        return true;
    } else {
        return false
    }
}

function move(item) {
    item.addEventListener("dragleave", (event) => {
        item.classList.remove("on");
    });

    item.addEventListener("dragend", (event) => {
        item.classList.remove("on");
    });

    item.addEventListener("dragover", (event) => {
        event.preventDefault();
        item.classList.add("on");
    });

    item.addEventListener('drop', async (event) => {
        event.preventDefault();
        item.classList.remove("on");

        var data = event.dataTransfer.getData("element");
        item.closest(".box").insertAdjacentElement('afterend', dataToMove);
    });
}

async function getBlock() {
    var box = await getContent('http://192.168.0.117:8000/modules/text-editor/template/box.html');
    var text = await getContent('http://192.168.0.117:8000/modules/text-editor/template/text.html');
    var options = await getContent('http://192.168.0.117:8000/modules/text-editor/template/options.html');

    var newElement = document.createElement("div");
    newElement.classList.add("box");
    newElement.innerHTML = box;
    newElement.querySelector('.data').innerHTML = text;
    newElement.querySelector('.select').innerHTML = options;

    return newElement
}

async function add(event) {

    let newElement = await getBlock();

    event.target.closest(".box").after(newElement);

    newElement.querySelector('.content').focus();
    newActive(newElement)

}

async function addEnter() {

    let newElement = await getBlock();

    var existingElement = document.querySelector("#boxs");
    existingElement.appendChild(newElement);

    newElement.querySelector('.content').focus();
    newActive(newElement);
}

addEnter();


window.onload = function () {
    document.addEventListener('paste', function (e) {
        // console.log("paste handler");
        console.log(e.clipboardData.getData('text/plain'))
        e.preventDefault();
    });
}

// class MojTag extends HTMLElement {
//     constructor() {
//         super();
//     }

//     connectedCallback() {
//         this.innerHTML = "To jest mój niestandardowy element!";
//     }
// }

// // Rejestrujemy niestandardowy tag "moj-tag"
// customElements.define('moj-tag', MojTag);
// <moj-tag></moj-tag>

function removeOther(item) {
    var classList = ["h1", "h2", "h3", "p"];
    classList.forEach((element) => {

        item.closest(".box").querySelector('.content').classList.remove(element);
    })
    // item.closest(".box").querySelector('.content').classList.remove("h2");
    // item.closest(".box").querySelector('.content').classList.remove("h3");
    // item.closest(".box").querySelector('.content').classList.remove("p");
}

function setHeader1(item) {
    removeOther(item);
    item.closest(".box").querySelector('.content').classList.add("h1");
    item.closest(".box").querySelector('.content').setAttribute("type", "h1");
}

function setHeader2(item) {
    removeOther(item);
    item.closest(".box").querySelector('.content').classList.add("h2");
    item.closest(".box").querySelector('.content').setAttribute("type", "h2");
}

function setHeader3(item) {
    removeOther(item);
    item.closest(".box").querySelector('.content').classList.add("h3");
    item.closest(".box").querySelector('.content').setAttribute("type", "h3");
}

function setParagraf(item) {
    removeOther(item);
    item.closest(".box").querySelector('.content').classList.add("p");
    item.closest(".box").querySelector('.content').setAttribute("type", "p");
}

// JSON 

function download() {
    // var boxList = document.querySelector("#boxs").querySelectorAll(".box");
    // var result = [];
    // boxList.forEach((item) => {
    //     let contentBox = item.querySelector('.content');
    //     let content = contentBox.innerText;
    //     let type = contentBox.getAttribute("type");
    //     // console.log(className);
    //     result.push({
    //         "type": type,
    //         "content": content
    //     })
    // })

    // console.log(JSON.stringify(result));
}