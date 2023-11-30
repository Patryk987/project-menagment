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
