const tabs = document.querySelectorAll(".tabs .tab");


tabs.forEach(tab => {
    tab.addEventListener("click", () => {
        tabs.forEach(tab_second => {
            tab_second.classList.remove("active");
        })

        tab.classList.add("active")

        switch (tab.dataset.type) {
            case "grid":
                document.querySelector("#grid").style.display = "block";
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

