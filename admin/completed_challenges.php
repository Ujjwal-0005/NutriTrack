<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

$completed_challenges_table_check = $conn->query("SHOW TABLES LIKE 'completed_challenges'");
if ($completed_challenges_table_check->num_rows == 0) {
    $create_completed_challenges_table = "CREATE TABLE completed_challenges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        challenge_id INT NOT NULL,
        completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (challenge_id) REFERENCES challenges(id) ON DELETE CASCADE
    )";
    $conn->query($create_completed_challenges_table);
$challenge_points_table_check = $conn->query("SHOW TABLES LIKE 'challenge_points'");
if ($challenge_points_table_check->num_rows == 0) {
    $create_challenge_points_table = "CREATE TABLE challenge_points (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        points INT NOT NULL DEFAULT 0,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->query($create_challenge_points_table);
}
}

$sql = "SELECT cc.id, cc.completed_at, u.id as user_id, u.firstname, u.lastname, u.email, 
        c.id as challenge_id, c.title, c.category, c.points, c.difficulty, c.badge_image  
        FROM completed_challenges cc
        JOIN users u ON cc.user_id = u.id
        JOIN challenges c ON cc.challenge_id = c.id
        ORDER BY cc.completed_at DESC";

$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
$points_query = "SELECT cp.user_id, cp.points, u.firstname, u.lastname 
                FROM challenge_points cp
                JOIN users u ON cp.user_id = u.id
                ORDER BY cp.points DESC";
$points_result = $conn->query($points_query);

