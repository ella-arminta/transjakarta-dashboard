<?php
include 'api/mysql_connection.php'
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <!-- Leaflet JS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <link rel="stylesheet" href="navbar.css">
</head>
<style>
  .content {
    background: #f6f7fb;
    margin: 15px;
    padding: 20px;
    border-radius: 15px;
    display: flex;
    flex-direction: column;
    height: auto;
    position: relative;
  }

  .card {
    width: 100%;
    padding: 0;
    text-align: center;
    background-color: rgb(214, 227, 248);
  }

  .card-text {
    font-size: 1.1rem;
    font-weight: 500;
  }

  .row {
    margin-left: 0;
    margin-top: 0;
    margin-right: 0;
  }

  #map {
    height: 100vh;
    width: 80%;
    margin: auto;
  }

  #BarChartPremReg {
    /* height: 45vh; */
  }

  canvas {
    margin: auto;
    height: 42vh;
  }
</style>

<body>
  <main>
    <?php include 'navbar.php'; ?>

    <section>
      <h1 class="m-4">Transjakarta Analysis for Passangers</h1>

      <!-- Row 1 -->
      <div class="content">
        <div class="mb-4" style="width:100%; display:flex; justify-content:end">
          <div class="gap-4" style="display: flex; width:70%; justify-content:end">
            <!-- Nama pelanggan -->
            <div class="pelanggan">
              <label for="pelanggan">Nama pelanggan:</label>
              <br>
              <select name="pelanggan" id="pelanggan" style="width: 10vw;">
                <?php
                $stmt = $conn->prepare('select * from paycard');
                $stmt->execute();
                $pelanggan = $stmt->fetchAll();
                $index = 0;
                foreach ($pelanggan as $p) {
                  if ($index == 0) {
                    echo '<option default value="' . $p['payCardID'] . '">' . $p['payCardName'] . '</option>';
                  } else {
                    echo '<option value="' . $p['payCardID'] . '">' . $p['payCardName'] . '</option>';
                  }
                  $index++;
                }
                ?>
              </select>
            </div>
            <!-- waktu -->
            <div class="date">
              <label for="date">Date Range:</label>
              <br>
              <input type="date" value="2022-01-01" name="date_start" id="date_start" min="2022-01-01" max="2023-12-31">
              -
              <input type="date" value="2023-12-31" name="date_end" id="date_end" min="2022-01-01" max="2023-12-31">
            </div>

            <!-- waktu -->
            <div class="range">
              <label for="range">Range rata-rata:</label>
              <br>
              <select name="range" id="range" style="width: 10vw;">
                <!-- <option value="hari">Harian</option>
                    <option value="minggu">Mingguan</option> -->
                <option value="bulan" selected>Bulanan</option>
                <option value="tahun">Tahunan</option>
              </select>
            </div>

            <!-- search button -->
            <div class="search">
              <button type="button" id="searchbtn" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
          </div>

        </div>

        <div style="width: 100%; display:flex;">

          <div class="card">
            <div class="card-body">
              <p class="card-title
                ">Total Pengeluaran</p>
              <h5 id="total_pengeluaran" class="card-text">Rp. ...</h5>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <p class="card-title
                ">Rata-Rata Total Pengeluaran <span class="perwaktu"></span></p>
              <h5 id="average-pengeluaran" class="card-text">Rp. ...</h5>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <p class="card-title
                ">Jumlah Transaksi</p>
              <h5 id="jumlah_transaksi" class="card-text">...</h5>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <p class="card-title
                ">Rata-Rata jumlah Transaksi <span class="perwaktu"></span></p>
              <h5 class="card-text" id="average-jumlah-transaksi">...</h5>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <p class="card-title
                ">Total waktu dalam bis</p>
              <h5 id="total_bis" class="card-text">...</h5>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <p class="card-title
                ">Rata-rata waktu dalam bis <span class="perwaktu"></span></p>
              <h5 class="card-text" id="durasibis">...</h5>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 2 -->
      <div class="content" style="margin:15px;">
        <!-- Year Filter -->
        <div class="yearFilter" style="display: flex; gap:10px; justify-content:center; align-items:center">
          <label for="yearFilter">Year: </label>
          <br>
          <select name="yearFilter" id="yearFilter" style="width: 10vw;">
            <option value="2022" default>2022</option>
            <option value="2023">2023</option>
          </select>
        </div>
        <div class="row">
          <div class="col-5" style="padding-top: 0;">
            <!-- #1 Card Pengelompokan User Berdasarkan Jumlah naik per bulannya -->
            <div style="display:flex; margin-top:10px">
              <div class="card">
                <div class="card-body">
                  <p class="card-title
                      ">Min transaksi user perbulan</p>
                  <h5 id="min_transaksi" class="card-text">...</h5>
                </div>
              </div>
              <div class="card">
                <div class="card-body">
                  <p class="card-title
                      ">Max transaksi user perbulan</p>
                  <h5 id="max_transaksi" class="card-text">...</h5>
                </div>
              </div>
              <div class="card">
                <div class="card-body">
                  <p class="card-title
                      ">Avg transaksi users perbulan</p>
                  <h5 id="avg_transaksi" class="card-text">...</h5>
                </div>
              </div>
            </div>
            <!-- #1 Analisis Pengelompokan User berdasarkan jumlah naik per bulannya -->
            <h5 style="color:#787878;padding-left: 1%;padding-right: 1%;padding-top:10px; text-align:center">Pengelompokan User berdasarkan rata-rata jumlah transaksi perbulan</h5>
            <div style="">
              <canvas id="PieChart" style="width: 80%;"></canvas>
            </div>
          </div>

          <div class="col-7" style="padding-top: 0;">
            <!-- #2 Analisis perbandingan Premium dan Regular Per bulan -->
            <h5 style="color:#787878;padding-left: 5%;padding-right: 5%;padding-top:10px; text-align:center">Trend Transaksi berdasarkan Tipe Bis dan bulan</h5>
            <div style="padding-left: 10%;padding-right: 10%;">
              <canvas id="LineChartPremReg"></canvas>
            </div>

            <!-- #3 Jumlah Transaksi berdasarkan Tipe Bus yang sering digunakan dan Agenya -->
            <h5 style="color:#787878;padding-left: 5%;padding-right: 5%;padding-top:10px; text-align:center">Jumlah Transaksi berdasarkan Tipe Bus yang sering digunakan dan Usia</h5>
            <div style="padding-left: 10%;padding-right: 10%;">
              <canvas id="BarChartPremReg"></canvas>
            </div>
          </div>
        </div>

        <!-- #6 Pengelompokkan usia dan gender x=usia y = gender-->
        <h5 style="color:#787878;padding-left: 5%;padding-right: 5%;padding-top:10px; text-align:center">Analisa User berdasarkan usia dan gender</h5>
        <div style="padding-left: 10%;padding-right: 10%;">
          <canvas id="BarChartPremReg2"></canvas>
        </div>

        <!-- #6 Analisa user berdasarkan usia dan waktu -->
        <h5 style="color:#787878;padding-left: 5%;padding-right: 5%;padding-top:10px; text-align:center">Trend Transaksi berdasarkan usia dan jam</h5>
        <div style="padding-left: 10%;padding-right: 10%;">
          <canvas id="LineChartPremReg2"></canvas>
        </div>
      </div>

      <!-- Row 3 -->
      <div class="row content" style="margin: 15px;">
        <!-- Year and Hour Filter -->
        <div class="FiltersRow3" style="display: flex; gap:10px; justify-content:center; align-items:center;margin-bottom:2%">
          <!-- Year Filter 3 -->
          <label for="yearFilter2">Year: </label>
          <br>
          <select name="yearFilter2" id="yearFilter2" style="width: 10vw;">
            <option value="2022" default>2022</option>
            <option value="2023">2023</option>
          </select>

          <!-- Hour Filter 3 -->
          <label for="hourSelect">Hour: </label>
          <br>
          <select name="hourSelect" id="hourSelect" style="width: 10vw;">
            <?php
            for ($i = 0; $i < 24; $i++) {
              if ($i == 6) {
                echo '<option selected value=' . $i . '>' . str_pad($i, 2, '0', STR_PAD_LEFT) . ':00 - ' . str_pad($i + 1, 2, '0', STR_PAD_LEFT) . ':00</option>';
              } else {
                echo '<option value=' . $i . '>' . str_pad($i, 2, '0', STR_PAD_LEFT) . ':00 - ' . str_pad($i + 1, 2, '0', STR_PAD_LEFT) . ':00</option>';
              }
            }
            ?>
          </select>
          <select id="jenisKelamin" name="jenisKelamin">
            <option selected value="semua">semua</option>
            <option value="F">Perempuan</option>
            <option value="M">Laki-laki</option>
          </select>
        </div>
        <!-- #4 Top 30 Destination Popularity by time -->
        <h5 style="color:#787878;padding-left: 5%;padding-right: 5%;padding-top:10px; text-align:center">Top 30 Destinasi Terpopular berdasarkan waktu</h5>
        <div id="map"></div>
      </div>
    </section>
  </main>

  <script src="https://kit.fontawesome.com/e52db3bf8a.js" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    $(document).ready(function() {
      function formatCurrency(amount) {
        if (amount == null) {
          return "Rp. 0";
        }
        // Convert to a number if it's a string
        let num = parseFloat(amount);

        // Check if the number is valid
        if (isNaN(num)) {
          return "Invalid number";
        }

        // Format the number with commas
        let formattedAmount = num.toLocaleString('id-ID', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        });

        // Return the formatted currency string
        return `Rp. ${formattedAmount}`;
      }

      function getCardsData(startdate, enddate, id_pelanggan) {
        $.ajax({
          url: 'api/pelanggan.php',
          type: 'GET',
          data: {
            type: 'card',
            startdate: startdate,
            enddate: enddate,
            id_pelanggan: id_pelanggan,
            range: $('#range').val()
          },
          success: function(result) {
            try {
              var result = JSON.parse(result);
              $('#total_pengeluaran').text(formatCurrency(parseFloat(result.sum)));
              $('#jumlah_transaksi').text(result.count);
              $('#total_bis').text(result.total_duration_seconds);
              $('#average-pengeluaran').text(formatCurrency(result.averagePayAmountPerMonth));
              $('#average-jumlah-transaksi').text(result.averageJumlahTransaksi);
              $('#durasibis').text(result.averageDuration);
              $('.perwaktu').text('Per ' + result.waktu)
            } catch (e) {
              console.error("Failed to parse JSON response:", e);
            }
          },
          error: function(xhr, status, error) {
            console.error("Error:", status, error);
          }
        });
      }

      $('#searchbtn').click(function() {
        var date_start = $('#date_start').val();
        var date_end = $('#date_end').val();
        var id_pelanggan = $('#pelanggan').val();
        if (date_start == '' || date_end == '' || id_pelanggan == '') {
          alert('Please fill all the fields');
          return;
        }

        if (date_start > date_end) {
          alert('Invalid date range');
          return;
        }

        getCardsData(date_start, date_end, id_pelanggan);
      });

      getCardsData('2022-01-01', '2023-12-31', $('#pelanggan').val());
    });


    const navItems = document.querySelectorAll(".nav-item");

    navItems.forEach((navItem, i) => {
      navItem.addEventListener("click", () => {
        navItems.forEach((item, j) => {
          item.className = "nav-item";
        });
        navItem.className = "nav-item active";
      });
    });

    // #1 Analisis Pengelompokan User berdasarkan jumlah naik per bulannya
    var pieChartJs;

    function getAnalisisPengelompokanUserCode1() {
      $.ajax({
        type: "GET",
        url: "api/pelanggan.php",
        data: {
          type: '#1',
          year: $('#yearFilter').val()
        },
        success: function(response) {
          data = JSON.parse(response)
          $('#min_transaksi').text(data.min)
          $('#max_transaksi').text(data.max)
          $('#avg_transaksi').text(data.avg)
          const pieChart = document.getElementById('PieChart');
          if (pieChartJs) {
            pieChartJs.destroy(); // Destroy the previous chart instance
          }
          pieChartJs = new Chart(pieChart, {
            type: 'pie',
            data: {
              labels: data.analisis.labels,
              datasets: [{
                label: 'Data',
                data: data.analisis.data,
                backgroundColor: [
                  'rgb(255, 99, 132)',
                  'rgb(54, 162, 235)',
                  'rgb(255, 205, 86)'
                ],
                hoverOffset: 4
              }]
            },
          });
        }
      });
    }
    $('#yearFilter').on('change', function() {
      getAnalisisPengelompokanUserCode1()
    })
    getAnalisisPengelompokanUserCode1()

    // #2 Analisis perbandingan Premium dan Regular Per bulan
    var lineChartJs;

    function getAnalisisPremRegTrans() {
      $.ajax({
        type: "GET",
        url: "api/pelanggan.php",
        data: {
          type: '#2',
          year: $('#yearFilter').val()
        },
        success: function(response) {
          respData = JSON.parse(response)
          if (lineChartJs) {
            lineChartJs.destroy()
          }
          const lineChartPremReq = document.getElementById('LineChartPremReg');
          var data = {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [{
                label: 'Premium',
                data: respData.premium,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
              },
              {
                label: 'Regular',
                data: respData.regular,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
              },
            ]
          };

          var options = {
            responsive: true,
            scales: {
              x: {
                stacked: false
              },
              y: {
                stacked: false,
                ticks: {
                  stepSize: 500,
                }
              }
            }
          };

          lineChartJs = new Chart(lineChartPremReq, {
            type: 'line',
            data: data,
            options: options
          });
        }
      });
    }
    $('#yearFilter').on('change', function() {
      getAnalisisPremRegTrans()
    })
    getAnalisisPremRegTrans()

    // #3 
    var barChartJs;

    function getAnalisisDistAgeBusType() {
      $.ajax({
        type: "GET",
        url: "api/pelanggan.php",
        data: {
          type: '#3',
          year: $('#yearFilter').val()
        },
        success: function(response) {
          dataResp = JSON.parse(response)
          if (barChartJs) {
            barChartJs.destroy()
          }
          const barChartPremReq = document.getElementById('BarChartPremReg');
          var data = {
            labels: ['0 - 17 tahun', '18-25 tahun', '26-35 tahun', '36-45 tahun', '46-55 tahun', '> 55 tahun'],
            datasets: [{
                label: 'Premium',
                data: dataResp.premium,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
              },
              {
                label: 'Regular',
                data: dataResp.regular,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
              },
            ]
          };

          var options = {
            responsive: true,
            scales: {
              x: {
                stacked: false,
                label: 'Usia',
                title: {
                  display: true,
                  text: 'Usia'
                },
              },
              y: {
                stacked: false,
                ticks: {
                  stepSize: 10,
                },
                title: {
                  display: true,
                  text: 'Jumlah Transaksi'
                },

              }
            }
          };

          barChartJs = new Chart(barChartPremReq, {
            type: 'bar',
            data: data,
            options: options
          });
        }
      });
    }
    $('#yearFilter').on('change', function() {
      getAnalisisDistAgeBusType()
    })
    getAnalisisDistAgeBusType()


    // #4 Map
    var map = L.map('map').setView([-6.1944, 106.8229], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var circles = [];

    //remove all circles
    function removeAllCircles() {
      circles.forEach(function(circle) {
        map.removeLayer(circle);
      });
      circles = [];
    }

    function getAnalisisDestPopularity() {
      $.ajax({
        type: "GET",
        url: "api/pelanggan.php",
        data: {
          type: '#4',
          year: $('#yearFilter2').val(),
          hour: $('#hourSelect').val(),
          jenisKelamin: $('#jenisKelamin').val()
        },
        success: function(response) {
          dataResp = JSON.parse(response)
          jenisKelamin = $('#jenisKelamin').val()
          removeAllCircles()

          var mycolor;
          var myfillcolor;

          if (jenisKelamin == 'F') {
            mycolor = 'red';
            myfillcolor = 'red';
          } else if (jenisKelamin == 'M') {
            mycolor = 'blue';
            myfillcolor = 'blue';
          } else {
            mycolor = '#625da4';
            myfillcolor = '#625da4';
          }

          // Add Circles to the map
          dataResp.forEach(function(data) {

            var circle = L.circle([data.latitude, data.longitude], {
              color: mycolor,
              fillColor: myfillcolor,
              fillOpacity: data.fillOpacity,
              radius: data.radius
            }).addTo(map);

            circles.push(circle); // Store reference to the circle in the array

          })
        }
      });
    }
    getAnalisisDestPopularity();
    $('#yearFilter2').on('change', function() {
      getAnalisisDestPopularity()
    })
    $('#hourSelect').on('change', function() {
      getAnalisisDestPopularity()
    })
    $('#jenisKelamin').on('change', function() {
      getAnalisisDestPopularity()
    })

    // #6
    var barChartJs2;

    function getAnalisisUsiaGender() {
      $.ajax({
        type: "GET",
        url: "api/pelanggan.php",
        data: {
          type: '#6',
        },
        success: function(response) {
          dataResp = JSON.parse(response)
          if (barChartJs2) {
            barChartJs2.destroy()
          }
          const barChartPremReq2 = document.getElementById('BarChartPremReg2');
          var data = {
            // labels: ['0 - 17 tahun', '18-25 tahun', '26-35 tahun', '36-45 tahun', '46-55 tahun', '> 55 tahun'],
            datasets: [{
                label: 'Perempuan',
                data: dataResp.cewek,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
              },
              {
                label: 'Laki-laki',
                data: dataResp.cowok,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
              },
            ]
          };

          var options = {
            responsive: true,
            scales: {
              x: {
                stacked: false,
                label: 'Usia',
                title: {
                  display: true,
                  text: 'Usia'
                },
              },
              y: {
                stacked: false,
                ticks: {
                  stepSize: 10,
                },
                title: {
                  display: true,
                  text: 'Jumlah User'
                },

              }
            }
          };

          barChartJs2 = new Chart(barChartPremReq2, {
            type: 'bar',
            data: data,
            options: options
          });
        }
      });
    }
    $('#yearFilter').on('change', function() {
      getAnalisisUsiaGender()
    })
    getAnalisisUsiaGender()

    // #5 line chart time dan umur
    var lineChartJs2;

    function getAnalisisTimeAge() {
      $.ajax({
        type: "GET",
        url: "api/pelanggan.php",
        data: {
          type: '#5',
          year: $('#yearFilter').val()
        },
        success: function(response) {
          data = JSON.parse(response)
          if (lineChartJs2) {
            lineChartJs2.destroy()
          }
          // Menyiapkan data untuk Chart.js
          const labels = Array.from({
            length: 24
          }, (_, i) => i + 1);
          const datasets = Object.keys(data).map(ageGroup => ({
            label: ageGroup,
            data: data[ageGroup],
            fill: false,
            borderColor: getRandomColor(),
            tension: 0.1
          }));

          // Fungsi untuk mendapatkan warna acak
          function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
              color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
          }

          // Membuat chart
          const lineChartPremReq2 = document.getElementById('LineChartPremReg2');
          lineChartJs2 = new Chart(lineChartPremReq2, {
            type: 'line',
            data: {
              labels: labels,
              datasets: datasets
            },
            options: {
              scales: {
                x: {
                  title: {
                    display: true,
                    text: 'Hour of the Day'
                  }
                },
                y: {
                  title: {
                    display: true,
                    text: 'Transaction Count'
                  },
                  beginAtZero: true
                }
              }
            }
          });
        }
      });
    }
    $('#yearFilter').on('change', function() {
      getAnalisisTimeAge()
    })
    getAnalisisTimeAge()
  </script>
</body>

</html>