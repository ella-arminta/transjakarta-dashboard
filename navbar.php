<nav class="main-menu">
    <h1>Fitness App</h1>
    <img style="background-color: white; width:60px;height:auto" class="logo rounded-circle py-2 px-1" src="assets/logo-transjakarta.png" alt="" />
    <ul>
        <li class="nav-item <?php echo end(explode('/', $_SERVER['SCRIPT_NAME'])) == 'regular.php' ? 'active' : ''; ?>">
            <b></b>
            <b></b>
            <a href="regular.php">
                <i class="fa fa-house nav-icon"></i>
                <span class="nav-text">Home</span>
            </a>
        </li>

        <li class="nav-item <?php echo end(explode('/', $_SERVER['SCRIPT_NAME'])) == 'premium.php' ? 'active' : ''; ?>">
            <b></b>
            <b></b>
            <a href="premium.php">
                <i class="fa-solid fa-crown"></i>
                <span class="nav-text">Premium</span>
            </a>
        </li>

        <li class="nav-item <?php echo end(explode('/', $_SERVER['SCRIPT_NAME'])) == 'pelanggan.php' ? 'active' : ''; ?>">
            <b></b>
            <b></b>
            <a href="pelanggan.php">
                <i class="fa fa-user nav-icon"></i>
                <span class="nav-text">Profile</span>
            </a>
        </li>


        <li class="nav-item <?php echo end(explode('/', $_SERVER['SCRIPT_NAME'])) == 'settings.php' ? 'active' : ''; ?>">
            <b></b>
            <b></b>
            <a href="settings.php">
                <i class="fa fa-sliders nav-icon"></i>
                <span class="nav-text">Settings</span>
            </a>
        </li>

    </ul>
</nav>