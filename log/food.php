<?php
session_start();
include "../db.php";

function ensureFoodTableExists($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS foods (
        id VARCHAR(32) PRIMARY KEY,
        date DATE NOT NULL,
        meal_type VARCHAR(50) NOT NULL,
        food_name VARCHAR(100) NOT NULL,
        calories INT NOT NULL DEFAULT 0,
        protein FLOAT NOT NULL DEFAULT 0,
        carbs FLOAT NOT NULL DEFAULT 0,
        fat FLOAT NOT NULL DEFAULT 0,
        notes TEXT,
        timestamp INT NOT NULL,
        email VARCHAR(100) NOT NULL,
        FOREIGN KEY (email) REFERENCES users(email) ON DELETE CASCADE
    )";
    
    if (!$conn->query($sql)) {
        die("Error creating table: " . $conn->error);
    }
}

ensureFoodTableExists($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = uniqid();
    $date = $_POST['date'];
    $meal_type = $_POST['meal_type'];
    $food_name = $_POST['food_name'];
    $calories = $_POST['calories'] ?? 0;
    $protein = $_POST['protein'] ?? 0;
    $carbs = $_POST['carbs'] ?? 0;
    $fat = $_POST['fat'] ?? 0;
    $notes = $_POST['notes'] ?? '';
    $timestamp = time();

    $sql = "INSERT INTO foods (id, date, meal_type, food_name, calories, protein, carbs, fat, notes, timestamp, email)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiddssis", $id, $date, $meal_type, $food_name, $calories, $protein, $carbs, $fat, $notes, $timestamp, $_SESSION['email']);
    
    if (!$stmt->execute()) {
        die("Error saving food: " . $stmt->error);
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $idToDelete = $_GET['delete'];
    
    $sql = "DELETE FROM foods WHERE id = ? AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $idToDelete, $_SESSION['email']);
    
    if (!$stmt->execute()) {
        die("Error deleting food: " . $stmt->error);
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$foods = [];
$sql = "SELECT * FROM foods WHERE email = ? ORDER BY date DESC, timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $foods[] = $row;
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
    <style>
        .das{
            background: rgb(0,51,102);
background: linear-gradient(159deg, rgba(0,51,102,1) 0%, rgba(15,82,186,1) 100%);
        }
        </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="das text-white">
        <?php include '../nav_dashboard.php'; ?>
        
        <div class="container mx-auto px-6 py-16">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Food Tracker</h1>
            <p class="text-xl opacity-90 max-w-2xl">Track your nutrition journey with our advanced food logging system. Monitor your daily intake and maintain a balanced diet.</p>
        </div>
    </div>

    <div class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-utensils text-secondary mr-2"></i> Add Food
                    </h2>
                    <form method="POST" action="" class="space-y-4">
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" id="date" name="date" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label for="meal_type" class="block text-sm font-medium text-gray-700 mb-1">Meal Type</label>
                            <select id="meal_type" name="meal_type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Select Meal</option>
                                <option value="Breakfast">Breakfast</option>
                                <option value="Lunch">Lunch</option>
                                <option value="Dinner">Dinner</option>
                                <option value="Snack">Snack</option>
                                <option value="Dessert">Dessert</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="food_name" class="block text-sm font-medium text-gray-700 mb-1">Food Name</label>
                            <input type="text" id="food_name" name="food_name" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label for="calories" class="block text-sm font-medium text-gray-700 mb-1">Calories</label>
                            <input type="number" id="calories" name="calories" min="0" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="protein" class="block text-sm font-medium text-gray-700 mb-1">Protein (g)</label>
                                <input type="number" step="0.1" id="protein" name="protein" min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>
                            <div>
                                <label for="carbs" class="block text-sm font-medium text-gray-700 mb-1">Carbs (g)</label>
                                <input type="number" step="0.1" id="carbs" name="carbs" min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>
                            <div>
                                <label for="fat" class="block text-sm font-medium text-gray-700 mb-1">Fat (g)</label>
                                <input type="number" step="0.1" id="fat" name="fat" min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea id="notes" name="notes" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-indigo-600 transition-colors duration-300 flex justify-center items-center">
                            <i class="fas fa-save mr-2"></i> Save Food Entry
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-clipboard-list text-primary mr-2"></i> Your Food Log
                        </h2>
                        <div class="relative">
                            <input type="text" placeholder="Search foods..." class="px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Meal</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Food</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Calories</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($foods) > 0): ?>
                                    <?php foreach ($foods as $food): ?>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="px-4 py-4 text-gray-700"><?= htmlspecialchars($food['date']) ?></td>
                                            <td class="px-4 py-4">
                                                <span class="px-3 py-1 rounded-full text-xs font-medium 
                                                    <?php
                                                    switch($food['meal_type']) {
                                                        case 'Breakfast': echo 'bg-yellow-100 text-yellow-800'; break;
                                                        case 'Lunch': echo 'bg-green-100 text-green-800'; break;
                                                        case 'Dinner': echo 'bg-blue-100 text-blue-800'; break;
                                                        case 'Snack': echo 'bg-purple-100 text-purple-800'; break;
                                                        case 'Dessert': echo 'bg-pink-100 text-pink-800'; break;
                                                        default: echo 'bg-gray-100 text-gray-800';
                                                    }
                                                    ?>
                                                ">
                                                    <?= htmlspecialchars($food['meal_type']) ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-gray-700"><?= htmlspecialchars($food['food_name']) ?></td>
                                            <td class="px-4 py-4 text-gray-700"><?= htmlspecialchars($food['calories']) ?> kcal</td>
                                            <td class="px-4 py-4">
                                                <div class="flex space-x-2">
                                                   
                                                    <a href="?delete=<?= $food['id'] ?>" class="text-gray-500 hover:text-red-500" 
                                                       title="Delete food" 
                                                       onclick="return confirm('Are you sure you want to delete this food entry?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No food entries found. Start by adding your first meal!</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (count($foods) > 0): ?>
                    <div class="mt-6 flex justify-between items-center text-sm text-gray-600">
                        <div>Showing <?= count($foods) ?> food entries</div>
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
        <?php include '../footer.php'; ?>
    </footer>
</body>
</html>
