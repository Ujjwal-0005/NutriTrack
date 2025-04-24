<?php
session_start();
include '../db.php';
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$today = date('Y-m-d');
$email = $_SESSION['email'];

function ensureWorkoutTableExists($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS workouts (
        id VARCHAR(32) PRIMARY KEY,
        date DATE NOT NULL,
        type VARCHAR(50) NOT NULL,
        duration INT NOT NULL,
        calories INT NOT NULL DEFAULT 0,
        intensity VARCHAR(20) NOT NULL,
        notes TEXT,
        timestamp INT NOT NULL,
        email VARCHAR(255) NOT NULL,
        FOREIGN KEY (email) REFERENCES users(email) ON DELETE CASCADE
    )";
    
    if (!$conn->query($sql)) {
        die("Error creating table: " . $conn->error);
    }
}

ensureWorkoutTableExists($conn);

$sql_calories_consumed = "SELECT COALESCE(SUM(calories), 0) as total_calories_consumed
                         FROM foods 
                         WHERE date = ? AND email = ?";
$stmt = $conn->prepare($sql_calories_consumed);
$stmt->bind_param("ss", $today, $email);
$stmt->execute();
$result = $stmt->get_result();
$calories_consumed = $result->fetch_assoc()['total_calories_consumed'];
$stmt->close();

$sql_calories_burned = "SELECT COALESCE(SUM(calories), 0) as total_calories_burned
                       FROM workouts
                       WHERE date = ? AND email = ?";
$stmt = $conn->prepare($sql_calories_burned);
$stmt->bind_param("ss", $today, $email);
$stmt->execute();
$result = $stmt->get_result();
$calories_burned = $result->fetch_assoc()['total_calories_burned'];
$stmt->close();

$first_day_of_month = date('Y-m-01');
$last_day_of_month = date('Y-m-t');

$sql_workout_time = "SELECT COALESCE(SUM(duration), 0) as total_workout_time
                    FROM workouts
                    WHERE date BETWEEN ? AND ? AND email = ?";
$stmt = $conn->prepare($sql_workout_time);
$stmt->bind_param("sss", $first_day_of_month, $last_day_of_month, $email);
$stmt->execute();
$result = $stmt->get_result();
$total_workout_time = $result->fetch_assoc()['total_workout_time'];
$stmt->close();

$start_date = date('Y-m-d', strtotime('-6 days'));
$end_date = $today;

$sql_last_seven_days = "SELECT 
    a.date,
    COALESCE(SUM(f.calories), 0) as calories_consumed,
    COALESCE(SUM(w.calories), 0) as calories_burned
FROM (
    SELECT CURDATE() - INTERVAL n DAY as date
    FROM (
        SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 
        UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
    ) numbers
) a
LEFT JOIN foods f ON a.date = f.date AND f.email = ?
LEFT JOIN workouts w ON a.date = w.date AND w.email = ?
WHERE a.date BETWEEN ? AND ?
GROUP BY a.date
ORDER BY a.date";

$stmt = $conn->prepare($sql_last_seven_days);
$stmt->bind_param("ssss", $email, $email, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$consumed_data = [];
$burned_data = [];
$net_data = [];

$total_consumed = 0;
$total_burned = 0;
$days_count = 0;

while ($row = $result->fetch_assoc()) {
    $date = date('M d', strtotime($row['date']));
    $labels[] = $date;
    $consumed_data[] = $row['calories_consumed'];
    $burned_data[] = $row['calories_burned'];
    $net_data[] = $row['calories_consumed'] - $row['calories_burned'];
    
    $total_consumed += $row['calories_consumed'];
    $total_burned += $row['calories_burned'];
    $days_count++;
}
$stmt->close();

$conn->close();

$total_workout_time_hours = floor($total_workout_time / 60);
$total_workout_time_minutes = $total_workout_time % 60;
$total_workout_time_formatted = sprintf("%02d:%02d", $total_workout_time_hours, $total_workout_time_minutes);
$total_calories_consumed_formatted = number_format($calories_consumed, 0, '.', ',');
$total_calories_burned_formatted = number_format($calories_burned, 0, '.', ',');

$avg_daily_consumed = $days_count > 0 ? round($total_consumed / $days_count) : 0;
$avg_daily_burned = $days_count > 0 ? round($total_burned / $days_count) : 0;
$net_balance = $avg_daily_consumed - $avg_daily_burned;
$net_balance_formatted = ($net_balance >= 0 ? '+' : '') . $net_balance;

$chart_labels = json_encode($labels);
$chart_consumed_data = json_encode($consumed_data);
$chart_burned_data = json_encode($burned_data);
$chart_net_data = json_encode($net_data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | NutriTrack 2025</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#10B981',
                        dark: '#111827',
                    },
                    fontFamily: {
                        sans: ['Inter var', 'sans-serif'],
                    },
                }
            }
        }
    </script>
     <style>
        .das{
            background: rgb(38, 94, 150);
background: linear-gradient(180deg, rgb(0,51,102) 50%, rgba(58, 176, 215) 100%);
        }
        </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="das text-white">
        <?php include '../nav_dashboard.php'; ?>
        <div class="container mx-auto px-6 py-16">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome, <?php echo $_SESSION['firstname']; ?> 👋</h1>
            <p class="text-xl opacity-90 max-w-2xl">Track your fitness progress and nutritional intake with our advanced dashboard. Stay on top of your health goals.</p>
        </div>
    </div>
