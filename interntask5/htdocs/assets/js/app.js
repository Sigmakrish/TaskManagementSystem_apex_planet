/* --------------------------------------------------------
   Task Management System - Custom JS
--------------------------------------------------------- */

// Confirm before deleting
function confirmDelete() {
    return confirm("Are you sure you want to delete this item?");
}

// Auto-hide alerts
setTimeout(() => {
    document.querySelectorAll(".alert").forEach(a => a.style.display = "none");
}, 3000);

// AJAX FILTER
document.addEventListener("DOMContentLoaded", function () {

    const filterForm = document.getElementById("filterForm");
    if (!filterForm) return;

    filterForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData).toString();

        fetch("./ajax_tasks.php?" + params)
            .then(res => res.text())
            .then(html => {
                document.getElementById("taskList").innerHTML = html;
            });
    });

});
