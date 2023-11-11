const tabs = document.querySelectorAll(".tabs .tab");


tabs.forEach(tab => {
    tab.addEventListener("click", () => {
        tabs.forEach(tab_second => {
            tab_second.classList.remove("active");
        })

        tab.classList.add("active")

        switch (tab.dataset.type) {
            case "grid":
                document.querySelector("#grid").style.display = "flex";
                document.querySelector("#kanban").style.display = "none";
                document.querySelector("#list").style.display = "none";
                break;
            case "kanban":
                document.querySelector("#grid").style.display = "none";
                document.querySelector("#kanban").style.display = "flex";
                document.querySelector("#list").style.display = "none";
                break;
            case "list":
                document.querySelector("#grid").style.display = "none";
                document.querySelector("#kanban").style.display = "none";
                document.querySelector("#list").style.display = "block";
                break;
            default:
                break;
        }
    });
})


// INIT GRID



let data = [
    {
        "id": 1,
        "title": "test 1",
        "short_description": "Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.",
        "background": "https://cdn.pixabay.com/photo/2023/11/05/21/04/alps-8368328_1280.jpg"
    },
    {
        "id": 2,
        "title": "test 2",
        "short_description": "Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.",
        "background": "https://images.unsplash.com/photo-1682687221175-fd40bbafe6ca?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
    },
    {
        "id": 3,
        "title": "test 3",
        "short_description": "Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.",
        "background": "https://cdn.pixabay.com/photo/2023/11/05/21/04/alps-8368328_1280.jpg"
    },
    {
        "id": 4,
        "title": "test 4",
        "short_description": "Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.",
        "background": "https://cdn.pixabay.com/photo/2023/11/05/21/04/alps-8368328_1280.jpg"
    },
    {
        "id": 5,
        "title": "test 5",
        "short_description": "Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.",
        "background": "https://cdn.pixabay.com/photo/2023/11/05/21/04/alps-8368328_1280.jpg"
    },
    {
        "id": 6,
        "title": "test 6",
        "short_description": "Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.",
        "background": "https://cdn.pixabay.com/photo/2023/11/05/21/04/alps-8368328_1280.jpg"
    },
    {
        "id": 7,
        "title": "test 7",
        "short_description": "Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.",
        "background": "https://cdn.pixabay.com/photo/2023/11/05/21/04/alps-8368328_1280.jpg"
    },
    {
        "id": 8,
        "title": "test 8",
        "short_description": "Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.",
        "background": "https://cdn.pixabay.com/photo/2023/11/05/21/04/alps-8368328_1280.jpg"
    },
    {
        "id": 9,
        "title": "test 9",
        "short_description": "Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.Culpa anim ex exercitation deserunt duis enim minim. Id magna et esse amet do in.",
        "background": "https://cdn.pixabay.com/photo/2023/11/05/21/04/alps-8368328_1280.jpg"
    }
]

var grid = new Grid(50);
grid.load(data);