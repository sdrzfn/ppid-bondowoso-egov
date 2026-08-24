(function() {
    'use strict';

    const config = window.searchbarConfig || {};
    const targetId = config.targetId || 'cardsContainer';
    const searchEndpoint = config.searchEndpoint || 'search-cards.php';
    const filterEndpoint = config.filterEndpoint || 'filter-cards.php';

    const searchInput = document.getElementById("searchInput");
    const container = document.getElementById(targetId);

    // Search functionality
    if (searchInput && container) {
        searchInput.addEventListener("keyup", function () {
            const query = this.value.trim();

            // tampilkan indikator loading
            container.innerHTML = `<div class="col-span-full text-center text-slate-500 py-10">Mencari...</div>`;

            fetch(searchEndpoint + "?q=" + encodeURIComponent(query))
                .then(res => res.text())
                .then(html => {
                    container.innerHTML = html;
                })
                .catch(err => {
                    container.innerHTML = `<div class="col-span-full text-center text-red-500 py-10">Terjadi kesalahan</div>`;
                    console.error(err);
                });
        });
    }

    // Filter functionality
    document.addEventListener("DOMContentLoaded", function () {
        const filterBtn = document.getElementById("filterBtn");
        const filterModal = document.getElementById("filterModal");
        const closeFilter = document.getElementById("closeFilter");
        const filterForm = document.getElementById("filterForm");
        const searchKeywordInput = document.getElementById("filterSearchKeyword");

        // Set keyword dari search input ke hidden field di filter
        if (searchInput && searchKeywordInput) {
            searchKeywordInput.value = searchInput.value;
        }

        // Toggle modal
        if (filterBtn && filterModal) {
            filterBtn.addEventListener("click", () => {
                if (searchKeywordInput && searchInput) {
                    searchKeywordInput.value = searchInput.value;
                }
                filterModal.classList.remove("hidden");
            });
        }

        if (closeFilter && filterModal) {
            closeFilter.addEventListener("click", () => {
                filterModal.classList.add("hidden");
            });

            // Tutup modal jika klik di luar area modal
            filterModal.addEventListener("click", function(e) {
                if (e.target === this) {
                    filterModal.classList.add("hidden");
                }
            });
        }

        // Submit filter
        if (filterForm && container) {
            filterForm.addEventListener("submit", function (e) {
                e.preventDefault();
                const formData = new FormData(filterForm);

                // Panggil AJAX filter
                fetch(filterEndpoint, {
                    method: "POST",
                    body: formData
                })
                    .then(res => res.text())
                    .then(html => {
                        container.innerHTML = html;
                        filterModal.classList.add("hidden");
                    })
                    .catch(err => {
                        console.error(err);
                        container.innerHTML = `<div class="col-span-full text-center text-red-500 py-10">Terjadi kesalahan</div>`;
                    });
            });
        }
    });

    // Tutup modal filter dengan Escape
    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") {
            const filterModal = document.getElementById("filterModal");
            if (filterModal && !filterModal.classList.contains("hidden")) {
                filterModal.classList.add("hidden");
            }
        }
    });
})();