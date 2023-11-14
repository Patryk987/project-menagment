// Create a class for the element
class NoteDetailsElement extends HTMLElement {
    static observedAttributes = [
        "title",
        "author",
        "last_modify",
        "background"
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

        const title = this.getAttribute("title") || "";
        const author = this.getAttribute("author") || "";
        const last_modify = this.getAttribute("last_modify") || "0000/00/00";
        const background = this.getAttribute("background") || null;

        var photoBox = background != null ? `<div class='background' style="background-image: url('${background}')"></div>` : ``;

        this.innerHTML = `
            <div>
                ${photoBox}
                <div class="title">
                    <h3>
                        <input type='text' value='${title}' id='note_title'/>
                    </h3>
                </div>
                <div class="params">
                    <table>
                        <tr>
                            <th>Author</th>
                            <td>${author}</td>
                        </tr>
                        <tr>
                            <th>Last modify</th>
                            <td>${last_modify}</td>
                        </tr>
                    </table>
                </div>
                <div class="content">

                </div>
            </div>
        `;
    }
}

customElements.define("note-content", NoteDetailsElement);

// Create a class for the element
class SimpleCardElement extends HTMLElement {
    static observedAttributes = [
        "note_id",
        "background",
        "title",
        "create_time"
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

        const note_id = this.getAttribute("data-id") || "";
        const background = this.getAttribute("background") || "";
        const title = this.getAttribute("title") || "";
        const create_time = this.getAttribute("create_time") || "";

        const style = background ? `style="background-image: url('${background}');"` : "";

        this.innerHTML = `
            <div
                data-id="${note_id}"
                class="box" 
                ${style}
                >
                <div class="box_content">
                    <div class="title">${title}</div>
                    <div class="params"></div>
                    <div class="description">
                        ${create_time}
                    </div>
                </div>
            </div>
        `;
    }
}

customElements.define("simple-card", SimpleCardElement);