$user_points = [];
if ($points_result && $points_result->num_rows > 0) {
    while($point_row = $points_result->fetch_assoc()) {
        $user_points[$point_row['user_id']] = $point_row['points'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Challenges | NutriTrack 2025</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#10B981',
                        dark: '#111827',
                        admin: '#BE185D',
                    },
                    fontFamily: {
                        sans: ['Inter var', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Top Navigation -->
    <style>
        .das{
            background-color: #b92e34;
background-image: linear-gradient(326deg, #b92e34 0%, #3d0c02 74%);
        }
    </style>
    <nav class="das text-white shadow-md">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <span class="text-2xl font-bold mr-4">
                    <span class="text-orange-500">Admin</span>Panel
                </span>
                <div class="hidden md:flex space-x-6 font-medium text-sm">
                    <a href="dashboard.php" class="py-2 px-3 hover:bg-orange-100 hover:text-orange-900 rounded-md">Dashboard</a>
                    <a href="users.php" class="py-2 px-3 hover:bg-orange-100 hover:text-orange-900 rounded-md">Users</a>
                    <a href="foods.php" class="py-2 px-3 hover:bg-orange-100 hover:text-orange-900 rounded-md">Food Entries</a>
                    <a href="workouts.php" class="py-2 px-3 hover:bg-orange-100 hover:text-orange-900 rounded-md">Workout Entries</a>
                    <a href="challenges.php" class="py-2 px-3 bg-orange-700 hover:bg-orange-100 hover:text-orange-900 rounded-md text-white">Challenges</a>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="../dashboard/user.php" class="text-sm hover:underline">
                    <i class="fas fa-home mr-1"></i> User Dashboard
                </a>
                <div class="relative group">
                    <button class="flex py-2 px-3 items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-admin flex items-center justify-center">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <span class="hidden md:inline-block"><?php echo $_SESSION['firstname']; ?></span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute right-0 w-48 bg-white rounded-md shadow-lg py-1 mt-2 z-10 hidden group-hover:block">
                        <a href="../settings/admin_profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-cog mr-2"></i> Admin Profile
                        </a>
                        <a href="../logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <style>
        .new{
            background-color: #f2f0ef;
background-image: linear-gradient(315deg, #f2f0ef 0%, #fbceb1 74%);

        }
    </style>
    <div class="container new mx-auto px-6 py-8">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Completed Challenges</h1>
                <p class="text-gray-600 mt-1">View all completed challenges by users</p>
            </div>
            <a href="challenges.php" class="bg-admin hover:bg-pink-800 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Challenges
            </a>
        </div>

        <!-- Top Users by Points -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="p-5 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Top Users by Challenge Points</h2>
            </div>
            <div class="p-6">
                <?php if (!empty($user_points)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php 
                        $i = 1;
                        foreach ($user_points as $user_id => $points): 
                            // Only show top 6 users
                            if ($i > 6) break;
                            
                            // Find user name
                            $name = "";
                            $points_result->data_seek(0);
                            while($row = $points_result->fetch_assoc()) {
                                if ($row['user_id'] == $user_id) {
                                    $name = $row['firstname'] . ' ' . $row['lastname'];
                                    break;
                                }
                            }
                            
                            // Define medal color based on ranking
                            $medal_color = 'text-gray-500'; // Default
                            if ($i === 1) $medal_color = 'text-yellow-500'; // Gold
                            else if ($i === 2) $medal_color = 'text-gray-400'; // Silver
                            else if ($i === 3) $medal_color = 'text-amber-700'; // Bronze
                        ?>
                            <div class="flex items-center bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex-shrink-0 mr-4">
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-admin bg-opacity-10">
                                        <i class="fas fa-medal <?php echo $medal_color; ?> text-2xl"></i>
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        <?php echo htmlspecialchars($name); ?>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        ID: <?php echo $user_id; ?>
                                    </p>
                                </div>
                                <div class="inline-flex items-center text-base font-semibold text-gray-900 bg-yellow-100 px-3 py-1 rounded-full">
                                    <?php echo $points; ?> points
                                </div>
                            </div>
                        <?php 
                        $i++;
                        endforeach; 
                        ?>
                    </div>
                <?php else: ?>
                    <div class="text-center p-6">
                        <div class="inline-flex bg-blue-100 rounded-full p-4 mb-4">
                            <i class="fas fa-trophy text-blue-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No points earned yet</h3>
                        <p class="text-gray-500">Users haven't completed any challenges yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Completed Challenges List -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Challenge Completions</h2>
                
                <!-- Filter/Search could be added here -->
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search by user or challenge..." 
                           class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                    <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                </div>
            </div>
            
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Challenge
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category
                                </th>
                               
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Points
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Completed
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Badge
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="completionsTable">
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-admin text-white flex items-center justify-center">
                                                <span class="font-bold"><?php echo strtoupper(substr($row['firstname'], 0, 1) . substr($row['lastname'], 0, 1)); ?></span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($row['email']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium">
                                            <?php echo htmlspecialchars($row['title']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ID: <?php echo $row['challenge_id']; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo ucfirst($row['category']); ?>
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm bg-yellow-100 text-yellow-800 inline-flex items-center px-2.5 py-0.5 rounded-full">
                                            <i class="fas fa-star mr-1 text-xs"></i>
                                            <?php echo $row['points']; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php 
                                            $completed_date = new DateTime($row['completed_at']);
                                            echo $completed_date->format('M d, Y') . ' at ' . $completed_date->format('g:i A');
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($row['badge_image']): ?>
                                            <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-100 border border-gray-200">
                                                <img src="../uploads/badges/<?php echo $row['badge_image']; ?>" 
                                                     alt="Badge" class="h-full w-full object-cover">
                                            </div>
                                        <?php else: ?>
                                            <div class="h-10 w-10 rounded-full bg-admin flex items-center justify-center">
                                                <i class="fas fa-trophy text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center p-12">
                    <div class="inline-flex bg-blue-100 rounded-full p-6 mb-4">
                        <i class="fas fa-trophy text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No completed challenges yet</h3>
                    <p class="text-gray-500 mb-6">When users complete challenges, they will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-6">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div class="text-xl font-bold">NutriTrack Admin</div>
                    <div class="text-gray-400 text-sm">Version 1.0.0</div>
                </div>
                
            </div>
            <div class="mt-6 border-t border-gray-700 pt-4 text-sm text-gray-400 text-center">
                <p>&copy; 2025 NutriTrack Admin Panel. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const value = this.value.toLowerCase();
                const rows = document.querySelectorAll('#completionsTable tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.indexOf(value) > -1) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    </script>
</body>
</html>
