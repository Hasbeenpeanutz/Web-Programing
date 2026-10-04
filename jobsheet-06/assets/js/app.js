// ===== Hamburger menu (JS-driven, replaces the checkbox hack) =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");

    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}


// ===== Delete confirmation + row counter =====
function initDeleteConfirm() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-delete");

        if (!btn) return;

        const row = btn.closest("tr");

        const name = row
            ? row.querySelector("td")?.textContent.trim()
            : "this item";

        const confirmed = confirm(
            'Are you sure you want to delete "' + name + '"?'
        );

        if (confirmed && row) {
            row.remove();
            updateRowCounter();
        }
    });
}

// ===== Remaining-row counter =====
function updateRowCounter() {
    const table = document.querySelector(".table-responsive table");
    const counter = document.getElementById("row-counter");

    if (!table || !counter) return;

    const rows = table.querySelectorAll("tbody tr");

    let visibleRows = 0;

    rows.forEach(function (row) {
        if (row.style.display !== "none") {
            visibleRows++;
        }
    });

    const totalRows = rows.length;

    const isBookPage = window.location.pathname.includes("/books/");
    const itemName = isBookPage ? "books" : "members";

    counter.textContent =
        "Showing " + visibleRows + " of " + totalRows + " " + itemName;
}

// ===== Real-time table filter/search + row counter =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");

    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");

        rows.forEach(function (row) {
            const firstCell = row.querySelector("td");

            const text = firstCell
                ? firstCell.textContent.toLowerCase()
                : "";

            row.style.display =
                text.includes(keyword) ? "" : "none";
        });

        updateRowCounter();
    });

    updateRowCounter();
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


// ===== Refactored Form Validation =====
function initFormValidation() {
    const form = document.getElementById("form-add");

    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        // Required text fields
        const requiredFields = [
            {
                selector: "[name='title']",
                message: "Title is required."
            },
            {
                selector: "[name='name']",
                message: "Name is required."
            },
            {
                selector: "[name='author']",
                message: "Author is required."
            },
            {
                selector: "[name='member_no']",
                message: "Member number is required."
            }
        ];

        requiredFields.forEach(function (field) {
            const input = form.querySelector(field.selector);

            if (!input) return;

            if (input.value.trim() === "") {
                showError(input, field.message);
                valid = false;
            } else {
                removeError(input);
            }
        });


        // ===== Year validation =====
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


        // ===== Stock validation =====
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


        // ===== ISBN validation =====
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


        // Stop form submission when validation fails
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