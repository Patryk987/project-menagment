const tabs = document.querySelectorAll(".tabs .tab");

tabs.forEach(tab => {
    tab.addEventListener("click", () => {
        tabs.forEach(tab_second => {
            tab_second.classList.remove("active");
        })

        tab.classList.add("active")

        switch (tab.dataset.type) {
            case "kanban":
                document.querySelector("#kanban").style.display = "flex";
                document.querySelector("#list").style.display = "none";
                kanban.load();
                break;
            case "list":
                document.querySelector("#kanban").style.display = "none";
                document.querySelector("#list").style.display = "block";
                task.loadTask();
                break;
            default:
                break;
        }
    });
})

