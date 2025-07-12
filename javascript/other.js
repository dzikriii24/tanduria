    function searchLahan() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#lahan a");

            rows.forEach(function(row) {
                let namaEl = row.querySelector(".nama_lahan");
                if (!namaEl) return; // skip kalau tidak ada

                let nama = namaEl.textContent.toLowerCase();

                if (nama.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }