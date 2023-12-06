class TasksStatusRepository {

    static task_group_id;
    static project_id;

    constructor(task_group_id, project_id) {
        TasksRepository.task_group_id = task_group_id;
        TasksRepository.project_id = project_id;
    }

    async getAllTaskTags() {
        let response = await api.get("api/get_task_tags", {
            "project_id": TasksRepository.project_id,
            "task_group_id": TasksRepository.task_group_id
        });

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;
    }

    async createNewTaskTags(name, task_tag_id = -1, taskDescription = null) {

        var data = {};
        data.name = name;
        data.project_id = TasksRepository.project_id;
        data.task_group_id = TasksRepository.task_group_id;
        data.task_tag_id = task_tag_id;

        let response = await api.post("api/add_task_tags", data);

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;
    }

    async updateTaskTags(id, params) {

        let defaultData = {
            "project_id": TasksRepository.project_id,
            "task_group_id": TasksRepository.task_group_id,
            "task_tags_id": id
        };
        let data = Object.assign({}, params, defaultData);

        let response = await api.put("api/update_task_tags", data);

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;

    }

    async deleteTaskTags(id) {

        let response = await api.delete("api/delete_task_tags", {
            "project_id": TasksRepository.project_id,
            "task_group_id": TasksRepository.task_group_id,
            "task_tags_id": id
        });

        var message = [];

        if (response && response.status) {
            message = await response.message;
        }

        return message;

    }

}