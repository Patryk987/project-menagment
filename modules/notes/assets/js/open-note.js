class Note extends RepositoryNote {

    single_note = document.querySelector("#single_note");
    static notepad_id;
    static project_id;
    note_id = 0;

    active() {
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

        await this.#loadNoteData(id);

        await this.#addImage(id);
        await this.#activeUpdateTitle(id);

    }

    #close() {
        this.single_note.querySelector(".close").addEventListener("click", () => {
            this.single_note.classList.remove("show_single_note");
        });
    }

    async #loadNoteData(id) {

        this.single_note.querySelector(".content").innerHTML = `<div class="loader"></div>`;
        this.single_note.classList.add("show_single_note");

        var data = await this.download_data(id);
        document.querySelector('.modify_data').innerHTML = data.create_time;

        var title = data.title;
        var note = data.note;
        var update_time = data.update_time;
        var background = data.background;

        await this.#addNoteBlock(title, update_time, background)
        await this.#loadTextEditor(id, note);
    }

    async #loadTextEditor(id, note) {

        var text_editor = new TextEditor;
        text_editor.load();
        text_editor.loadContent(note);

        text_editor.addChangeListener((newValue) => {
            this.update_notes(id, "", newValue);
        });

    }

    async #addNoteBlock(title, update_time, background) {

        if (background) background = "url(/" + background + ")";

        let noteContent = document.createElement('note-content');

        noteContent.setAttribute('title', title);
        noteContent.setAttribute('author', 'Jan kowalski');
        noteContent.setAttribute('last_modify', update_time);
        noteContent.setAttribute('background', background);
        let contentElement = this.single_note.querySelector('.content');
        contentElement.innerHTML = '';
        contentElement.appendChild(noteContent);

    }
}