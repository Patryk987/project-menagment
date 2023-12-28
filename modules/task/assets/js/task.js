class Task extends TasksRepository {

    taskBlock = document.querySelector("#task");
    details;

    async loadTask() {
        this.taskBlock.innerHTML = "";
        var tasks = await this.getAllTask();

        for (let task of tasks) {
            await this.#addNewBlock(task.task_id, task.task_name, task.task_status_id);
        }

        this.#activeBlocks();
        this.#opneDetails();
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
                this.#opneDetails();
            }
        })
    }

    active() {

        this.details = new Details;
        this.details.active();

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
        checkboxTask.addEventListener("click", async (event) => {
            event.stopPropagation();
            let taskElement = await checkboxTask.closest("task-element");

            let taskId = await taskElement.getAttribute("id");
            let actualStatus = await taskElement.getAttribute("status");
            let newStatus = actualStatus == 2 ? 1 : 2;
            let response = await this.updateTask(taskId, { "task_status": newStatus });
            taskElement.setAttribute("status", newStatus);
            this.#activeBlocks();

        }, false);

    }

    #addNewBlock(id, name, status_id) {
        var newElement = document.createElement("task-element");
        newElement.setAttribute("id", id);
        newElement.setAttribute("name", name);
        newElement.setAttribute("status", status_id);
        this.taskBlock.appendChild(newElement);
    }

    #opneDetails() {
        let taskElements = document.querySelectorAll("#task task-element");
        taskElements.forEach(element => {
            element.addEventListener("click", async () => {
                let id = element.getAttribute("id");
                this.details.open();

                var details = await this.getTaskByDetailsId(id);
                var detailsData = details[0];

                if (detailsData.author.additional_data.name) {
                    var nick = detailsData.author.additional_data.name + " " + detailsData.author.additional_data.surname;
                } else {
                    var nick = detailsData.author.data.nick;
                }

                var data = {
                    "title": detailsData.task_name,
                    "content": detailsData.content,
                    "update_time": detailsData.update_date,
                    "create_time": detailsData.create_date,
                    "background": detailsData.background,
                    "author": nick
                }

                this.details.insertData(data);

                this.details.addChangeTitle((value) => {
                    this.updateTask(id, { "name": value })
                    element.querySelector(".title").innerHTML = value;
                });

                this.details.addChangeData((value) => {
                    this.updateTask(id, { "content": value })
                });

                this.details.deleteNote((value) => {
                    this.deleteTask(id);
                    this.details.close();
                    element.remove();
                });

                this.details.changeNoteBackground((value) => {
                    this.updateTask(id, { "background": value });
                });
            })

        })
    }

}