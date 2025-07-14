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


    function kirim() {
        Swal.fire({
            title: "Berhasil dikirim!",
            text: "Tunggu respon di notifikasi yaa!",
            icon: "success",
            draggable: true
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        const textarea = document.querySelector('textarea[name="gejala"]');
        const submitButton = document.querySelector('#formKonsultasi button[type="submit"]');

        function cekForm() {
            if (textarea.value.trim().length > 0) {
                submitButton.disabled = false;
                submitButton.classList.remove("opacity-50", "cursor-not-allowed");
            } else {
                submitButton.disabled = true;
                submitButton.classList.add("opacity-50", "cursor-not-allowed");
            }
        }

        textarea.addEventListener("input", cekForm);
        cekForm(); // Jalankan sekali saat halaman dimuat
    });

    function showFotoModal() {
        const src = document.getElementById("fotoProfilSrc").src;
        document.getElementById("modalFotoImage").src = src;
        my_modal_foto.showModal();
    }
