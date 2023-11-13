// Create a class for the element
class NoteDetailsElement extends HTMLElement {
    static observedAttributes = [
        "title",
        "author",
        "last_modify",
        "background"
    ];

    constructor() {
        super();
    }

    connectedCallback() {
        // console.log("Custom element added to page.");
        this.render();
    }

    disconnectedCallback() {
        // console.log("Custom element removed from page.");
    }

    adoptedCallback() {
        // console.log("Custom element moved to new page.");
    }

    attributeChangedCallback(name, oldValue, newValue) {
        // console.log(`Attribute ${name} has changed.`);
        this.render();
    }

    render() {

        const title = this.getAttribute("title") || "";
        const author = this.getAttribute("author") || "";
        const last_modify = this.getAttribute("last_modify") || "0000/00/00";
        const background = this.getAttribute("background") || null;

        var photoBox = background != null ? `<div class='background' style="background-image: url('${background}')"></div>` : ``;

        this.innerHTML = `
            <div>
                ${photoBox}
                <div class="title">
                    <h3>
                        <input type='text' value='${title}' id='note_title'/>
                    </h3>
                </div>
                <div class="params">
                    <table>
                        <tr>
                            <th>Author</th>
                            <td>${author}</td>
                        </tr>
                        <tr>
                            <th>Last modify</th>
                            <td>${last_modify}</td>
                        </tr>
                    </table>
                </div>
                <div class="content">

                </div>
            </div>
        `;
    }
}

customElements.define("note-content", NoteDetailsElement);


class Note {

    single_note = document.querySelector("#single_note");
    static notepad_id;
    static project_id;
    note_id = 0;
    constructor(notepad_id, project_id) {
        Note.notepad_id = notepad_id;
        Note.project_id = project_id;
    }

    async get_notes() {
        let response = await api.get("api/get_notes", {
            "notepad_id": Note.notepad_id
        });

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;
    }

    async add_notes(title, note = "") {
        let response = await api.post("api/add_note", {
            "notepad_id": Note.notepad_id,
            "title": title,
            "note": "asdf",
            "background": "",
            "project_id": Note.project_id
        });

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;
    }

    async update_notes(id, title, note = "") {
        let response = await api.put("api/update_note", {
            "note_id": id,
            "notepad_id": Note.notepad_id,
            "project_id": Note.project_id,
            "title": title,
        });

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;
    }

    async delete_note() {
        let response = await api.delete("api/delete_note", {
            "note_id": this.note_id
        });

        return response;
    }

    active(data = null) {
        this.#button_add_note();
        const box = document.querySelectorAll(".box");

        box.forEach(item => {
            item.addEventListener("click", () => {
                this.note_id = item.dataset.id;
                this.#open(item.dataset.id);
            })
        })
        this.#close();
    }

    async #button_add_note() {
        var newNote = document.querySelector("#add_new_note");

        newNote.addEventListener("click", async () => {
            var data = await this.add_notes("New notes");
            this.note_id = data.id;
            this.#open(data.id);
        })
    }

    #activeUpdateTitle(id) {

        const noteTitle = document.querySelector("#note_title");

        noteTitle.addEventListener("change", () => {
            this.update_notes(id, noteTitle.value)
            document.querySelector(".box[data-id='" + id + "'").querySelector(".title").innerHTML = noteTitle.value;
        })

        const deleteButton = document.querySelector("#single_note .delete");

        deleteButton.addEventListener("click", async () => {
            var data = await this.delete_note(id);
            console.log(data);
            document.querySelector(".box[data-id='" + id + "'").style.display = "none";
            this.single_note.classList.remove("show_single_note");
        })

    }

    #load_content(data) {
        this.single_note.innerHTML = `
            
        `;
    }

    async #open(id) {


        this.single_note.querySelector(".content").innerHTML = `<div class="loader"></div>`;
        this.single_note.classList.add("show_single_note");

        var data = await this.#download_data(id);
        console.log(data);
        document.querySelector('.modify_data').innerHTML = data.create_time;

        var title = data.title;
        var update_time = data.update_time;
        var background = data.background;
        this.single_note.querySelector(".content").innerHTML = `
            <note-content 
                title="` + title + `" 
                author="" 
                last_modify="` + update_time + `"
                background="` + background + `" />`;

        this.#activeUpdateTitle(id);

    }

    async #download_data(id) {

        let response = await api.get("api/get_note", {
            "note_id": id
        });

        var message = [];

        if (response && response.status) {
            message = await response.message[0];
        }

        return message;

    }

    #close() {
        this.single_note.querySelector(".close").addEventListener("click", () => {
            this.single_note.classList.remove("show_single_note");
        });
    }
}
