class TasksRepository {

    static task_group_id;
    static project_id;

    constructor(task_group_id, project_id) {
        TasksRepository.task_group_id = task_group_id;
        TasksRepository.project_id = project_id;
    }

    async getAllTask() {
        let response = await api.get("api/get_task", {
            "project_id": TasksRepository.project_id,
            "task_group_id": TasksRepository.task_group_id
        });

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;
    }

    async getTaskByDetailsId(id) {

        let response = await api.get("api/get_task_details", {
            "project_id": TasksRepository.project_id,
            "task_group_id": TasksRepository.task_group_id,
            "task_id": id,
        });

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;

    }

    async createNewTask(taskName, task_tag_id = -1, taskDescription = null) {

        var data = {};
        data.task = taskName;
        data.project_id = TasksRepository.project_id;
        data.task_group_id = TasksRepository.task_group_id;
        data.task_tag_id = task_tag_id;

        let response = await api.post("api/add_task", data);

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;
    }

    async updateTask(id, params) {

        let defaultData = {
            "project_id": TasksRepository.project_id,
            "task_group_id": TasksRepository.task_group_id,
            "task_id": id
        };
        let data = Object.assign({}, params, defaultData);
        let response = await api.put("api/update_task", data);

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return response;

    }

    async deleteTask(id) {

        let response = await api.delete("api/delete_task", {
            "project_id": TasksRepository.project_id,
            "task_group_id": TasksRepository.task_group_id,
            "task_id": id
        });

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;

    }

    async getCollaboratorsList() {

        let response = await api.get("api/get_all_project_user", {
            "project_id": TasksRepository.project_id
        });

        var message = [];


        if (response && response.status) {
            message = await response.message;
        }

        return message;

    }

    async getAssignedCollaboratorsList(task_id) {

        let response = await api.get("api/get_assigned_collaborator", {
            "project_id": TasksRepository.project_id,
            "task_group_id": TasksRepository.task_group_id,
            "task_id": task_id
        });

        var message = [];


        if (response && response.status && response.message && response.message.data) {
            message = await response.message.data;
        }

        return message;

    }

    async assignCollaborators(collaborator_id, task_id) {

        let response = await api.put("api/assign_collaborator", {
            "project_id": TasksRepository.project_id,
            "task_group_id": TasksRepository.task_group_id,
            "task_id": parseInt(task_id),
            "collaborator_id": parseInt(collaborator_id)
        });

        var message = [];


        if (response && response.status) {
            message = await response.message;
        }

        return message;

    }

}