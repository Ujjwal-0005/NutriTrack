<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$sql_users = "SELECT COUNT(*) as total_users FROM users";
$result_users = $conn->query($sql_users);
$total_users = $result_users->fetch_assoc()['total_users'];

$sql_foods = "SELECT COUNT(*) as total_foods FROM foods";
$result_foods = $conn->query($sql_foods);
$total_foods = $result_foods->fetch_assoc()['total_foods'];

$sql_workouts = "SELECT COUNT(*) as total_workouts FROM workouts";
$result_workouts = $conn->query($sql_workouts);
$total_workouts = $result_workouts->fetch_assoc()['total_workouts'];

$sql_recent_users = "SELECT * FROM users ORDER BY id DESC LIMIT 5";
$result_recent_users = $conn->query($sql_recent_users);

$sql_recent_activities = "
    (SELECT 'food' as type, f.id, f.food_name as activity_name, f.calories, f.email, f.date, u.firstname, u.lastname
     FROM foods f
     JOIN users u ON f.email = u.email
     ORDER BY f.date DESC, f.id DESC
     LIMIT 5)
    UNION ALL
    (SELECT 'workout' as type, w.id, w.type as activity_name, w.calories, w.email, w.date, u.firstname, u.lastname
     FROM workouts w
     JOIN users u ON w.email = u.email
     ORDER BY w.date DESC, w.id DESC
     LIMIT 5)
    ORDER BY date DESC, id DESC
    LIMIT 10
";
$result_recent_activities = $conn->query($sql_recent_activities);

$server_info = $_SERVER['SERVER_SOFTWARE'];
$php_version = phpversion();
$mysql_version = $conn->server_info;
$max_upload = ini_get('upload_max_filesize');
$max_post = ini_get('post_max_size');
$memory_limit = ini_get('memory_limit');

$sql_monthly_users = "SELECT 
    MONTH(created_at) as month, 
    COUNT(*) as count 
FROM users 
WHERE YEAR(created_at) = YEAR(CURRENT_DATE()) 
GROUP BY MONTH(created_at) 
ORDER BY month";
$result_monthly_users = $conn->query($sql_monthly_users);

$monthly_users = array_fill(1, 12, 0); // Initialize with zeros
while ($row = $result_monthly_users->fetch_assoc()) {
    $monthly_users[$row['month']] = $row['count'];
}

$sql_monthly_foods = "SELECT 
    MONTH(date) as month, 
    COUNT(*) as count 
FROM foods 
WHERE YEAR(date) = YEAR(CURRENT_DATE()) 
GROUP BY MONTH(date) 
ORDER BY month";
$result_monthly_foods = $conn->query($sql_monthly_foods);

$monthly_foods = array_fill(1, 12, 0);
while ($row = $result_monthly_foods->fetch_assoc()) {
    $monthly_foods[$row['month']] = $row['count'];
}

$sql_monthly_workouts = "SELECT 
    MONTH(date) as month, 
    COUNT(*) as count 
FROM workouts 
WHERE YEAR(date) = YEAR(CURRENT_DATE()) 
GROUP BY MONTH(date) 
ORDER BY month";
$result_monthly_workouts = $conn->query($sql_monthly_workouts);

