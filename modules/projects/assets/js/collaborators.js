class AddCollaborators {

    static project_id;

    constructor(project_id) {
        AddCollaborators.project_id = project_id;
    }


    async #addUsers(collaborators_id) {

        let response = await api.post('api/add_collaborators', {
            'collaborators_id': collaborators_id,
            'project_id': AddCollaborators.project_id
        });

        return response;

    }

    active() {
        let invite = document.querySelectorAll('.invite');
        invite.forEach((element) => {
            element.addEventListener('click', async () => {

                let id = element.getAttribute('data-id');
                let response = await this.#addUsers(id);
                element.innerHTML = "invited"

            });

        })
    }

}


class SearchCollaborators extends AddCollaborators {

    constructor(project_id) {
        super(project_id);
    }

    async #downloadUsers(nick) {

        let response = await api.get('api/find_user', {
            'search_nick': nick
        });

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;

    }

    async #addRow(element, find_user_results) {

        var row = find_user_results.insertRow(-1);

        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);

        cell1.innerHTML = element.nick;
        cell2.innerHTML = element.email;
        cell3.innerHTML = `<div class='invite' data-id=${element.user_id}>Send invite</div>`;
    }

    #activeFinder() {
        document.querySelector('.find_user').addEventListener('keyup', async () => {
            var nick = await document.querySelector('.find_user').value;
            var results = await this.#downloadUsers(nick);
            var find_user_results = await document.querySelector('.find_user_results table tbody');

            // Clear the row
            find_user_results.innerHTML = '';

            // Add new rows
            results.forEach(element => this.#addRow(element, find_user_results));

            this.active();

        });
    }

    init() {
        this.#activeFinder();
    }
}


class Collaborators {

    static project_id;

    constructor(project_id) {
        Collaborators.project_id = project_id;
    }

    activeFindCollaborators() {
        let searchCollaborators = new SearchCollaborators(Collaborators.project_id);
        searchCollaborators.init();
    }

}