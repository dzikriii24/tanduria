document.getElementById('jenisAktifitas').addEventListener('change', function () {
    const value = this.value;

  
    document.getElementById('formPestisida').classList.add('hidden');
    document.getElementById('formPupuk').classList.add('hidden');
    document.getElementById('formLainnya').classList.add('hidden');

    if (value === 'pestisida') {
      document.getElementById('formPestisida').classList.remove('hidden');
    } else if (value === 'pupuk') {
      document.getElementById('formPupuk').classList.remove('hidden');
    } else if (value === 'lainnya') {
      document.getElementById('formLainnya').classList.remove('hidden');
    }
  });


//   Date Picker
  flatpickr("#datepicker", {
    dateFormat: "d/m/Y",
    defaultDate: "today",
    locale: "id" // pakai bahasa Indonesia
  });