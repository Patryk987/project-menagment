class Details {

    single_note = document.querySelector("#single_note");

    title;
    data;
    deleteActiveNote;
    addNewBackgroundFile;
    deadline;

    active() {
        this.#insertContent();
        this.single_note.querySelector("#single_note .close").addEventListener("click", () => {
            this.close();
        });
    }

    async open() {

        this.single_note.querySelector(".content").innerHTML = `<div class="loader"></div>`;
        this.single_note.classList.add("show_single_note");


    }

    /**
     * 
     * @param {title, note, update_time, background} data 
     */
    insertData(data) {

        document.querySelector('.modify_data').innerHTML = data.create_time;

        var title = data.title;
        var content = data.content;
        var update_time = data.update_time;
        var author = data.author;
        var deadline = data.deadline;
        var background = data.background;
        if (background) background = "url(" + background + ")";
        this.single_note.querySelector(".content").innerHTML = `
            <note-content 
                title="` + title + `" 
                author="`+ author + `" 
                last_modify="` + update_time + `"
                background="` + background + `"
                deadline="`+ deadline + `" />`;

        this.#updateTitle();
        this.#deleteNote();
        this.#noteBackground();
        this.#updateDeadline();
        this.#activeTextEditor(content);

    }

    insertComments() {

    }

    // Private
    addChangeTitle(callback) {
        this.title = callback;
    }

    #updateTitle() {
        const title = document.querySelector("#note_title");

        title.addEventListener("change", () => {
            this.title(title.value);
        })
    }

    changeDeadline(callback) {
        this.deadline = callback;
    }

    #updateDeadline() {
        const deadline = document.querySelector("#deadline");

        deadline.addEventListener("change", () => {
            this.deadline(deadline.value);
        })
    }

    deleteNote(callback) {
        this.deleteActiveNote = callback;
    }

    #deleteNote() {

        const deleteNote = document.querySelector("#single_note .delete");

        deleteNote.addEventListener("click", () => {
            this.deleteActiveNote(true);
        })
    }

    changeNoteBackground(callback) {
        this.addBackgroundFile = callback;
    }

    async #noteBackground() {

        var background = await document.querySelector('#single_note .background');

        await background.addEventListener('click', async (item) => {

            var fileInput = await document.getElementById('fileInput');
            await fileInput.click();

            await fileInput.addEventListener('change', async () => {

                var selectedFile = await fileInput.files[0];

                if (selectedFile && selectedFile.type.startsWith('image/')) {
                    var reader = new FileReader();
                    await reader.addEventListener(
                        "load",
                        async () => {
                            background.style.backgroundImage = 'url("' + reader.result + '")';
                            this.addBackgroundFile(await reader.result);
                        },
                        false,
                    );

                    await reader.readAsDataURL(selectedFile);
                } else {
                    alert('Wybierz plik w formacie obrazka (np. JPG, PNG, GIF)');
                }
            });
        });
    }

    addChangeData(callback) {
        this.data = callback;
    }

    #activeTextEditor(content) {

        // Active text editor
        var text_editor = new TextEditor;
        text_editor.load();
        text_editor.loadContent(content);
        // this.#updateNote(text_editor, id);

        text_editor.addChangeListener((value) => {
            this.data(value);
        });

    }

    #insertContent() {

    }

    close() {


        this.single_note.classList.remove("show_single_note");

    }
}