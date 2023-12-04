class TaskElementy extends HTMLElement {
    static observedAttributes = [
        "name",
        "status",
        "id"
    ];

    constructor() {
        super();
    }

    connectedCallback() {
        // console.log("Custom element added to page.");
        this.render();
    }

    disconnectedCallback() {
        // console.log("Custom element removed from page.");
    }

    adoptedCallback() {
        // console.log("Custom element moved to new page.");
    }

    attributeChangedCallback(name, oldValue, newValue) {
        // console.log(`Attribute ${name} has changed.`);
        this.render();
    }

    render() {

        const name = this.getAttribute("name") || "";
        const status = this.getAttribute("status") || "false";
        const id = this.getAttribute("id") || "0";

        var checked = `<div class='checkbox'></div>`
        if (status && (status == "true" || status == "2")) {
            var checked = `<div class='checkbox checked'></div>`
        }

        this.innerHTML = `
            <div data-id='${id}'>
                <div>
                    ${checked}
                    ${name}
                </div>
                
                <div class='delete'>
                    <img src="/modules/task/assets/img/trash.svg" />
                </div>
            </div>
        `;
    }
}

customElements.define("task-element", TaskElementy);

// Task box

class TaskBox extends HTMLElement {
    static observedAttributes = [
        "name",
        "status",
        "id"
    ];

    constructor() {
        super();
    }

    connectedCallback() {
        // console.log("Custom element added to page.");

        this.render();
    }

    disconnectedCallback() {
        // console.log("Custom element removed from page.");
    }

    adoptedCallback() {
        // console.log("Custom element moved to new page.");
    }

    attributeChangedCallback(name, oldValue, newValue) {
        // console.log(`Attribute ${name} has changed.`);
        this.render();
    }

    render() {

        const name = this.getAttribute("name") || "";
        // console.log(this.childNodes);
        this.innerHTML = `
                <div class="box_content">
                    <div class="title">${name}</div>
                    <div class="params"></div>
                    <div class="description">

                    </div>
                </div>
        `;
    }
}

customElements.define("task-box", TaskBox);


// kanban view box

class KanbanView extends HTMLElement {
    static observedAttributes = [
        "name"
    ];

    constructor() {
        super();
    }

    connectedCallback() {
        // console.log("Custom element added to page.");

        this.render();
    }

    disconnectedCallback() {
        // console.log("Custom element removed from page.");
    }

    adoptedCallback() {
        // console.log("Custom element moved to new page.");
    }

    attributeChangedCallback(name, oldValue, newValue) {
        // console.log(`Attribute ${name} has changed.`);
        this.render();
    }

    render() {

        const name = this.getAttribute("name") || "";

        this.innerHTML = `
            <div class="kanban_view">

                <div class="title">
                    <input type="text" value="${name}" class="tags_name" />
                    <div class='tags_delete icon'>
                        <img src="/modules/task/assets/img/trash.svg" />
                    </div>
                    <div class='add_task icon'>
                        <img src="/modules/task/assets/img/add.svg" />
                    </div>
                </div>
                
                <div class="kanban_view_content">
                    
                </div>

            </div>
        `;
    }
}

customElements.define("kanban-view", KanbanView);

// add kanban view box

class AddNewKanbanTagButton extends HTMLElement {
    static observedAttributes = [
        "name"
    ];

    constructor() {
        super();
    }

    connectedCallback() {
        // console.log("Custom element added to page.");

        this.render();
    }

    disconnectedCallback() {
        // console.log("Custom element removed from page.");
    }

    adoptedCallback() {
        // console.log("Custom element moved to new page.");
    }

    attributeChangedCallback(name, oldValue, newValue) {
        // console.log(`Attribute ${name} has changed.`);
        this.render();
    }

    render() {

        const name = this.getAttribute("name") || "";

        this.innerHTML = `
            <div class="add_kanban">

                    ${name}

            </div>
        `;
    }
}

customElements.define("new-kanban-tag", AddNewKanbanTagButton);