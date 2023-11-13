
class Grid {

    maxDescriptionLength = 50

    constructor(maxDescriptionLength = 50) {
        this.maxDescriptionLength = maxDescriptionLength;
    }

    load(items) {
        this.#renderItems(items);
        this.#implementDragElement();
    }

    // Private

    #renderItems(items) {
        var grid = document.querySelector(".grid_view");

        items.forEach(element => {

            grid.innerHTML += `
                <div 
                    data-id="` + element.note_id + `"
                    class="box" 
                    draggable="true"
                    style="background-image: url('` + element.background + `');">
                    <div class="box_content">
                        <div class="title">` + element.title + `</div>
                        <div class="params"></div>
                        <div class="description">
                            `+ element.create_time + `
                        </div>
                    </div>
                </div>
            `;
            // grid.innerHTML += `
            //     <div 
            //         class="box" 
            //         draggable="true"
            //         style="background-image: url('` + element.background + `');">
            //         <div class="box_content">
            //             <div class="title">` + element.title + `</div>
            //             <div class="params"></div>
            //             <div class="description">
            //                 ` + this.#truncate(element.short_description, this.maxDescriptionLength) + `
            //             </div>
            //         </div>
            //     </div>
            // `;
        })

        grid.innerHTML += `
                <div 
                    class="add_box" id="add_new_note" >

                        <div class="add_icon">+</div>
                        <div class="add_title">Add new note</div>

                </div>
            `;
    }

    #implementDragElement() {
        const draggableGrid = document.querySelectorAll('#grid .box')
        const containersGrid = document.querySelectorAll('#grid .grid_view')

        draggableGrid.forEach(draggable => {
            draggable.addEventListener('dragstart', () => {
                draggable.classList.add('dragging')
            })

            draggable.addEventListener('dragend', () => {
                draggable.classList.remove('dragging')
            })
        })

        containersGrid.forEach(container => {
            container.addEventListener('dragover', e => {
                e.preventDefault()
                const afterElement = this.#getDragAfterElementGrid(container, e.clientY, e.clientX)
                const draggable = document.querySelector('#grid .dragging')

                if (afterElement == null) {
                    // container.appendChild(draggable)
                    container.appendChild(draggable)
                } else {
                    container.insertBefore(draggable, afterElement.element)
                }
            })
        })

    }

    #getDragAfterElementGrid(container, y, x) {
        const draggableElements = [...container.querySelectorAll('#grid .box')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect()
            if (x < box.right && x > box.left && y < box.bottom && y > box.top) {
                return { offset: box.left, element: child }
            } else {
                return closest
            }
        }, { offset: Number.NEGATIVE_INFINITY })

    }

    #truncate(str, length) {
        if (str.length <= length) {
            return str;
        } else {

            return str.slice(0, length) + "...";
        }
    }
}
