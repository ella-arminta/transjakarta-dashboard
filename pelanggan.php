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
                <select name="pelanggan" id="pelanggan">
                  <option value=""></option>
                </select>
              </div>
              <!-- waktu -->
              <div class="date">
                <label for="date">Date Range:</label>
                <br>
                <input type="date" name="date_start" id="date_start" min="2022-01-01" max="2023-12-31">
                -
                <input type="date" name="date_end" id="date_end" min="2022-01-01" max="2023-12-31">
              </div>
          </div>

        </div>

        <div style="width: 100%; display:flex;">

          <div class="card">
            <div class="card-body">
              <p class="card-title
              ">Total Pengeluaran</p>
              <h5 class="card-text">Rp. 1.000.000</h5>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <p class="card-title
              ">Rata-Rata Total Pengeluaran</p>
              <h5 class="card-text">Rp. 1.000.000</h5>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <p class="card-title
              ">Jumlah Transaksi</p>
              <h5 class="card-text">10</h5>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <p class="card-title
              ">Rata-Rata jumlah Transaksi</p>
              <h5 class="card-text">10</h5>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <p class="card-title
              ">Total waktu dalam bis</p>
              <h5 class="card-text">10</h5>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <p class="card-title
              ">Rata-rata waktu dalam bis</p>
              <h5 class="card-text">10</h5>
            </div>
          </div>

        </div>
      </section>
    </main>

    <script src="https://kit.fontawesome.com/e52db3bf8a.js" crossorigin="anonymous"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
    <script>
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