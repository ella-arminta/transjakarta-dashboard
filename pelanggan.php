<?php 
include 'api/mysql_connection.php'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

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

  .card{
    width: 100%;
    padding: 0;
    text-align: center;
    background-color: rgb(214, 227, 248);
  }
  .card-text{
    font-size: 1.1rem;
    font-weight: 500;
  }
</style>
<body>
    <main>
      <?php include 'navbar.php'; ?>

      <section class="content">
        <div class="mb-4" style="width:100%; display:flex; justify-content:space-between">
          <h1>Transjakarta Analysis for Passangers</h1>

          <div class="gap-4" style="display: flex; ">
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
                    foreach($pelanggan as $p){
                      if ($index == 0){
                        echo '<option default value="'.$p['payCardID'].'">'.$p['payCardName'].'</option>';
                      }else{
                        echo '<option value="'.$p['payCardID'].'">'.$p['payCardName'].'</option>';
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
              ">Rata-Rata Total Pengeluaran</p>
              <h5 class="card-text">Rp. ...</h5>
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
              ">Rata-Rata jumlah Transaksi</p>
              <h5 class="card-text">...</h5>
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
              ">Rata-rata waktu dalam bis</p>
              <h5 class="card-text">...</h5>
            </div>
          </div>

        </div>
      </section>
    </main>

    <script src="https://kit.fontawesome.com/e52db3bf8a.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
    <script>
      $(document).ready(function(){
          function formatCurrency(amount) {
              // Convert to a number if it's a string
              let num = parseFloat(amount);

              // Check if the number is valid
              if (isNaN(num)) {
                  return "Invalid number";
              }

              // Format the number with commas
              let formattedAmount = num.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

              // Return the formatted currency string
              return `Rp. ${formattedAmount}`;
          }

          function getCardsData(startdate, enddate, id_pelanggan){
              $.ajax({
                  url: 'api/pelanggan.php',
                  type: 'GET',
                  data: {
                      type:'card',
                      startdate: startdate,
                      enddate: enddate,
                      id_pelanggan: id_pelanggan
                  },
                  success: function(result){
                      try {
                          var result = JSON.parse(result);
                          $('#total_pengeluaran').text(formatCurrency(parseFloat(result.sum)));
                          $('#jumlah_transaksi').text(result.count);
                          $('#total_bis').text(result.total_duration_seconds);
                      } catch (e) {
                          console.error("Failed to parse JSON response:", e);
                      }
                  },
                  error: function(xhr, status, error) {
                      console.error("Error:", status, error);
                  }
              });
          }

          $('#searchbtn').click(function(){
              var date_start = $('#date_start').val();
              var date_end = $('#date_end').val();
              var id_pelanggan = $('#pelanggan').val();
              console.log(date_start, date_end, id_pelanggan)
              if (date_start == '' || date_end == '' || id_pelanggan == ''){
                  alert('Please fill all the fields');
                  return;
              }

              if (date_start > date_end){
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

    </script>
  </body>
</html>