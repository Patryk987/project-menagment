class IssuesDetailsElement extends HTMLElement {
    static observedAttributes = [
        "title",
        "author",
        "last_modify",
        "content",
        "status",
        "options"
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
        const content = this.getAttribute("content") || "";
        const status = this.getAttribute("status") || "";

        var select_options = "<select id='status_change'>";
        Issues.options.forEach(element => {
            let checked = status == element.key ? "selected" : "";
            select_options += `<option value='${element.key}' ${checked}>${element.value}</option>`;
        });
        select_options += "</select>";

        this.innerHTML = `
            <div>
                <div class="title">
                    <h3>
                        ${title}
                        <!--- <input type='text' value='${title}' id='note_title'/> -->
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
                        <tr>
                            <th>Status</th>
                            <td>
                                ${select_options}
                            </td>
                        </tr>
                    </table>
                </div>
                <p class='content'>
                    ${content}
                </p>
            </div>
        `;
    }
}

customElements.define("issues-content", IssuesDetailsElement);