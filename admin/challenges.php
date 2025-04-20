<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Initialize variables for form processing
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $goal = mysqli_real_escape_string($conn, $_POST['goal']);
    $points = (int)$_POST['points'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $difficulty = mysqli_real_escape_string($conn, $_POST['difficulty']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $badge_name = mysqli_real_escape_string($conn, $_POST['badge_name']);
    
    // Check if a file was uploaded
    $badge_image = null;
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
    
    // Insert into database if no upload errors
    if (empty($message)) {
        $sql = "INSERT INTO challenges (title, description, goal, points, start_date, end_date, difficulty, category, badge_name, badge_image, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssississs", $title, $description, $goal, $points, $start_date, $end_date, $difficulty, $category, $badge_name, $badge_image);
        
        if ($stmt->execute()) {
            $message = "Challenge created successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $messageType = "error";
        }
        
        $stmt->close();
    }
}

// Check if the challenges table exists, if not create it
$table_check = $conn->query("SHOW TABLES LIKE 'challenges'");
if ($table_check->num_rows == 0) {
    // Create challenges table
    $create_table_sql = "CREATE TABLE challenges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        goal VARCHAR(255) NOT NULL,
        points INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        difficulty VARCHAR(50) NOT NULL,
        category VARCHAR(50) NOT NULL,
        badge_name VARCHAR(100) NOT NULL,
        badge_image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $conn->query($create_table_sql);
    $message = "Challenges table created successfully. You can now add challenges.";
    $messageType = "success";
}

