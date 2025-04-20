<style>
    .hh{
        background-color: #7f5a83;
background-image: linear-gradient(315deg, #7f5a83 0%, #0d324d 74%);


}
    </style>

<nav class="container mx-auto px-6 py-4">
    <div class="flex justify-between items-center">
        <div class="text-2xl font-bold text-white">NutriTrack</div>
        <div class="space-x-4 hidden md:flex items-center">
            <a href="index.php" class="text-secondary p-[8px] py-2 px-3  hover:text-white hover:font-bold text-white transition">Home</a>
            <?php if (isset($_SESSION['firstname'])): ?>
                <a href="dashboard/user.php" class="py-2 px-3 hover:text-white text-white hover:font-bold  transition">Dashboard</a>
                <a href="dashboard/challenges.php" class="py-2 px-3 hover:text-white text-white hover:font-bold   rounded-md">Challenges</a>
                <a href="settings/user.php" class="py-2 px-3 hover:text-white text-white hover:font-bold  transition">Profile</a>
                <a href="logout.php" class=" hh bg-white text-white  text-primary hover:font-bold  px-4 py-2  rounded-lg shadow hover:bg-opacity-90 button-glow transition">Sign Out</a>
            <?php else: ?>
                <a href="login.php" class="hh bg-white text-white  text-primary hover:font-bold  px-4 py-2  rounded-lg shadow hover:bg-opacity-90 button-glow transition">Login</a>
            <?php endif; ?>
        </div>
        <button class="md:hidden text-2xl"><i class="fas fa-bars"></i></button>
    </div>
</nav>
