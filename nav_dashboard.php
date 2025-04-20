<style>
    .hhhh{
        background-color: #b0c4de;
        background-image: linear-gradient(315deg, #b0c4de 0%, #016396 74%);


}
    </style>
<nav class="container mx-auto px-6 py-4">
    <div class="flex justify-between items-center">
        <div class="text-2xl font-bold">NutriTrack</div>
        <div class="space-x-4 hidden md:flex items-center">
            <a href="../index.php" class="hover:text-secondary transition">Home</a>
            <?php if (isset($_SESSION['firstname'])): ?>
                <a href="../dashboard/user.php" class="hover:text-secondary transition">Dashboard</a>
                <a href="../dashboard/challenges.php" class="py-2 px-3 rounded-md">Challenges</a>
                <a href="../settings/user.php" class="hover:text-secondary transition">Profile</a>
                <?php if ($_SESSION['admin'] == 1): ?>
                    <a href="../admin/dashboard.php" class="hover:text-secondary transition">Admin Dashboard</a>
                <?php endif; ?>
                <a href="../logout.php" class="bg-white text-white text-primary hhhh px-4 py-2 rounded-lg shadow hover:bg-opacity-90 transition animate-pulse">Sign Out</a>
            <?php else: ?>
                <a href="../login.php" class="bg-white text-white button-glow hover-scale text-primary hhhh px-4 py-2 rounded-lg shadow hover:bg-opacity-90 transition">Login</a>
            <?php endif; ?>
        </div>
        <button class="md:hidden text-2xl"><i class="fas fa-bars"></i></button>
    </div>
</nav>