$monthly_workouts = array_fill(1, 12, 0); // Initialize with zeros
while ($row = $result_monthly_workouts->fetch_assoc()) {
    $monthly_workouts[$row['month']] = $row['count'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | NutriTrack 2025</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <a href="dashboard.php" class="py-2 px-3 bg-admin rounded-md text-white">Dashboard</a>
                    <a href="users.php" class="py-2 px-3 hover:bg-gray-700 rounded-md">Users</a>
                    <a href="foods.php" class="py-2 px-3 hover:bg-gray-700 rounded-md">Food Entries</a>
                    <a href="workouts.php" class="py-2 px-3 hover:bg-gray-700 rounded-md">Workout Entries</a>
                    <a href="settings.php" class="py-2 px-3 hover:bg-gray-700 rounded-md">Settings</a>
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
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
                <p class="text-gray-600 mt-1">Welcome back, <?php echo $_SESSION['firstname']; ?>. Here's what's happening today.</p>
            </div>
            <div class="flex space-x-3">
                <button class="bg-admin hover:bg-pink-800 text-white px-4 py-2 rounded-md flex items-center text-sm">
                    <i class="fas fa-download mr-2"></i> Export Data
                </button>
                <button class="bg-dark hover:bg-gray-800 text-white px-4 py-2 rounded-md flex items-center text-sm">
                    <i class="fas fa-cog mr-2"></i> Settings
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="rounded-full bg-blue-100 p-3 mr-4">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">Total Users</h3>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $total_users; ?></p>
                    <span class="text-green-500 text-xs font-medium flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i> 12% increase
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="rounded-full bg-purple-100 p-3 mr-4">
                    <i class="fas fa-utensils text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">Food Entries</h3>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $total_foods; ?></p>
                    <span class="text-green-500 text-xs font-medium flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i> 8% increase
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="rounded-full bg-green-100 p-3 mr-4">
                    <i class="fas fa-dumbbell text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">Workout Entries</h3>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $total_workouts; ?></p>
                    <span class="text-green-500 text-xs font-medium flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i> 15% increase
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="rounded-full bg-yellow-100 p-3 mr-4">
                    <i class="fas fa-server text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">System Status</h3>
                    <p class="text-2xl font-bold text-gray-800">Healthy</p>
                    <span class="text-green-500 text-xs font-medium flex items-center mt-1">
                        <i class="fas fa-check-circle mr-1"></i> All systems normal
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts & Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Chart Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- User Activity Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">User Activity</h2>
                        <div class="flex space-x-2">
                            <button class="text-xs bg-admin text-white px-3 py-1 rounded-md">Month</button>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="userActivityChart"></canvas>
                    </div>
                </div>

                <!-- Recent Activities Table -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Recent Activities</h2>
                        <a href="activities.php" class="text-admin hover:underline text-sm font-medium">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calories</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while ($row = $result_recent_activities->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-xs font-medium"><?php echo substr($row['firstname'], 0, 1) . substr($row['lastname'], 0, 1); ?></span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($row['activity_name']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($row['type'] == 'food'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Food</span>
                                        <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Workout</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $row['calories']; ?> kcal
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M d, Y', strtotime($row['date'])); ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <div class="space-y-8">
                <!-- New Users -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">New Users</h2>
                        <a href="users.php" class="text-admin hover:underline text-sm font-medium">View All</a>
                    </div>
                    <div class="space-y-5">
                        <?php while ($user = $result_recent_users->fetch_assoc()): ?>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                <span class="font-semibold text-gray-600"><?php echo substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1); ?></span>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h3>
                                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <div class="flex space-x-2">
                                <button class="text-gray-400 hover:text-admin">
                                    <i class="fas fa-envelope"></i>
                                </button>
                                <button class="text-gray-400 hover:text-admin">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- System Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">System Information</h2>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Server</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($server_info); ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">PHP Version</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($php_version); ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">MySQL Version</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($mysql_version); ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Max Upload</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($max_upload); ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Memory Limit</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($memory_limit); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h2>
                    <div class="space-y-3">
                        <a href="users.php" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-md transition">
                            <div class="rounded-full bg-blue-100 h-8 w-8 flex items-center justify-center mr-3">
                                <i class="fas fa-user-plus text-blue-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-800">Manage Users</span>
                        </a>
                        <a href="backup.php" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-md transition">
                            <div class="rounded-full bg-green-100 h-8 w-8 flex items-center justify-center mr-3">
                                <i class="fas fa-database text-green-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-800">Backup Database</span>
                        </a>
                        <a href="logs.php" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-md transition">
                            <div class="rounded-full bg-yellow-100 h-8 w-8 flex items-center justify-center mr-3">
                                <i class="fas fa-file-alt text-yellow-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-800">View System Logs</span>
                        </a>
                        <a href="settings.php" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-md transition">
                            <div class="rounded-full bg-purple-100 h-8 w-8 flex items-center justify-center mr-3">
                                <i class="fas fa-cog text-purple-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-800">System Settings</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-6">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div class="text-xl font-bold">NutriTrack<span class="text-admin">2025</span> Admin</div>
                    <div class="text-gray-400 text-sm">Version 1.0.0</div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-question-circle"></i> Help
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-shield-alt"></i> Privacy
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-file-contract"></i> Terms
                    </a>
                </div>
            </div>
            <div class="mt-6 border-t border-gray-700 pt-4 text-sm text-gray-400 text-center">
                <p>&copy; 2025 NutriTrack Admin Panel. All rights reserved.</p>
            </div>
        </div>
    </footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('userActivityChart').getContext('2d');

        const newUsersGradient = ctx.createLinearGradient(0, 0, 0, 400);
        newUsersGradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
        newUsersGradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');
        
        const foodEntriesGradient = ctx.createLinearGradient(0, 0, 0, 400);
        foodEntriesGradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        foodEntriesGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
        
        const workoutEntriesGradient = ctx.createLinearGradient(0, 0, 0, 400);
        workoutEntriesGradient.addColorStop(0, 'rgba(190, 24, 93, 0.4)');
        workoutEntriesGradient.addColorStop(1, 'rgba(190, 24, 93, 0.0)');
        
        const userActivityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'New Users',
                        data: <?php echo json_encode(array_values($monthly_users)); ?>,
                        backgroundColor: newUsersGradient,
                        borderColor: '#4F46E5',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#4F46E5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Food Entries',
                        data: <?php echo json_encode(array_values($monthly_foods)); ?>,
                        backgroundColor: foodEntriesGradient,
                        borderColor: '#10B981',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Workout Entries',
                        data: <?php echo json_encode(array_values($monthly_workouts)); ?>,
                        backgroundColor: workoutEntriesGradient,
                        borderColor: '#BE185D',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#BE185D',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#111827',
                        bodyColor: '#111827',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 4],
                            color: '#e5e7eb'
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                elements: {
                    line: {
                        tension: 0.4
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });
    });
</script>
</body>
</html>