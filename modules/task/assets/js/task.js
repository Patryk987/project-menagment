class Task extends TasksRepository {

    taskBlock = document.querySelector("#task");

    async loadTask() {
        this.taskBlock.innerHTML = "";
        var tasks = await this.getAllTask();

        for (let task of tasks) {
            await this.#addNewBlock(task.task_id, task.task_name, task.task_status_id);
        }

        this.#activeBlocks();
    }

    async addNewTask() {
        let addNewTask = document.querySelector("#addNewTask");
        addNewTask.addEventListener("click", async () => {
            let addNewTaskName = document.querySelector("#newTaskName");
            if (addNewTaskName.value.length > 0) {
                let message = await this.createNewTask(addNewTaskName.value);
                this.#addNewBlock(message.id, addNewTaskName.value, 1);
                addNewTaskName.value = "";
                this.#activeBlocks();
            }
        })
    }

    active() {
        this.loadTask();
        this.addNewTask();
    }


    // Private 

    #activeBlocks() {

        var tasksList = this.taskBlock.querySelectorAll("task-element");
        tasksList.forEach(element => {
            this.#activeDeleteTask(element);
            this.#markAsDone(element);
        });
    }

    async #activeDeleteTask(element) {
        let deleteTask = element.querySelector(".delete");
        deleteTask.addEventListener("click", async () => {
            let taskElement = await deleteTask.closest("task-element");
            let taskId = await taskElement.getAttribute("id");
            await this.deleteTask(taskId);
            taskElement.remove();
        });
    }

    async #markAsDone(element) {
        let checkboxTask = element.querySelector(".checkbox");
        checkboxTask.addEventListener("click", async () => {
            let taskElement = await checkboxTask.closest("task-element");

            let taskId = await taskElement.getAttribute("id");
            let actualStatus = await taskElement.getAttribute("status");
            let newStatus = actualStatus == 2 ? 1 : 2;
            let response = await this.updateTask(taskId, { "task_status": newStatus });
            taskElement.setAttribute("status", newStatus);
            this.#activeBlocks();
        });

    }

    #addNewBlock(id, name, status_id) {
        var newElement = document.createElement("task-element");
        newElement.setAttribute("id", id);
        newElement.setAttribute("name", name);
        newElement.setAttribute("status", status_id);
        this.taskBlock.appendChild(newElement);
    }

}