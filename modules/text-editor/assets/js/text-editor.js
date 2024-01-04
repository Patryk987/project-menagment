
class TextEditor {

    content = {};
    dataToMove;

    // Moving element

    #moveUp() {
        var sel = window.getSelection();
        var range = sel.getRangeAt(0);
        var currentElement = range.commonAncestorContainer.parentElement.closest(".text_box");

        if (currentElement.previousElementSibling) {
            var prevSibling = currentElement.previousElementSibling.querySelector('.text_content');
            prevSibling.focus();
        }
    }

    #moveDown() {
        var sel = window.getSelection();
        var range = sel.getRangeAt(0);
        var currentElement = range.commonAncestorContainer.parentElement.closest(".text_box");
        if (currentElement.nextElementSibling) {
            var prevSibling = currentElement.nextElementSibling.querySelector('.text_content');
            prevSibling.focus();
            return true;
        } else {
            return false
        }
    }

    #move(item) {
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
            item.closest(".text_box").insertAdjacentElement('afterend', this.dataToMove);
            this.#updateContent();
        });
    }

    // change element type

    #removeOther(item) {
        var classList = ["h1", "h2", "h3", "p"];
        classList.forEach((element) => {

            item.closest(".text_box").querySelector('.text_content').classList.remove(element);
            this.#updateContent();

        })
    }

    #setHeader1(item) {
        this.#removeOther(item);
        item.closest(".text_box").querySelector('.text_content').classList.add("h1");
        item.closest(".text_box").querySelector('.text_content').setAttribute("type", "h1");
    }

    #setHeader2(item) {
        this.#removeOther(item);
        item.closest(".text_box").querySelector('.text_content').classList.add("h2");
        item.closest(".text_box").querySelector('.text_content').setAttribute("type", "h2");
        item.closest(".text_box").querySelector('.text_content').setAttribute("contenteditable", "true");
    }

    #setHeader3(item) {
        this.#removeOther(item);
        item.closest(".text_box").querySelector('.text_content').classList.add("h3");
        item.closest(".text_box").querySelector('.text_content').setAttribute("type", "h3");
        item.closest(".text_box").querySelector('.text_content').setAttribute("contenteditable", "true");
    }

    #setParagraf(item) {
        this.#removeOther(item);
        item.closest(".text_box").querySelector('.text_content').classList.add("p");
        item.closest(".text_box").querySelector('.text_content').setAttribute("type", "p");
        item.closest(".text_box").querySelector('.text_content').setAttribute("contenteditable", "true");
    }

    #setImage(item) {
        this.#removeOther(item);
        item.closest(".text_box").querySelector('.text_content').classList.add("img");
        item.closest(".text_box").querySelector('.text_content').setAttribute("type", "img");
        item.closest(".text_box").querySelector('.text_content').setAttribute("contenteditable", "false");
        item.closest(".text_box").querySelector('.text_content').innerHTML = "<img src='https://images.unsplash.com/photo-1682687982185-531d09ec56fc?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D' alt='text-editor-image'/>";
    }

    #typeChange(element) {
        var setHeader = element.querySelectorAll(".select-box.h1");
        setHeader.forEach(item => item.addEventListener("click", (event) => {
            this.#setHeader1(item)
            this.#updateContent();
        }));

        var setHeader = element.querySelectorAll(".select-box.h2");
        setHeader.forEach(item => item.addEventListener("click", (event) => {
            this.#setHeader2(item)
            this.#updateContent();
        }));

        var setHeader = element.querySelectorAll(".select-box.h3");
        setHeader.forEach(item => item.addEventListener("click", (event) => {
            this.#setHeader3(item)
            this.#updateContent();
        }));

        var setHeader = element.querySelectorAll(".select-box.p");
        setHeader.forEach(item => item.addEventListener("click", (event) => {
            this.#setParagraf(item)
            this.#updateContent();
        }));

        var setHeader = element.querySelectorAll(".select-box.img");
        setHeader.forEach(item => item.addEventListener("click", (event) => {
            this.#setImage(item)
            this.#updateContent();
        }));
    }


    // Active new

    #handleKeyPress(event, item) {

        if (event.keyCode === 13) {
            event.preventDefault();
            this.#addNewElement(event);
        }

        if (event.keyCode === 38) { // cursor up
            event.preventDefault();
            this.#moveUp();
        }

        if (event.keyCode === 40) { // cursor down
            event.preventDefault();
            if (!this.#moveDown()) {
                this.#addNewElement(event);
            }
        }

        if (event.keyCode === 46) { // delete
            event.preventDefault();
            this.#moveUp();
            this.#deleteElement(item);
        }

    }

    #deleteAction(element) {
        var container = element.querySelectorAll(".delete");
        container.forEach(item => {
            item.addEventListener("click", (event) => this.#deleteElement(item));
            this.#updateContent();
        });
    }

    #addAction(element) {
        var addButton = element.querySelectorAll(".add");
        addButton.forEach(item => {
            item.addEventListener('click', (event) => this.#addNewElement(event));
        });
    }

    #contentActive(element) {
        var content = element.querySelectorAll(".text_content");
        content.forEach(item => {
            item.addEventListener('keydown', (event) => {
                this.#handleKeyPress(event, item)
            });
        });
    }

    #optionActive(element) {
        var content = element.querySelectorAll(".option");
        content.forEach(item => {
            item.addEventListener('click', () => {
                let isShow = item.closest(".text_box").querySelector('.select').classList.contains("show");

                let boxList = item.closest("#text-editor").querySelectorAll(".text_box");
                boxList.forEach(boxListItem => {
                    boxListItem.querySelector('.select').classList.remove("show");
                })

                if (!isShow) {
                    item.closest(".text_box").querySelector('.select').classList.toggle("show");
                }
            });
        });
    }

    #moveElement(element) {
        var draggable = element.querySelectorAll(".move");
        draggable.forEach(item => {
            item.addEventListener('dragstart', (event) => {
                this.dataToMove = item.closest(".text_box");
            });
        });

        var dropZone = element.querySelectorAll(".drop");
        dropZone.forEach(item => {
            this.#move(item);
        });
    }

    addChangeListener(callback) {
        this.content = callback;
    }

    #changeListener(element) {
        const textContent = element.querySelector(".text_content");

        textContent.addEventListener("focusout", () => {
            this.#updateContent();
        })
    }

    #updateContent() {
        let note = this.download()
        console.log(note);
        this.content(note);
    }

    #activeNewElement(element) {
        this.#deleteAction(element);
        this.#addAction(element);
        this.#typeChange(element);
        this.#contentActive(element);
        this.#optionActive(element);
        this.#moveElement(element);
        this.#changeListener(element);
    }

    // Delete

    #deleteElement(item) {
        var box = document.querySelectorAll('#text-editor .text_box');
        if (box.length > 1)
            item.closest(".text_box").remove()
    }

    // Core

    async #getContent(link) {
        return fetch(link)
            .then(response => response.text())
            .then(html => {
                return html;
            })
            .catch(error => {
                console.error('Wystąpił błąd podczas pobierania pliku HTML:', error);
            });
    }


    async #getBlock() {

        var box = await this.#getContent('http://192.168.0.117:8000/modules/text-editor/template/box.html');
        var text = await this.#getContent('http://192.168.0.117:8000/modules/text-editor/template/text.html');
        var options = await this.#getContent('http://192.168.0.117:8000/modules/text-editor/template/options.html');

        var newElement = document.createElement("div");
        newElement.classList.add("text_box");
        newElement.innerHTML = box;
        newElement.querySelector('.data').innerHTML = text;
        newElement.querySelector('.select').innerHTML = options;

        return newElement

    }

    async #addNewElement(event) {

        let newElement = await this.#getBlock();

        event.target.closest(".text_box").after(newElement);
        newElement.querySelector('#text-editor .text_content').focus();
        this.#activeNewElement(newElement)

    }

    async #addNewElementType(type, value) {

        let newElement = await this.#getBlock();

        var existingElement = document.querySelector("#text-editor");
        existingElement.appendChild(newElement);
        newElement.querySelector('.text_content').innerHTML = value;
        newElement.querySelector('#text-editor .text_content');
        this.#activeNewElement(newElement);

        switch (type) {
            case "text":
                this.#setParagraf(newElement);
                break;
            case "h1":
                this.#setHeader1(newElement);
                break;
            case "h2":
                this.#setHeader2(newElement);
                break;
            case "h3":
                this.#setHeader3(newElement);
                break;
            case "img":
                this.#setImage(newElement);
                break;
            default:
                this.#setParagraf(newElement);
                break;
        }

    }

    async #addNewElementOnEnterClick() {

        let newElement = await this.#getBlock();

        var existingElement = document.querySelector("#text-editor");
        existingElement.appendChild(newElement);
        newElement.querySelector('#text-editor .text_content').focus();
        this.#activeNewElement(newElement);

    }

    load() {
        // this.#addNewElementOnEnterClick();
    }

    // JSON 

    download() {

        var boxList = document.querySelector("#text-editor").querySelectorAll(".text_box");
        var result = [];
        boxList.forEach((item, key) => {
            let contentBox = item.querySelector('.text_content');
            let content = contentBox.innerText;
            let type = contentBox.getAttribute("type");
            result.push({
                "key": key,
                "type": type,
                "content": content
            })
        })
        return JSON.stringify(result);
    }

    async loadContent(content = '[{"type":"text","content":""}]') {
        try {
            var json = JSON.parse(content);
            if (json && json.length > 0) {
                const sortedJson = json.sort((a, b) => a.key - b.key);

                for (const element of sortedJson) {
                    await this.#addNewElementType(element.type, element.content);
                }

            } else {
                this.#addNewElementOnEnterClick();
            }
        } catch (error) {
            this.#addNewElementOnEnterClick();
        }

    }

}
