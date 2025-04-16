<?php
    session_start();
    include '../db.php';

    if (!isset($_SESSION['email'])) {
        header("Location: ../login.php");
        exit();
    }
    $email = $_SESSION['email'];
    $message = '';
    $messageType = '';
    
    // Fetch user data
    $stmt = $conn->prepare("SELECT firstname, lastname, email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header("Location: ../logout.php");
        exit();
    }
    
    $userData = $result->fetch_assoc();
    $stmt->close();
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstname = htmlspecialchars($_POST['firstname']);
        $lastname = htmlspecialchars($_POST['lastname']);
        $newEmail = htmlspecialchars($_POST['email']);
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (!password_verify($currentPassword, $user['password'])) {
            $message = "Current password is incorrect";
            $messageType = "error";
        } else {
            // Start transaction
            $conn->begin_transaction();
            try {
                // Update basic info
                $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ? WHERE email = ?");
                $stmt->bind_param("sss", $firstname, $lastname, $email);
                $stmt->execute();
                $stmt->close();
                               
                if (!empty($newPassword)) {
                    if ($newPassword !== $confirmPassword) {
                        throw new Exception("New passwords do not match");
                    }
                    
                    if (strlen($newPassword) < 8) {
                        throw new Exception("Password must be at least 8 characters long");
                    }
                    
                    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                    $stmt->bind_param("ss", $hashedPassword, $_SESSION['email']);
                    $stmt->execute();
                    $stmt->close();
                }
                
                $conn->commit();
                
                $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;
                
                $message = "Profile updated successfully";
                $messageType = "success";
                
                $userData['firstname'] = $firstname;
                $userData['lastname'] = $lastname;
                $userData['email'] = $_SESSION['email'];
                
            } catch (Exception $e) {
                $conn->rollback();
                $message = $e->getMessage();
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
    <title>User Settings | NutriTrack 2025</title>
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
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Account Settings</h1>
            <p class="text-xl opacity-90 max-w-2xl">Manage your personal information and account preferences.</p>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg p-8">
            <?php if (!empty($message)): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-800">Personal Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="firstname" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" id="firstname" name="firstname" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                value="<?php echo $userData['firstname']; ?>" required>
                        </div>
                        <div>
                            <label for="lastname" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" id="lastname" name="lastname" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                value="<?php echo $userData['lastname']; ?>" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                            value="<?php echo $userData['email']; ?>" required readonly>
                    </div>
                    
                    <hr class="my-8 border-gray-200">
                    
                    <h2 class="text-2xl font-bold text-gray-800">Change Password</h2>
                    <p class="text-sm text-gray-600 mb-4">Leave new password fields empty if you don't want to change it</p>
                    
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" id="current_password" name="current_password" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                            required>
                        <p class="text-xs text-gray-500 mt-1">Required to save any changes</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" id="new_password" name="new_password" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                minlength="8">
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                minlength="8">
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-4 pt-6">
                        <a href="../dashboard.php" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <footer class="bg-dark text-white mt-16">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="text-2xl font-bold mb-4">NutriTrack<span class="text-secondary">2025</span></div>
                    <p class="text-gray-400 mb-4">Your ultimate companion for tracking nutrition and achieving your health goals.</p>
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
                        <li><a href="../index.php" class="text-gray-400 hover:text-white">Home</a></li>
                        <li><a href="../log/workout.php" class="text-gray-400 hover:text-white">Workouts</a></li>
                        <li><a href="../log/food.php" class="text-gray-400 hover:text-white">Nutrition</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start"><i class="fas fa-map-marker-alt mt-1 mr-2"></i> 123 Nutrition Street, Food City</li>
                        <li class="flex items-start"><i class="fas fa-phone mt-1 mr-2"></i> +1 (555) 123-4567</li>
                        <li class="flex items-start"><i class="fas fa-envelope mt-1 mr-2"></i> info@nutritrack2025.com</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 NutriTrack. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>