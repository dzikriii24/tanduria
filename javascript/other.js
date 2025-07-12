    function searchLahan() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let rows = document.querySelectorAll("#lahan a");

        rows.forEach(function (row) {
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

    function confirmLogout() {
        Swal.fire({
            title: 'Keluar dari akun?',
            text: "Anda akan diarahkan ke halaman login.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        });
    }