<?php
session_start();
include '../db.php'; // Include the database connection file

if (!isset($_SESSION['email']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all workout logs
$sql_all_workouts = "SELECT w.id, w.type, w.calories, w.email, w.date, u.firstname, u.lastname 
                     FROM workouts w 
                     JOIN users u ON w.email = u.email 
                     ORDER BY w.date DESC, w.id DESC";
$result_all_workouts = $conn->query($sql_all_workouts);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Logs | Admin Panel</title>
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
    <nav class="bg-dark text-white shadow-md">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <span class="text-2xl font-bold mr-4">
                    <span class="text-admin">Admin</span>Panel
                </span>
                <div class="hidden md:flex space-x-6 font-medium text-sm">
                    <a href="dashboard.php" class="py-2 px-3 hover:bg-gray-700 rounded-md">Dashboard</a>
                    <a href="users.php" class="py-2 px-3 hover:bg-gray-700 rounded-md">Users</a>
                    <a href="foods.php" class="py-2 px-3 hover:bg-gray-700 rounded-md">Food Entries</a>
                    <a href="workouts.php" class="py-2 px-3 bg-admin rounded-md text-white">Workout Entries</a>
                    <a href="challenges.php" class="py-2 px-3 hover:bg-gray-700 rounded-md">Challenges</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="../dashboard/user.php" class="text-sm hover:underline">
                    <i class="fas fa-home mr-1"></i> User Dashboard
                </a>
                <div class="relative group">
                    <button class="flex items-center space-x-2">
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
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Workout Log Entries</h1>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workout Type</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calories</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($workout = $result_all_workouts->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($workout['firstname'] . ' ' . $workout['lastname']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($workout['email']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($workout['type']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $workout['calories']; ?> kcal
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($workout['date'])); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>