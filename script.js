document.addEventListener("DOMContentLoaded", function () {

    // MOBILE NAVIGATION

    const mobileMenu = document.getElementById("mobileMenu");
    const publicNav = document.getElementById("publicNav");

    if (mobileMenu && publicNav) {

        mobileMenu.addEventListener("click", function () {

            publicNav.classList.toggle("show");

            const icon = mobileMenu.querySelector("i");

            if (publicNav.classList.contains("show")) {
                icon.classList.remove("fa-bars");
                icon.classList.add("fa-xmark");
            } else {
                icon.classList.remove("fa-xmark");
                icon.classList.add("fa-bars");
            }

        });

    }

    // TABLE SEARCH

    const searchInput = document.getElementById("searchTable");
    const table = document.getElementById("tableData");

    if (searchInput && table) {

        searchInput.addEventListener("input", function () {

            const keyword = this.value.toLowerCase();

            const rows = table.querySelectorAll("tbody tr");

            rows.forEach(function (row) {

                const text = row.innerText.toLowerCase();

                row.style.display =
                    text.includes(keyword) ? "" : "none";

            });

        });

    }

    // CONFIRM DELETE / ACTION

    document.querySelectorAll("[data-confirm]").forEach(function (button) {

        button.addEventListener("click", function (event) {

            const message = this.dataset.confirm;

            if (!confirm(message)) {
                event.preventDefault();
            }

        });

    });

    // FORMAT INPUT RUPIAH

    document.querySelectorAll("[data-rupiah]").forEach(function (input) {

        input.addEventListener("input", function () {

            let value = this.value.replace(/\D/g, "");

            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        });

    });

});
