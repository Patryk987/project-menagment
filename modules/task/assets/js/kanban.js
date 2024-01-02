class Kanban {
    draggableKanban = document.querySelectorAll('#kanban .box')
    containersKanban = document.querySelectorAll('#kanban .kanban_view')

    #getDragAfterElementKanban(container, y) {
        const draggableElements = [...container.querySelectorAll('#kanban .box:not(.dragging)')]

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect()
            const offset = y - box.top - box.height / 2
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child }
            } else {
                return closest
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element
    }

    load() {
        this.draggableKanban.forEach(draggable => {
            draggable.addEventListener('dragstart', () => {
                draggable.classList.add('dragging')
            })

            draggable.addEventListener('dragend', () => {
                draggable.classList.remove('dragging')
            })
        })

        this.containersKanban.forEach(container => {
            container.addEventListener('dragover', e => {
                e.preventDefault()
                const afterElement = this.#getDragAfterElementKanban(container, e.clientY)
                const draggable = document.querySelector('#kanban .dragging')
                if (afterElement == null) {
                    container.appendChild(draggable)
                } else {
                    container.insertBefore(draggable, afterElement)
                }
            })
        })
    }
}