<style>
    .new{
        background-color:rgb(174, 217, 232);
background-image: linear-gradient(180deg,rgb(60, 174, 212) 0%,rgb(232, 247, 250) 74%);

    }
    </style>
    <div class="container  new mx-auto pb-20 px-6 -mt-0">
        <div class="grid relative grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white -mt-11 rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-transform duration-300">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-700">Calories Consumed</h2>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Today</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-utensils text-4xl text-blue-500 mr-4"></i>
                    <div>
                        <p class="text-3xl font-bold text-blue-600"><?php echo $total_calories_consumed_formatted; ?></p>
                        <p class="text-sm text-gray-500">Calories</p>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-500">
                    <a href="../log/food.php" class="text-primary hover:underline flex items-center">
                        <span>Log your meals</span>
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white -mt-11 rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-transform duration-300">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-700">Calories Burned</h2>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Today</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-fire-flame-curved text-4xl text-green-500 mr-4"></i>
                    <div>
                        <p class="text-3xl font-bold text-green-600"><?php echo $total_calories_burned_formatted; ?></p>
                        <p class="text-sm text-gray-500">Calories</p>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-500">
                    <a href="../log/workout.php" class="text-primary hover:underline flex items-center">
                        <span>Log your workouts</span>
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white  -mt-11 rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-transform duration-300">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-700">Total Workout Time</h2>
                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">This Month</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-stopwatch text-4xl text-purple-500 mr-4"></i>
                    <div>
                        <p class="text-3xl font-bold text-purple-600"><?php echo $total_workout_time_formatted; ?></p>
                        <p class="text-sm text-gray-500">hours</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 t lg:grid-cols-3 gap-6 mt-8">
            <div class="lg:col-span-2 transform hover:scale-y-105 duration-300 transition-transform bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-chart-line text-primary mr-2"></i> Health Overview
                </h2>
                
                <div class="bg-gray-50 rounded-xl p-4 h-64">
                    <canvas id="calorieChart"></canvas>
                </div>
                
                <div class="mt-6 grid grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-xl text-center">
                        <p class="text-gray-600 text-sm">Average Daily</p>
                        <p class="font-bold text-blue-600"><?php echo number_format($avg_daily_consumed, 0); ?> cal</p>
                        <p class="text-xs text-gray-500">consumption</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-xl text-center">
                        <p class="text-gray-600 text-sm">Average Daily</p>
                        <p class="font-bold text-green-600"><?php echo number_format($avg_daily_burned, 0); ?> cal</p>
                        <p class="text-xs text-gray-500">burned</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-xl text-center">
                        <p class="text-gray-600 text-sm">Net Balance</p>
                        <p class="font-bold text-purple-600"><?php echo $net_balance_formatted; ?> cal</p>
                        <p class="text-xs text-gray-500">weekly avg.</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white transform hover:scale-105 duration-300 transition-transform rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-list-check text-secondary mr-2"></i> Quick Actions
                </h2>
                
                <div class="space-y-4">
                    <a href="../log/food.php" class="block bg-blue-50 hover:bg-blue-100 p-4 rounded-xl transition-colors">
                        <div class="flex items-center">
                            <div class="rounded-full bg-blue-100 p-3 mr-4">
                                <i class="fas fa-utensils text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Log Food Intake</h3>
                                <p class="text-sm text-gray-600">Record your meals and nutrition</p>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                        </div>
                    </a>
                    
                    <a href="../log/workout.php" class="block bg-green-50 hover:bg-green-100 p-4 rounded-xl transition-colors">
                        <div class="flex items-center">
                            <div class="rounded-full bg-green-100 p-3 mr-4">
                                <i class="fas fa-dumbbell text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Log Workout</h3>
                                <p class="text-sm text-gray-600">Track your exercise sessions</p>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                        </div>
                    </a>

                    <a href="../settings/user.php" class="block bg-gray-50 hover:bg-gray-100 p-4 rounded-xl transition-colors">
                        <div class="flex items-center">
                            <div class="rounded-full bg-gray-200 p-3 mr-4">
                                <i class="fas fa-gear text-gray-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Settings</h3>
                                <p class="text-sm text-gray-600">Update your profile and preferences</p>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                        </div>
                    </a>
                    
                    <a href="challenges.php" class="block bg-red-50 hover:bg-red-100 p-4 rounded-xl transition-colors">
                        <div class="flex items-center">
                            <div class="rounded-full bg-red-100 p-3 mr-4">
                                <i class="fas fa-sign-out-alt text-red-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Challenges</h3>
                                <p class="text-sm text-gray-600">Join and track your fitness challenges</p>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                        </div>
                    </a>

                </div>
            </div>
        </div>
    </div>
    <footer class="bg-gray-900 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                 
                <div class="space-y-4">
                    <h3 class="text-2xl font-bold text-red-500">NutriTrack</h3>
                    <p class="text-gray-300 text-sm">Elevate your fitness journey with premium nutrition supplements.</p>
                   
                </div>

               
                <div>
                    <h4 class="text-xl font-semibold mb-4 text-red-500">Quick Links</h4>
                    <ul class="space-y-2">
                    <li><a href="../index.php" class="text-gray-300 hover:text-white transition">Home</a></li>
                       
                       <li><a href="../read.php" class="text-gray-300 hover:text-white transition">About Us</a></li>
                       <li><a href="../contact.php" class="text-gray-300 hover:text-white transition">Contact</a></li>
                        <!-- <li><a href="#" class="text-gray-300 hover:text-white transition">Blog</a></li> -->
                    </ul>
                </div>

                
             

               
                <div>
                    <h4 class="text-xl font-semibold mb-4 text-red-500">Contact Info</h4>
                    <ul class="space-y-3">
                        <li class="flex items-center space-x-2">
                            <i class="ri-map-pin-line text-red-400"></i>
                            <span class="text-gray-300">123 Fitness Street, Muscle City</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="ri-phone-line text-red-400"></i>
                            <span class="text-gray-300">+1 (555) 123-4567</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="ri-mail-line text-red-400"></i>
                            <span class="text-gray-300">support@nutritrack.com</span>
                        </li>
                    </ul>
                </div>
            </div>

             
            <div class="mt-12 pt-6 border-t border-gray-700 text-center">
                <p class="text-gray-400">
                    © 2025 NutriTrack. All Rights Reserved. 
                    <span class="ml-4 text-sm">
                        <a href="#" class="hover:text-white transition">Privacy Policy</a> | 
                        <a href="#" class="hover:text-white transition">Terms of Service</a>
                    </span>
                </p>
            </div>
        </div>
    </footer>
   
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('calorieChart').getContext('2d');
            
            const labels = <?php echo $chart_labels; ?>;
            const consumedData = <?php echo $chart_consumed_data; ?>;
            const burnedData = <?php echo $chart_burned_data; ?>;
            const netData = <?php echo $chart_net_data; ?>;
            
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Calories Consumed',
                            data: consumedData,
                            backgroundColor: 'rgba(79, 70, 229, 0.7)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Calories Burned',
                            data: burnedData,
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1
                        },
                        {
                            type: 'line',
                            label: 'Net Balance',
                            data: netData,
                            backgroundColor: 'rgba(139, 92, 246, 0.5)',
                            borderColor: 'rgba(139, 92, 246, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(139, 92, 246, 1)',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: false,
                            tension: 0.1
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
                                font: {
                                    family: 'Inter var, sans-serif'
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleFont: {
                                family: 'Inter var, sans-serif',
                                size: 14
                            },
                            bodyFont: {
                                family: 'Inter var, sans-serif',
                                size: 13
                            },
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y + ' cal';
                                    }
                                    return label;
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
                                    family: 'Inter var, sans-serif'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            },
                            ticks: {
                                font: {
                                    family: 'Inter var, sans-serif'
                                },
                                callback: function(value) {
                                    return value + ' cal';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
