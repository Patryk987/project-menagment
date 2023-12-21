class IssuesRepository {

    static project_id;

    constructor(project_id) {
        IssuesRepository.project_id = project_id;
    }

    async getAllIssues() {
        let response = await api.get("api/get_issues", {
            "project_id": IssuesRepository.project_id,
        });

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;
    }

    async getIssueDetailsById(id) {

        let response = await api.get("api/get_issues_by_id", {
            "project_id": IssuesRepository.project_id,
            "issues_id": id,
        });

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;

    }

    async createNewIssues(title, description = null) {

        var data = {};
        data.project_id = IssuesRepository.project_id;
        data.title = title;
        data.description = description;

        let response = await api.post("api/add_issues", data);

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;
    }

    async updateIssues(id, params) {

        let defaultData = {
            "project_id": IssuesRepository.project_id,
            "issues_id": id
        };
        let data = Object.assign({}, params, defaultData);
        let response = await api.put("api/update_issues", data);

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return response;

    }

    async getOptions() {

        let data = {
            "project_id": IssuesRepository.project_id
        };

        let response = await api.get("api/get_options", data);

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return response;

    }

    // async deleteIssues(id) {

    //     let response = await api.delete("api/delete_Issues", {
    //         "project_id": IssuesRepository.project_id,
    //         "issues_id": id
    //     });

    //     var message = [];

    //     if (response && response.status) {
    //         message = await response.message;
    //     }

    //     return message;

    // }
}