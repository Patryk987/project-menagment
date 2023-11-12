
class Note {

    single_note = document.querySelector("#single_note");

    constructor() {

    }

    active(data = null) {

        const box = document.querySelectorAll(".box");

        box.forEach(item => {
            item.addEventListener("click", () => {
                this.#open();
            })
        })
        this.#close();
    }

    #load_content(data) {
        this.single_note.innerHTML = `
            
        `;
    }

    #open() {


        // this.single_note.innerHTML = `<div class="loader"></div>`;
        this.single_note.classList.add("show_single_note");

        // setTimeout(() => {
        //     var data = this.#download_data();
        //     this.#load_content(data);
        // }, 1000);


    }

    #download_data() {
        let data = {
            "title": "TEST",
            "description": "TEST",
            "params": "TEST",
            "autor": "TEST",
            "create_date": "TEST",
            "modify_date": "TEST"
        }

        return data;
    }

    #close() {
        this.single_note.querySelector(".close").addEventListener("click", () => {
            this.single_note.classList.remove("show_single_note");
        });
    }
}

var note = new Note;
note.active();