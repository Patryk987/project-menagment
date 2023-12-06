class RepositoryNote {

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

    async download_data(id) {

        let response = await api.get("api/get_note", {
            "note_id": id
        });

        var message = [];

        if (response && response.status) {
            message = await response.message[0];
        }

        return message;

    }
}