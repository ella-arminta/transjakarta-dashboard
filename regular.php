<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="navbar.css">
</head>
<body>
    <main>
      <?php include 'navbar.php'; ?>

      <section class="content">
        <div class="left-content">
            <h1>Transjakarta Analysis for Regular Bus</h1>

          
        </div>

        <div class="right-content"></div>


  
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