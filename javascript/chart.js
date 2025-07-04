  const lahanData = [{
          nama: "Lahan 1",
          tanggalTanam: "23/02/2024",
          hariKe: 120
      },
      {
          nama: "Lahan 2",
          tanggalTanam: "01/03/2024",
          hariKe: 90
      },
      {
          nama: "Lahan 3",
          tanggalTanam: "10/03/2024",
          hariKe: 60
      },
      {
          nama: "Lahan 4",
          tanggalTanam: "20/03/2024",
          hariKe: 30
      }
  ];

  const ctx = document.getElementById('lahanChart').getContext('2d');

  const labels = lahanData.map(
      (lahan) => `${lahan.nama}||${lahan.tanggalTanam}`
  );


  const data = {
      labels: labels,
      datasets: [{
          label: 'Hari Pertumbuhan',
          data: lahanData.map(lahan => lahan.hariKe),
          backgroundColor: '#38bdf8',
          hoverBackgroundColor: '#0ea5e9'
      }]
  };

  const lahanChart = new Chart(ctx, {
              type: 'bar',
              data: data,
              options: {
                  responsive: true,
                  plugins: {
                      tooltip: {
                          callbacks: {
                              label: (context) => `Hari ke-${context.parsed.y}`
                          }
                      }
                  },
                  onClick: (e, elements) => {
                      if (elements.length > 0) {
                          const index = elements[0].index;
                          const hari = lahanData[index].hariKe;
                          const fase = getFaseByHari(hari);
                          const nama = lahanData[index].nama;

                          // Show modal
                          document.getElementById('modalTitle').textContent = nama;
                          document.getElementById('faseText').textContent = `Hari ke-${hari}, fase: ${fase}`;
                          document.getElementById('faseModal').classList.remove('hidden');
                          document.getElementById('faseModal').classList.add('flex');
                      }
                  },
                  scales: {
                      x: {
                          ticks: {
                              callback: function (value, index) {
                                  const label = this.getLabelForValue(value);
                                  return label.split('||');
                              },
                              font: {
                                  size: 12
                              }
                          }
                      },
                      y: {
                          title: {
                              display: true,
                              text: 'Hari ke-'
                          },
                          min: 0,
                          max: 130
                      }
                  }
                }
              });

          function getFaseByHari(hari) {
              if (hari <= 20) return 'Persemaian';
              else if (hari <= 45) return 'Vegetatif';
              else if (hari <= 80) return 'Berbunga';
              else if (hari <= 120) return 'Pematangan / Menjelang Panen';
              else return 'Panen / Melebihi Siklus';
          }

          function closeModal() {
              document.getElementById('faseModal').classList.add('hidden');
          }