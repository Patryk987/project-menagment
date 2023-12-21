
class Issues extends IssuesRepository {
    static options
    details;

    constructor(project_id) {
        super(project_id);
        this.details = new Details();
        this.details.active();
    }

    async active() {
        var options = await this.getOptions();
        Issues.options = options.message.options;

        var issuesList = document.querySelectorAll(".cms_table tr");

        issuesList.forEach(element => {
            element.addEventListener("click", async () => {
                let id = element.getAttribute("data-id");
                this.details.open();
                var data = await this.getIssueDetailsById(id);

                if (data[0].author.additional_data.name) {
                    var nick = data[0].author.additional_data.name + " " + data[0].author.additional_data.surname;
                } else {
                    var nick = data[0].author.data.nick;
                }
                var dataDetails = {
                    "title": data[0].title,
                    "content": data[0].description,
                    "update_time": data[0].update_date,
                    "author": nick,
                    "create_time": data[0].create_date,
                    "status": data[0].status
                }
                this.details.insertData(dataDetails);
                this.#change(id, element);
            })
        })

    }

    #change(id, element) {
        var statusChange = document.querySelector("#status_change");
        statusChange.addEventListener("change", () => {
            let key = statusChange.value;
            let selectedOptions = this.#findKey(key);
            this.updateIssues(id, { "status": key });
            element.querySelector(".value div").innerText = selectedOptions.value;
            element.querySelector(".value div").removeAttribute("class");
            element.querySelector(".value div").classList.add(selectedOptions.class);
        });
    }

    #findKey(key) {
        for (let tmpKey in Issues.options) {
            if (Issues.options[tmpKey].key === parseInt(key)) {
                return Issues.options[tmpKey];
            }
        }
        return null; // Zwracamy null, jeśli nie znaleziono rekordu o danym kluczu
    }
}