// Fetch existing challenges for display
$sql = "SELECT * FROM challenges ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Challenges | NutriTrack 2025</title>
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
                    <a href="workouts.php" class="py-2 px-3 hover:bg-gray-700 rounded-md">Workout Entries</a>
                    <a href="challenges.php" class="py-2 px-3 bg-admin rounded-md text-white">Challenges</a>
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
                <h1 class="text-3xl font-bold text-gray-800">Manage Fitness Challenges</h1>
                <p class="text-gray-600 mt-1">Create and manage fitness challenges for your users</p>
            </div>
            <a href="completed_challenges.php" class="text-sm text-gray-600 hover:text-gray-800">
                <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg flex items-center">
                    <i class="fas fa-check-circle mr-2"></i> View Completed Challenges
                </button>
            </a>
            <button id="openFormBtn" class="bg-admin hover:bg-pink-800 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> New Challenge
            </button>
        </div>
        
        <!-- Alert Message (if any) -->
        <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 rounded-md <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Create Challenge Form Modal -->
        <div id="challengeFormModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-800">Create New Challenge</h3>
                        <button id="closeFormBtn" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <form action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Challenge Title</label>
                            <input type="text" id="title" name="title" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                        </div>
                        
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select id="category" name="category" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                                <option value="" disabled selected>Select category</option>
                                <option value="cardio">Cardio</option>
                                <option value="strength">Strength</option>
                                <option value="flexibility">Flexibility</option>
                                <option value="nutrition">Nutrition</option>
                                <option value="wellness">Wellness</option>
                                <option value="weight_loss">Weight Loss</option>
                                <option value="hydration">Hydration</option>
                                <option value="step_count">Step Count</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="3" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin"></textarea>
                        </div>
                        
                        <div>
                            <label for="goal" class="block text-sm font-medium text-gray-700 mb-1">Challenge Goal</label>
                            <input type="text" id="goal" name="goal" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                            <p class="text-xs text-gray-500 mt-1">E.g., "Run 30 miles in total" or "Log 5 workouts per week"</p>
                        </div>
                        
                        <div>
                            <label for="points" class="block text-sm font-medium text-gray-700 mb-1">Points Reward</label>
                            <input type="number" id="points" name="points" min="1" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                        </div>
                        
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" id="start_date" name="start_date" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                        </div>
                        
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" id="end_date" name="end_date" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                        </div>
                        
                        <div>
                            <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-1">Difficulty Level</label>
                            <select id="difficulty" name="difficulty" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="expert">Expert</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="badge_name" class="block text-sm font-medium text-gray-700 mb-1">Badge Name</label>
                            <input type="text" id="badge_name" name="badge_name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-admin">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="badge_image" class="block text-sm font-medium text-gray-700 mb-1">Badge Image</label>
                            <div class="flex items-center space-x-2">
                                <label class="block">
                                    <span class="sr-only">Choose badge image</span>
                                    <input type="file" id="badge_image" name="badge_image" accept="image/*"
                                        class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-medium
                                        file:bg-admin file:text-white
                                        hover:file:bg-pink-800">
                                </label>
                                <div id="image-preview" class="w-12 h-12 rounded-md border border-gray-300 hidden flex-shrink-0"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Recommended size: 128x128 pixels. Max file size: 2MB</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-admin text-white rounded-md hover:bg-pink-800">
                            Create Challenge
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Challenges List -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-5 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Active Challenges</h2>
            </div>
            
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="h-36 bg-gradient-to-r from-purple-500 to-pink-500 relative">
                                <?php if($row['badge_image']): ?>
                                    <div class="absolute -bottom-10 left-5 w-20 h-20 rounded-full border-4 border-white bg-white shadow-md flex items-center justify-center overflow-hidden">
                                        <img src="../uploads/badges/<?php echo $row['badge_image']; ?>" alt="Badge" class="max-w-full max-h-full">
                                    </div>
                                <?php else: ?>
                                    <div class="absolute -bottom-10 left-5 w-20 h-20 rounded-full border-4 border-white bg-gradient-to-r from-indigo-500 to-admin shadow-md flex items-center justify-center">
                                        <i class="fas fa-trophy text-white text-3xl"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute top-3 right-3 bg-white rounded-full py-1 px-3 text-xs font-medium text-gray-700">
                                    <?php echo ucfirst($row['difficulty']); ?>
                                </div>
                            </div>
                            
                            <div class="p-5 pt-12">
                                <div class="flex items-center mb-3">
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-2">
                                        <?php echo ucfirst($row['category']); ?>
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        <?php 
                                            $start_date = new DateTime($row['start_date']);
                                            $end_date = new DateTime($row['end_date']);
                                            echo $start_date->format('M d') . ' - ' . $end_date->format('M d, Y'); 
                                        ?>
                                    </span>
                                </div>
                                
                                <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3"><?php echo htmlspecialchars($row['description']); ?></p>
                                
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <div class="bg-yellow-100 text-yellow-800 rounded-full w-6 h-6 flex items-center justify-center mr-1">
                                            <i class="fas fa-star text-xs"></i>
                                        </div>
                                        <span class="text-sm font-medium"><?php echo $row['points']; ?> points</span>
                                    </div>
                                    
                                    <div class="flex space-x-1">
                                        <a href="edit_challenge.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_challenge.php?id=<?php echo $row['id']; ?>" 
                                           class="text-red-600 hover:text-red-800 delete-challenge"
                                           data-id="<?php echo $row['id']; ?>"
                                           data-title="<?php echo htmlspecialchars($row['title']); ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="p-6 text-center">
                    <div class="inline-flex bg-blue-100 rounded-full p-6 mb-4">
                        <i class="fas fa-trophy text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No challenges yet</h3>
                    <p class="text-gray-500 mb-6">Create your first challenge to motivate your users!</p>
                    <button id="createFirstChallengeBtn" class="bg-admin hover:bg-pink-800 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i> Create First Challenge
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-6 mt-12">
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

    <!-- Confirmation Dialog -->
    <div id="deleteConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Confirm Deletion</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to delete the challenge "<span id="challengeTitle"></span>"? This action cannot be undone.</p>
                <div class="flex justify-end space-x-3">
                    <button id="cancelDeleteBtn" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300">
                        Cancel
                    </button>
                    <a id="confirmDeleteBtn" href="#" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Delete Challenge
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal Control
        const openFormBtn = document.getElementById('openFormBtn');
        const closeFormBtn = document.getElementById('closeFormBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const challengeFormModal = document.getElementById('challengeFormModal');
        const createFirstChallengeBtn = document.getElementById('createFirstChallengeBtn');

        function openModal() {
            challengeFormModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            challengeFormModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        if (openFormBtn) openFormBtn.addEventListener('click', openModal);
        if (closeFormBtn) closeFormBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        if (createFirstChallengeBtn) createFirstChallengeBtn.addEventListener('click', openModal);

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

        // Delete Confirmation
        const deleteButtons = document.querySelectorAll('.delete-challenge');
        const deleteConfirmModal = document.getElementById('deleteConfirmModal');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const challengeTitleSpan = document.getElementById('challengeTitle');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                
                challengeTitleSpan.textContent = title;
                confirmDeleteBtn.href = `delete_challenge.php?id=${id}`;
                
                deleteConfirmModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        });
        
        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', function() {
                deleteConfirmModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            });
        }
    </script>
</body>
</html>