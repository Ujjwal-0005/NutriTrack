<nav class="container mx-auto px-6 py-4">
    <div class="flex justify-between items-center">
        <div class="text-2xl text-white font-bold">NutriTrack<span class="text-secondary">2025</span></div>
        <div class="space-x-4 hidden md:flex items-center">
            <a href="index.php" class="text-secondary hover:text-secondary transition">Home</a>
            <?php if (isset($_SESSION['firstname'])): ?>
                <a href="dashboard/user.php" class="hover:text-secondary text-white transition">Dashboard</a>
                <a href="settings/user.php" class="hover:text-secondary text-white transition">Profile</a>
                <a href="logout.php" class="bg-white text-primary px-4 py-2 rounded-lg shadow hover:bg-opacity-90 transition">Sign Out</a>
            <?php else: ?>
                <a href="login.php" class="bg-white text-primary px-4 py-2 rounded-lg shadow hover:bg-opacity-90 transition">Login</a>
            <?php endif; ?>
        </div>
        <button class="md:hidden text-2xl"><i class="fas fa-bars"></i></button>
    </div>
</nav>