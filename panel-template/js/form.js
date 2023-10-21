
var add = document.querySelectorAll('.add_field');
var remove = document.querySelectorAll('.remove_field');

function duplicateObjectGroup() {

    var nodes = document.querySelectorAll('.object_group');
    var objectGroup = nodes[nodes.length - 1];
    var clonedGroup = objectGroup.cloneNode(true);
    var inputs = clonedGroup.querySelectorAll('input');
    var selects = clonedGroup.querySelectorAll('select');


    inputs.forEach(function (input) {
        input.value = '';

        var regex = /\[(\d+)\]/;
        var match = regex.exec(input.name);
        // console.log(match[1]);

        let number = parseInt(match[1]) + 1;

        input.name = input.name.replace(regex, "[" + number + "]");
    });

    selects.forEach(function (select) {
        select.value = '';

        var regex = /\[(\d+)\]/;
        var match = regex.exec(select.name);
        // console.log(match[1]);

        let number = parseInt(match[1]) + 1;

        select.name = select.name.replace(regex, "[" + number + "]");
    });

    var multiList = document.querySelector('.multi-list');
    multiList.insertBefore(clonedGroup, multiList.lastElementChild);

    var removeButtons = clonedGroup.querySelectorAll('.remove_field');
    removeButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var parentElement = this.parentNode.parentNode;
            parentElement.parentNode.removeChild(parentElement);
        });
    });

}

var addButton = document.querySelector('.add_field');

if (addButton) {
    addButton.addEventListener('click', duplicateObjectGroup);
}

