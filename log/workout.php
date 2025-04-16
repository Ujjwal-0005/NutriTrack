<?php
session_start();
include "../db.php";

// Check if workout table exists, create if not
function ensureWorkoutTableExists($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS workouts (
        id VARCHAR(32) PRIMARY KEY,
        date DATE NOT NULL,
        type VARCHAR(50) NOT NULL,
        duration INT NOT NULL,
        calories INT NOT NULL DEFAULT 0,
        intensity VARCHAR(20) NOT NULL,
        notes TEXT,
        timestamp INT NOT NULL
    )";
    
    if (!$conn->query($sql)) {
        die("Error creating table: " . $conn->error);
    }
}

ensureWorkoutTableExists($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = uniqid();
    $date = $_POST['date'];
    $type = $_POST['type'];
    $duration = $_POST['duration'];
    $calories = $_POST['calories'] ?? 0;
    $intensity = $_POST['intensity'];
    $notes = $_POST['notes'] ?? '';
    $timestamp = time();

    $sql = "INSERT INTO workouts (id, date, type, duration, calories, intensity, notes, timestamp) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisisi", $id, $date, $type, $duration, $calories, $intensity, $notes, $timestamp);
    
    if (!$stmt->execute()) {
        die("Error saving workout: " . $stmt->error);
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $idToDelete = $_GET['delete'];
    
    $sql = "DELETE FROM workouts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $idToDelete);
    
    if (!$stmt->execute()) {
        die("Error deleting workout: " . $stmt->error);
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$workouts = [];
$result = $conn->query("SELECT * FROM workouts ORDER BY date DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $workouts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriTrack 2025</title>
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
                    },
                    fontFamily: {
                        sans: ['Inter var', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="bg-gradient-to-r from-primary to-indigo-800 text-white">
        <?php include '../nav_dashboard.php'; ?>
        
        <div class="container mx-auto px-6 py-16">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Workout Tracker</h1>
            <p class="text-xl opacity-90 max-w-2xl">Track your fitness journey with our advanced workout logging system. Stay consistent and see your progress over time.</p>
        </div>
    </div>

    <div class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-plus-circle text-secondary mr-2"></i> Add Workout
                    </h2>
                    <form method="POST" action="" class="space-y-4">
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" id="date" name="date" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Workout Type</label>
                            <select id="type" name="type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Select Type</option>
                                <option value="Running">Running</option>
                                <option value="Walking">Walking</option>
                                <option value="Cycling">Cycling</option>
                                <option value="Swimming">Swimming</option>
                                <option value="Weight Training">Weight Training</option>
                                <option value="HIIT">HIIT</option>
                                <option value="Yoga">Yoga</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                            <input type="number" id="duration" name="duration" min="1" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label for="calories" class="block text-sm font-medium text-gray-700 mb-1">Calories Burned</label>
                            <input type="number" id="calories" name="calories" min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label for="intensity" class="block text-sm font-medium text-gray-700 mb-1">Intensity</label>
                            <select id="intensity" name="intensity" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Very High">Very High</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea id="notes" name="notes" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-indigo-600 transition-colors duration-300 flex justify-center items-center">
                            <i class="fas fa-save mr-2"></i> Save Workout
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-dumbbell text-primary mr-2"></i> Your Workouts
                        </h2>
                        <div class="relative">
                            <input type="text" placeholder="Search workouts..." class="px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Type</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Duration</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Intensity</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($workouts) > 0): ?>
                                    <?php foreach ($workouts as $workout): ?>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="px-4 py-4 text-gray-700"><?= htmlspecialchars($workout['date']) ?></td>
                                            <td class="px-4 py-4">
                                                <span class="px-3 py-1 rounded-full text-xs font-medium 
                                                    <?php
                                                    switch($workout['type']) {
                                                        case 'Running': echo 'bg-blue-100 text-blue-800'; break;
                                                        case 'Walking': echo 'bg-green-100 text-green-800'; break;
                                                        case 'Cycling': echo 'bg-indigo-100 text-indigo-800'; break;
                                                        case 'Swimming': echo 'bg-cyan-100 text-cyan-800'; break;
                                                        case 'Weight Training': echo 'bg-red-100 text-red-800'; break;
                                                        case 'HIIT': echo 'bg-orange-100 text-orange-800'; break;
                                                        case 'Yoga': echo 'bg-purple-100 text-purple-800'; break;
                                                        default: echo 'bg-gray-100 text-gray-800';
                                                    }
                                                    ?>
                                                ">
                                                    <?= htmlspecialchars($workout['type']) ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-gray-700"><?= htmlspecialchars($workout['duration']) ?> min</td>
                                            <td class="px-4 py-4">
                                                <span class="px-3 py-1 rounded-full text-xs font-medium 
                                                    <?php
                                                    switch($workout['intensity']) {
                                                        case 'Low': echo 'bg-green-100 text-green-800'; break;
                                                        case 'Medium': echo 'bg-yellow-100 text-yellow-800'; break;
                                                        case 'High': echo 'bg-orange-100 text-orange-800'; break;
                                                        case 'Very High': echo 'bg-red-100 text-red-800'; break;
                                                        default: echo 'bg-gray-100 text-gray-800';
                                                    }
                                                    ?>
                                                ">
                                                    <?= htmlspecialchars($workout['intensity']) ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex space-x-2">
                                                    <button class="text-gray-500 hover:text-primary" title="View details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="text-gray-500 hover:text-secondary" title="Edit workout">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="?delete=<?= $workout['id'] ?>" class="text-gray-500 hover:text-red-500" 
                                                       title="Delete workout" 
                                                       onclick="return confirm('Are you sure you want to delete this workout?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No workouts found. Start by adding your first workout!</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (count($workouts) > 0): ?>
                    <div class="mt-6 flex justify-between items-center text-sm text-gray-600">
                        <div>Showing <?= count($workouts) ?> workouts</div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Previous</button>
                            <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Next</button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="bg-dark text-white mt-16">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="text-2xl font-bold mb-4">FitTrack<span class="text-secondary">2025</span></div>
                    <p class="text-gray-400 mb-4">Your ultimate companion for tracking fitness progress and achieving your health goals.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Home</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">About</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Features</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start"><i class="fas fa-map-marker-alt mt-1 mr-2"></i> 123 Fitness Street, Gym City</li>
                        <li class="flex items-start"><i class="fas fa-phone mt-1 mr-2"></i> +1 (555) 123-4567</li>
                        <li class="flex items-start"><i class="fas fa-envelope mt-1 mr-2"></i> info@fittrack2025.com</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 FitTrack. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
