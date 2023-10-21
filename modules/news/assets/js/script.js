
function rewrite() {
    let text = document.getElementsByName("title")[0].value;
    document.getElementsByName("title")[1].value = text;

    let text_keywords = document.querySelector("#keywords").innerText;
    document.getElementsByName("keywords")[0].value = text_keywords;
}

function getCaretCharacterOffsetWithin(element) {
    var caretOffset = 0;
    var doc = element.ownerDocument || element.document;
    var win = doc.defaultView || doc.parentWindow;
    var sel;
    if (typeof win.getSelection != "undefined") {
        sel = win.getSelection();
        if (sel.rangeCount > 0) {
            var range = win.getSelection().getRangeAt(0);
            var preCaretRange = range.cloneRange();
            preCaretRange.selectNodeContents(element);
            preCaretRange.setEnd(range.endContainer, range.endOffset);
            caretOffset = preCaretRange.toString().length;
        }
    } else if ((sel = doc.selection) && sel.type != "Control") {
        var textRange = sel.createRange();
        var preCaretTextRange = doc.body.createTextRange();
        preCaretTextRange.moveToElementText(element);
        preCaretTextRange.setEndPoint("EndToEnd", textRange);
        caretOffset = preCaretTextRange.text.length;
    }
    return caretOffset;
}

document.addEventListener("DOMContentLoaded", () => {

    document.querySelector('#background_image').addEventListener('change', async (e) => {

        if (e.target.files[0]) {
            document.body.append(e.target.files[0]);

            var name = e.target.files[0].name;
            var base64Image = await readFileAsDataURL(e.target.files[0]);

            let preview = document.querySelectorAll('.background_image_preview');

            preview.forEach(element => {
                element.innerHTML = `<img src="${base64Image}" alt='${name}'/>`;
            });

            document.querySelector("#distinctive_image").value = base64Image;

            document.querySelector(".distinctive_image_hidden").style.display = 'none';
        }
    });


    document.querySelector("#keywords").addEventListener("keyup", (event, element) => {

        if (event.keyCode == 188 || event.keyCode == 32) {
            update();
        }

        let text = document.querySelector("#keywords").innerText;
        document.getElementsByName("keywords")[0].value = text;
    })

    function update() {

        var element = document.querySelector("#keywords");
        // let cursorPosition = getCaretCharacterOffsetWithin(document.querySelector("#keywords"));
        var re = new RegExp(/[^,\s]+/g, "gi");

        let typed = document.querySelector("#keywords").textContent;
        var count = 0;
        let typedSplit = typed.split(/\,|\s/);
        if (typedSplit.length > 1) {

            let newString = '';
            typedSplit.forEach((element, key, array) => {
                if (element.replace(" ", "").length > 0) {

                    newString += "<span class='word'>" + element + "</span>,";
                }

                // if (key === array.length - 1) {
                //     newString += "<span></span>";
                // }
            });
            var x = document.querySelector("#keywords").lastChild.length;
            document.querySelector("#keywords").innerHTML = newString

            document.execCommand('selectAll', false, null);
            // collapse selection to the end
            document.getSelection().collapseToEnd();
        }




    }

    function readFileAsDataURL(file) {
        return new Promise((resolve, reject) => {
            var reader = new FileReader();

            reader.onload = function () {
                resolve(reader.result);
            };

            reader.onerror = function (error) {
                reject(error);
            };

            reader.readAsDataURL(file);
        });
    }



    update();
    rewrite();

})
