<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: challenges.php");
    exit();
}

$challenge_id = $_GET['id'];

// Fetch the challenge details
$sql = "SELECT * FROM challenges WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $challenge_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: challenges.php");
    exit();
}

$challenge = $result->fetch_assoc();

// Initialize message variables
$message = '';
$messageType = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $goal = mysqli_real_escape_string($conn, $_POST['goal']);
    $points = (int)$_POST['points'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $difficulty = mysqli_real_escape_string($conn, $_POST['difficulty']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $badge_name = mysqli_real_escape_string($conn, $_POST['badge_name']);
    
    // Handle badge image upload if a new one was provided
    $badge_image = $challenge['badge_image']; // Default to existing image
    
    if (isset($_FILES['badge_image']) && $_FILES['badge_image']['error'] === 0) {
        $target_dir = "../uploads/badges/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['badge_image']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES['badge_image']['tmp_name'], $target_file)) {
                // If successful upload, delete the old image if it exists
                if ($badge_image && file_exists($target_dir . $badge_image)) {
                    unlink($target_dir . $badge_image);
                }
                $badge_image = $new_filename;
            } else {
                $message = "Error uploading badge image.";
                $messageType = "error";
            }
        } else {
            $message = "Invalid file type. Allowed types: JPG, JPEG, PNG, GIF, SVG.";
            $messageType = "error";
        }
    }
    
    // Update the challenge if no upload errors
    if (empty($message)) {
        $update_sql = "UPDATE challenges SET 
                      title = ?, 
                      description = ?, 
                      goal = ?, 
                      points = ?,
                      start_date = ?, 
                      end_date = ?, 
                      difficulty = ?,
                      category = ?,
                      badge_name = ?,
                      badge_image = ?,
                      WHERE id = ?";
        
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssississssi", 
            $title, 
            $description, 
            $goal, 
            $points, 
            $start_date, 
            $end_date, 
            $difficulty, 
            $category, 
            $badge_name, 
            $badge_image, 
            $challenge_id
        );
        
        if ($update_stmt->execute()) {
            $message = "Challenge updated successfully!";
            $messageType = "success";
            
            // Refresh challenge data after update
            $stmt->execute();
            $result = $stmt->get_result();
            $challenge = $result->fetch_assoc();
        } else {
            $message = "Error updating challenge: " . $conn->error;
            $messageType = "error";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Challenge | NutriTrack 2025</title>
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
                    <span class="text-admin">Admin</span>Panel
                </span>
                <div class="hidden md:flex space-x-6 font-medium text-sm">
                    <a href="dashboard.php" class="py-2 px-3 hover:bg-orange-100 hover:text-orange-900 font-semibold rounded-md">Dashboard</a>
                    <a href="users.php" class="py-2 px-3 hover:bg-orange-100 hover:text-orange-900 font-semibold rounded-md">Users</a>
                    <a href="foods.php" class="py-2 px-3 hover:bg-orange-100 hover:text-orange-900  font-semibold rounded-md">Food Entries</a>
                    <a href="workouts.php" class="py-2 px-3 hover:bg-orange-100 hover:text-orange-900 font-semibold rounded-md">Workout Entries</a>
                    <a href="challenges.php" class="py-2 px-3 bg-orange-700 hover:bg-orange-100 font-semibold hover:text-orange-900 rounded-md text-white">Challenges</a>
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
                <h1 class="text-3xl font-bold text-gray-800">Edit Challenge</h1>
                <p class="text-gray-600 mt-1">Update the details of the fitness challenge</p>
            </div>
            <a href="challenges.php" class="bg-admin hover:bg-pink-800 text-white font-medium py-2 px-4 rounded-md transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Challenges
            </a>
        </div>

        <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 rounded-md <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Edit Challenge Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="edit_challenge.php?id=<?php echo $challenge_id; ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Challenge Title</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($challenge['title']); ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                    </div>
                    
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select id="category" name="category" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                            <option value="cardio" <?php echo $challenge['category'] === 'cardio' ? 'selected' : ''; ?>>Cardio</option>
                            <option value="strength" <?php echo $challenge['category'] === 'strength' ? 'selected' : ''; ?>>Strength</option>
                            <option value="flexibility" <?php echo $challenge['category'] === 'flexibility' ? 'selected' : ''; ?>>Flexibility</option>
                            <option value="nutrition" <?php echo $challenge['category'] === 'nutrition' ? 'selected' : ''; ?>>Nutrition</option>
                            <option value="wellness" <?php echo $challenge['category'] === 'wellness' ? 'selected' : ''; ?>>Wellness</option>
                            <option value="weight_loss" <?php echo $challenge['category'] === 'weight_loss' ? 'selected' : ''; ?>>Weight Loss</option>
                            <option value="hydration" <?php echo $challenge['category'] === 'hydration' ? 'selected' : ''; ?>>Hydration</option>
                            <option value="step_count" <?php echo $challenge['category'] === 'step_count' ? 'selected' : ''; ?>>Step Count</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin"><?php echo htmlspecialchars($challenge['description']); ?></textarea>
                    </div>
                    
                    <div>
                        <label for="goal" class="block text-sm font-medium text-gray-700 mb-1">Challenge Goal</label>
                        <input type="text" id="goal" name="goal" value="<?php echo htmlspecialchars($challenge['goal']); ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                        <p class="text-xs text-gray-500 mt-1">E.g., "Run 30 miles in total" or "Log 5 workouts per week"</p>
                    </div>
                    
                    <div>
                        <label for="points" class="block text-sm font-medium text-gray-700 mb-1">Points Reward</label>
                        <input type="number" id="points" name="points" min="1" value="<?php echo htmlspecialchars($challenge['points']); ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                    </div>
                    
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($challenge['start_date']); ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($challenge['end_date']); ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                    </div>
                    
                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-1">Difficulty Level</label>
                        <select id="difficulty" name="difficulty" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                            <option value="beginner" <?php echo $challenge['difficulty'] === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                            <option value="intermediate" <?php echo $challenge['difficulty'] === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="advanced" <?php echo $challenge['difficulty'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                            <option value="expert" <?php echo $challenge['difficulty'] === 'expert' ? 'selected' : ''; ?>>Expert</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="badge_name" class="block text-sm font-medium text-gray-700 mb-1">Badge Name</label>
                        <input type="text" id="badge_name" name="badge_name" value="<?php echo htmlspecialchars($challenge['badge_name']); ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                    </div>
                    
                    <div>
                        <label for="badge_image" class="block text-sm font-medium text-gray-700 mb-1">Badge Image</label>
                        <div class="flex items-center space-x-3">
                            <?php if(!empty($challenge['badge_image']) && file_exists("../uploads/badges/" . $challenge['badge_image'])): ?>
                                <div class="w-16 h-16 rounded-md border border-gray-300 flex items-center justify-center overflow-hidden">
                                    <img src="../uploads/badges/<?php echo $challenge['badge_image']; ?>" alt="Current Badge" class="max-w-full max-h-full">
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex-1">
                                <label class="block">
                                    <span class="sr-only">Choose new badge image</span>
                                    <input type="file" id="badge_image" name="badge_image" accept="image/*"
                                        class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-medium
                                        file:bg-admin file:text-white
                                        hover:file:bg-pink-800">
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image. Recommended size: 128x128 pixels</p>
                            </div>
                            
                            <div id="image-preview" class="w-16 h-16 rounded-md border border-gray-300 hidden flex-shrink-0"></div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="challenges.php" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-admin text-white rounded-md hover:bg-pink-800">
                        Update Challenge
                    </button>
                </div>
            </form>
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
        // Date validation
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            
            startDateInput.addEventListener('change', function() {
                // Ensure end date is not before start date
                if (endDateInput.value && new Date(startDateInput.value) > new Date(endDateInput.value)) {
                    endDateInput.value = startDateInput.value;
                }
                // Set min date for end date input
                endDateInput.min = startDateInput.value;
            });
            
            // Set initial min date for end date
            if (startDateInput.value) {
                endDateInput.min = startDateInput.value;
            }
            
            // Image Preview
            const fileInput = document.getElementById('badge_image');
            const imagePreview = document.getElementById('image-preview');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">`;
                            imagePreview.classList.remove('hidden');
                            imagePreview.classList.add('flex');
                        }
                        
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
        });
    </script>
</body>
</html>
