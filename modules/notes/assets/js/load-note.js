
class Grid {

    maxDescriptionLength = 50

    constructor(maxDescriptionLength = 50) {
        this.maxDescriptionLength = maxDescriptionLength;
    }

    load(items) {
        this.#renderItems(items);
        this.#add_note_add_button();
    }

    // Private

    #renderItems(items) {

        var grid = document.querySelector(".grid_view");

        items.forEach(element => {

            let simpleCard = document.createElement('simple-card');

            simpleCard.setAttribute('data-id', element.note_id);
            simpleCard.setAttribute('background', element.background);
            simpleCard.setAttribute('title', element.title);
            simpleCard.setAttribute('create_time', element.create_time);

            grid.appendChild(simpleCard);

        })

    }

    #add_note_add_button() {
        var grid = document.querySelector(".grid_view");
        let addBox = document.createElement('div');
        addBox.classList.add('add_box');
        addBox.id = 'add_new_note';

        let addIcon = document.createElement('div');
        addIcon.classList.add('add_icon');
        addIcon.textContent = '+';

        let addTitle = document.createElement('div');
        addTitle.classList.add('add_title');
        addTitle.textContent = 'Add new note';

        addBox.appendChild(addIcon);
        addBox.appendChild(addTitle);

        grid.appendChild(addBox);
    }



}
