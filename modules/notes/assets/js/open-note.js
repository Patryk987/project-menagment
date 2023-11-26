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
            var title = "New notes";
            var data = await this.add_notes(title);
            this.note_id = data.id;
            this.#open(data.id);

            // var grid = document.querySelector(".grid_view");
            // grid.innerHTML += `
            //     <simple-card 
            //         note_id="` + data.id + `"
            //         title="` + title + `"
            //         create_time="0000-00-00"/>
            // `;
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
            document.querySelector("simple-card[data-id='" + id + "'").style.display = "none";
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
                author="Jan kowalski" 
                last_modify="` + update_time + `"
                background="https://cdn.pixabay.com/photo/2023/11/05/21/04/alps-8368328_1280.jpg" />`;

        var text_editor = new TextEditor;
        text_editor.load();

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
