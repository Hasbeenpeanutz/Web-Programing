// ===== Hamburger menu (JS-driven, replaces the checkbox hack) =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");

    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}


// ===== Delete confirmation =====
function initDeleteConfirm() {
    document.querySelectorAll(".btn-delete").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const row = btn.closest("tr");

            const name = row
                ? row.querySelector("td")?.textContent.trim()
                : "this item";

            const confirmed = confirm(
                'Are you sure you want to delete "' + name + '"?'
            );

            if (confirmed && row) {
                row.remove();
            }
        });
    });
}


// ===== Real-time table filter/search =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");

    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");

        rows.forEach(function (row) {
            // Exercise 3:
            // Search only in the first column
            const firstCell = row.querySelector("td");

            const text = firstCell
                ? firstCell.textContent.toLowerCase()
                : "";

            row.style.display = text.includes(keyword) ? "" : "none";
        });
    });
}


// ===== Form validation =====
function showError(input, message) {
    removeError(input);

    const span = document.createElement("span");
    span.className = "error";
    span.textContent = message;

    input.insertAdjacentElement("afterend", span);
}


function removeError(input) {
    const next = input.nextElementSibling;

    if (next && next.classList.contains("error")) {
        next.remove();
    }
}


function initFormValidation() {
    const form = document.getElementById("form-add");

    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;


        // ===== Title / Member Name =====
        const title = form.querySelector(
            "[name='title'], [name='name']"
        );

        if (title && title.value.trim() === "") {
            showError(title, "This field is required.");
            valid = false;
        } else if (title) {
            removeError(title);
        }


        // ===== Author =====
        const author = form.querySelector("[name='author']");

        if (author && author.value.trim() === "") {
            showError(author, "Author is required.");
            valid = false;
        } else if (author) {
            removeError(author);
        }


        // ===== Year =====
        const year = form.querySelector("[name='year']");

        if (year) {
            const value = parseInt(year.value, 10);

            if (
                year.value.trim() === "" ||
                isNaN(value) ||
                value < 1900 ||
                value > 2026
            ) {
                showError(
                    year,
                    "Year must be between 1900-2026."
                );

                valid = false;
            } else {
                removeError(year);
            }
        }


        // ===== Stock =====
        const stock = form.querySelector("[name='stock']");

        if (stock) {
            const value = parseInt(stock.value, 10);

            if (
                stock.value.trim() === "" ||
                isNaN(value) ||
                value < 0
            ) {
                showError(
                    stock,
                    "Stock cannot be negative."
                );

                valid = false;
            } else {
                removeError(stock);
            }
        }


        // ===== Member Number =====
        const memberNo = form.querySelector("[name='member_no']");

        if (memberNo && memberNo.value.trim() === "") {
            showError(
                memberNo,
                "Member number is required."
            );

            valid = false;
        } else if (memberNo) {
            removeError(memberNo);
        }


        // ===== ISBN Validation =====
        // Exercise 1:
        // ISBN may contain only numbers and hyphens.
        const isbn = form.querySelector("[name='isbn']");

        if (isbn) {
            const value = isbn.value.trim();
            const isbnPattern = /^[0-9-]+$/;

            if (value !== "" && !isbnPattern.test(value)) {
                showError(
                    isbn,
                    "ISBN can contain only digits and hyphens."
                );

                valid = false;
            } else {
                removeError(isbn);
            }
        }


        // Prevent form submission if validation fails
        if (!valid) {
            e.preventDefault();
        }
    });
}


// ===== Entry Point =====
document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initDeleteConfirm();
    initTableFilter();
    initFormValidation();
});