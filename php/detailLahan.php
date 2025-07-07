<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../css/icon.css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <title>Tanduria</title>
</head>

<body>
    <section>
        <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4 md:items-center md:gap-8">
                <div class="md:col-span-3">
                    <img
                        src="https://images.unsplash.com/photo-1731690415686-e68f78e2b5bd?q=80&w=2670&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                        class="rounded"
                        alt="" />
                </div>

                <div class="md:col-span-1">
                    <div class="max-w-lg md:max-w-none px-4 mx-auto">
                        <h2 class="text-2xl font-semibold text-gray-900 sm:text-3xl">
                            Lahan Indramayu Idil
                        </h2>
                        <p class="mt-2 text-gray-700">
                            Lahan ini terletak di Indramayu, Jawa Barat. Jenis padi yang ditanam adalah padi premium
                            dengan kualitas terbaik. Mulai tanam pada 15 Mei 1945, lahan ini memiliki potensi hasil
                            panen yang tinggi.
                        </p>
                        <div class="flex-box items-center gap-2 mt-8">
                            <p class="text-black text-lg font-semibold">Lokasi Detail Lahan</p>
                            <p class="mt-2 text-black texl-sm font-semibold">Jalan Kajung 20 dekat sungai dekat rumah aldi</p>
                        </div>
                    </div>
                    <div class="mt-8 mx-auto px-2 flex justify-start items-start">
                        <a
                            class="group flex items-center justify-between gap-4 rounded-lg border border-indigo-600 bg-indigo-600 px-5 py-3 transition-colors hover:bg-transparent focus:ring-3 focus:outline-hidden"
                            href="#">
                            <span class="font-medium text-white transition-colors group-hover:text-indigo-600">
                                Lihat di Maps
                            </span>

                            <span class="shrink-0 rounded-full border border-current bg-white p-2 text-indigo-600">
                                <svg
                                    class="size-5 shadow-sm rtl:rotate-180"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <h3 class="text-black text-xl font-semibold mx-auto px-4 flex justify-start items-start">Spesifikasi Lahan</h3>
    <div class="flow-root grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 mx-auto px-4">
        <div class="">
            <dl
                class="divide-y divide-gray-200 rounded border border-gray-200 text-sm *:even:bg-gray-50">
                <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-gray-900">Luas Lahan</dt>

                    <dd class="text-gray-700 sm:col-span-2">110 Ha</dd>
                </div>

                <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-gray-900">Tanggal Penanaman</dt>

                    <dd class="text-gray-700 sm:col-span-2">20/12/1801</dd>
                </div>

                <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-gray-900">Hari Tanam</dt>

                    <dd class="text-gray-700 sm:col-span-2">19200</dd>
                </div>

                <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-gray-900">Fase Padi</dt>

                    <dd class="text-gray-700 sm:col-span-2">Pembucatan</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-gray-900">Jenis Pestisida</dt>

                    <dd class="text-gray-700 sm:col-span-2">Kanyodium</dd>
                </div>
            </dl>
        </div>

        <div>
            <dl
                class="divide-y divide-gray-200 rounded border border-gray-200 text-sm *:even:bg-gray-50">
                <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-gray-900">Pemupukan</dt>

                    <dd class="text-gray-700 sm:col-span-2">20/12/1802</dd>
                </div>

                <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-gray-900">Tanggal Penanaman</dt>

                    <dd class="text-gray-700 sm:col-span-2">20/12/1801</dd>
                </div>

                <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-gray-900">Modal Penanaman</dt>

                    <dd class="text-gray-700 sm:col-span-2">Rp. 10.000</dd>
                </div>

                <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-gray-900">Perkiraan Hasil Tanam</dt>

                    <dd class="text-gray-700 sm:col-span-2">10 Ton, Rp.10.000.000</dd>
                </div>
                <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-gray-900">Income Hasil Penanaman</dt>

                    <dd class="text-gray-700 sm:col-span-2">Rp. 50.000</dd>
                </div>
            </dl>
        </div>

    </div>

    <div>
        <h3 class="text-black text-xl font-semibold mx-auto px-4 flex justify-start items-start">Lokasi Lahan</h3>
        <div class="px-4 mx-auto">

            <iframe
                dir="rtl"
                class="rounded-s-lg mt-4"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3447.157123313611!2d107.71547209339076!3d-6.931217665918412!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68c302db3434f5%3A0xdf4aacdb8618199c!2sUIN%20Sunan%20Gunung%20Djati%20Bandung!5e0!3m2!1sid!2sid!4v1747523979240!5m2!1sid!2sid"
                width="100%" height="350" style="border:0; border-radius: 8px;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

    </div>


    <div class="mt-4 mx-auto px-4">
        <h3 class="text-black text-xl font-semibold mx-auto px-4 flex justify-start items-start">Aktifitas Lahan</h3>
        <div class="grid sm:grid-cols-3">
            <div class="mt-4 block rounded-md border border-gray-300 p-4 shadow-sm sm:p-6">
                <div class="sm:flex sm:justify-between sm:gap-4 lg:gap-6">
                    <div class="sm:mt-0">
                        <h3 class="text-lg font-medium text-pretty text-gray-900">
                            Pemberian Pupuk Kandang
                        </h3>

                        <p class="mt-1 text-sm text-gray-700">Oleh Mang Ujang</p>
                    </div>
                </div>

                <dl class="mt-6 flex gap-4 lg:gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-700">Tanggal</dt>

                        <dd class="text-xs text-gray-700">31/06/2025</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-700">Jumlah</dt>

                        <dd class="text-xs text-gray-700">10 Karung</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">Biaya</dt>

                        <dd class="text-xs text-gray-700">Rp. 210.000</dd>
                    </div>
                </dl>

                <div>
                    <p class="mt-4 font-reguler text-black sm:text-lg text-sm">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Inventore cupiditate odit ipsa necessitatibus? Consectetur eaque nostrum necessitatibus dicta numquam fugit, placeat facere possimus deserunt atque, earum accusamus ducimus est dolorum?
                    </p>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-4 mx-auto px-4">
        <h3 class="text-black text-xl font-semibold mx-auto px-4 flex justify-start items-start">Tambah Aktifitas Lahan</h3>
        <div>
            <article class="rounded-xl bg-white p-4 ring-3 ring-indigo-50 sm:p-6 lg:p-8">
                <form>
                    <select id="jenisAktifitas" class="select select-neutral">
                        <option disabled selected>Jenis Aktifitas</option>
                        <option value="pestisida">Penyiraman Pestisida</option>
                        <option value="pupuk">Pemberian Pupuk</option>
                        <option value="lainnya">Lainnya</option>
                    </select>

                    <!-- Form untuk Penyiraman Pestisida -->
                    <div id="formPestisida" class="mt-4 hidden">
                        <div class="grid grid-cols-1 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Nama Penyiram</legend>
                                <input type="text" class="input" placeholder="Ucok" />
                                <p class="label">Input nama penyiram</p>
                            </fieldset>

                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Tanggal Penyiraman</legend>
                                <input type="text" id="datepicker" class="input input-bordered w-full">
                                <p class="label">Input tanggal penyiraman</p>
                            </fieldset>

                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Luas Lahan</legend>
                                <input type="text" class="input input-bordered w-full" placeholder="10 Hektar">
                                <p class="label">Input luas lahan</p>
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Biaya Penyiraman</legend>
                                <input type="text" class="input input-bordered w-full" placeholder="10.000">
                                <p class="label">Input biaya penyiraman</p>
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Tambah Catatan</legend>
                                <textarea class="textarea" placeholder="Catatan"></textarea>
                                <p class="label">Opsional</p>
                            </fieldset>
                            <button class="btn btn-soft btn-success w-50 mt-10 ">Tambah Aktifitas</button>
                        </div>



                    </div>

                    <!-- Form untuk Pemberian Pupuk -->
                    <div id="formPupuk" class="mt-4 hidden">
                        <div class="grid grid-cols-1 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Nama Pemberi Pupuk</legend>
                                <input type="text" class="input" placeholder="Ucok" />
                                <p class="label">Input nama pemberi</p>
                            </fieldset>

                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Tanggal Pemupukan</legend>
                                <input type="text" id="datepicker" class="input input-bordered w-full">
                                <p class="label">Input tanggal pemupukan</p>
                            </fieldset>

                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Jumlah Pupuk</legend>
                                <input type="text" class="input input-bordered w-full" placeholder="10 Karung">
                                <p class="label">Input jumlah pupuk</p>
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Biaya Pemupukan</legend>
                                <input type="text" class="input input-bordered w-full" placeholder="10.000">
                                <p class="label">Input biaya pemupukan</p>
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Tambah Catatan</legend>
                                <textarea class="textarea" placeholder="Catatan"></textarea>
                                <p class="label">Opsional</p>
                            </fieldset>
                            <button class="btn btn-soft btn-success w-50 mt-10 ">Tambah Aktifitas</button>
                        </div>
                    </div>

                    <!-- Form untuk Lainnya -->
                    <div id="formLainnya" class="mt-4 hidden">
                        <div class="grid grid-cols-1 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Nama Aktifitas</legend>
                                <input type="text" class="input" placeholder="Bersihin Lahan" />
                                <p class="label">Input nama aktifitas</p>
                            </fieldset>

                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Tanggal</legend>
                                <input type="text" id="datepicker" class="input input-bordered w-full">
                                <p class="label">Input tanggal</p>
                            </fieldset>

                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Jumlah</legend>
                                <input type="text" class="input input-bordered w-full" placeholder="10 Karung">
                                <p class="label">Opsional</p>
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Biaya</legend>
                                <input type="text" class="input input-bordered w-full" placeholder="10.000">
                                <p class="label">Opsional</p>
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Tambah Catatan</legend>
                                <textarea class="textarea" placeholder="Catatan"></textarea>
                                <p class="label">Opsional</p>
                            </fieldset>
                            <button class="btn btn-soft btn-success w-50 mt-10 ">Tambah Aktifitas</button>
                        </div>
                    </div>
                </form>

            </article>
        </div>
    </div>

    <script src="../javascript/maps.js"></script>
    <script src="../javascript/other.js"></script>

</body>

</html>