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

    async add_notes(title, note = null) {
        let response = await api.post("api/add_note", {
            "notepad_id": Note.notepad_id,
            "title": title,
            "note": note,
            "background": "",
            "project_id": Note.project_id
        });

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;
    }

    async update_notes(id, title = "", note = "", background = "") {
        let response = await api.put("api/update_note", {
            "note_id": id,
            "notepad_id": Note.notepad_id,
            "project_id": Note.project_id,
            "title": title,
            "note": note,
            "background": background
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
            document.querySelector("simple-card[data-id='" + id + "'").style.display = "none";
            this.single_note.classList.remove("show_single_note");
        })

    }

    #addImage(id) {
        var background = document.querySelector('.background');

        background.addEventListener('click', (item) => {

            var fileInput = document.getElementById('fileInput');
            fileInput.click();

            fileInput.addEventListener('change', () => {

                var selectedFile = fileInput.files[0];

                if (selectedFile && selectedFile.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.addEventListener(
                        "load",
                        async () => {
                            background.style.backgroundImage = 'url("' + reader.result + '")';
                            await this.update_notes(id, "", "", reader.result);
                        },
                        false,
                    );

                    reader.readAsDataURL(selectedFile);
                } else {
                    alert('Wybierz plik w formacie obrazka (np. JPG, PNG, GIF)');
                }
            });

        });
    }

    async #open(id) {


        this.single_note.querySelector(".content").innerHTML = `<div class="loader"></div>`;
        this.single_note.classList.add("show_single_note");

        var data = await this.#download_data(id);
        document.querySelector('.modify_data').innerHTML = data.create_time;

        var title = data.title;
        var note = data.note;
        var update_time = data.update_time;
        var background = data.background;
        if (background) background = "url(/" + background + ")";
        this.single_note.querySelector(".content").innerHTML = `
            <note-content 
                title="` + title + `" 
                author="Jan kowalski" 
                last_modify="` + update_time + `"
                background="` + background + `" />`;

        // Active text editor
        var text_editor = new TextEditor;
        text_editor.load();
        text_editor.loadContent(note);
        // this.#updateNote(text_editor, id);

        text_editor.addChangeListener((newValue) => {
            this.update_notes(id, "", newValue);
        });
        // add image 

        this.#addImage(id);

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