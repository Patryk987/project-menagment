class loadKanban {

    kanban;
    taskRepository;
    TaskTagsRepository;

    kanbanStatusIdName = 'kanban';

    static task_group_id;
    static project_id;

    constructor(task_group_id, project_id, div = "#kanban") {

        TasksRepository.task_group_id = task_group_id;
        TasksRepository.project_id = project_id;

        this.taskRepository = new TasksRepository(task_group_id, project_id);

        this.TaskTagsRepository = new TasksStatusRepository(task_group_id, project_id);

        this.kanban = document.querySelector(div);

    }

    async #getTaskTags() {
        let TaskTags = await this.TaskTagsRepository.getAllTaskTags();

        return TaskTags;
    }

    async #getTask() {
        let task = await this.taskRepository.getAllTask()
        return task;
    }

    async #addTaskTags() {
        await this.TaskTagsRepository.createNewTaskTags("New tab")
        await this.load();
        window.scrollTo(0, document.querySelector("#kanban").scrollWidth);
    }

    async #loadTaskTags(name, id) {

        // Create a new kanban-view element
        const kanbanView = document.createElement('kanban-view');
        kanbanView.setAttribute('name', name);
        kanbanView.setAttribute('id', `${this.kanbanStatusIdName}-${id}`);
        kanbanView.setAttribute('data-id', id);

        // Append the created kanban-view element as a child of this.kanban
        this.kanban.appendChild(kanbanView);
    }

    async #addTaskTagsButtom() {
        this.kanban.innerHTML += `
            <new-kanban-tag name="+ add new"></new-kanban-tag>
        `;

        let addKanban = document.querySelector(".add_kanban");

        addKanban.addEventListener("click", () => {
            this.#addTaskTags();
        });
    }

    async #updateTaskTag() {
        let tagsNameInput = document.querySelectorAll('.tags_name');
        tagsNameInput.forEach(element => {
            element.addEventListener("blur", () => {
                let id = element.closest("kanban-view").getAttribute('data-id')
                let name = element.value;
                this.TaskTagsRepository.updateTaskTags(id, { "name": name })
            })
        });
    }

    async #addTaskToTagField(name, id, taskId) {

        let kanbanView = document.querySelector(`#${this.kanbanStatusIdName}-${id}`).querySelector(".kanban_view");

        const taskBox = document.createElement('task-box');
        taskBox.setAttribute('name', name);
        taskBox.setAttribute('data-id', taskId);
        taskBox.setAttribute('class', 'box');
        taskBox.setAttribute('draggable', 'true');

        kanbanView.appendChild(taskBox);

    }

    #updateStatus() {

        var draggableKanban = document.querySelectorAll('#kanban .box')

        draggableKanban.forEach(draggable => {
            draggable.addEventListener('dragend', () => {
                let tagsId = draggable.closest("kanban-view").getAttribute('data-id');
                let taskId = draggable.closest('task-box').getAttribute('data-id');
                this.taskRepository.updateTask(taskId, { "task_tag_id": tagsId })
            })
        })

    }

    async #delete() {
        let tagsDeleteButton = document.querySelectorAll('.tags_delete');
        tagsDeleteButton.forEach(element => {
            element.addEventListener("click", () => {
                let id = element.closest("kanban-view").getAttribute('data-id');
                this.TaskTagsRepository.deleteTaskTags(id);
                element.closest("kanban-view").remove();
            })
        });
    }

    async #addNewTask() {

        let tagsDeleteButton = document.querySelectorAll('.add_task');
        tagsDeleteButton.forEach(element => {
            element.addEventListener("click", async () => {
                let tag_id = element.closest("kanban-view").getAttribute('data-id');
                let tag_name = "new task";
                let response = await this.taskRepository.createNewTask(tag_name, tag_id);
                this.#addTaskToTagField(tag_name, tag_id, response.id);
                // this.#active();
            })
        });

    }

    #active() {
        this.#updateStatus();
        this.#updateTaskTag();
        this.#delete();
        this.#addNewTask();

        let note = new Note;
        note.active();
    }

    async load() {

        // Tags
        let TaskTags = await this.#getTaskTags();
        this.kanban.innerHTML = "";
        var tagsIdList = [];
        for (const tagElement of TaskTags) {
            this.#loadTaskTags(tagElement.name, tagElement.task_tags_id);
            tagsIdList.push(tagElement.task_tags_id);
        }
        this.#loadTaskTags("Other", -1);
        this.#addTaskTagsButtom();

        // Task
        let task = await this.#getTask();

        for (const taskElement of task) {
            var tag_id = -1;
            if (tagsIdList.includes(taskElement.task_tag_id) && taskElement.task_tag_id != undefined) {
                tag_id = taskElement.task_tag_id;
            }
            this.#addTaskToTagField(taskElement.task_name, tag_id, taskElement.task_id);
        }

        let x = new Kanban;
        x.load();

        this.#active();

    }
}