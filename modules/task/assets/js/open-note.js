class Details {

    single_note = document.querySelector("#single_note");

    title;
    data;

    active() {
        this.#insertContent();
        this.#close();
    }

    async open(id, data) {

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
        var background = data.background;
        if (background) background = "url(/" + background + ")";
        this.single_note.querySelector(".content").innerHTML = `
            <note-content 
                title="` + title + `" 
                author="`+ author + `" 
                last_modify="` + update_time + `"
                background="` + background + `" />`;

        this.#updateTitle();
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

    #addImage() {

    }

    #insertContent() {

    }

    #close() {

        this.single_note.querySelector("#single_note .close").addEventListener("click", () => {
            this.single_note.classList.remove("show_single_note");
        });

    }
}