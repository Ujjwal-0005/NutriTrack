<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = htmlspecialchars($_POST['firstname']);
    $last_name = htmlspecialchars($_POST['lastname']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $checkEmailSql = "SELECT id FROM users WHERE email = ?";
    $checkStmt = $conn->prepare($checkEmailSql);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $error_message = "An account with this email already exists.";
    } else {
        $sql = "INSERT INTO users (firstname, lastname, email, password, admin) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $first_name, $last_name, $email, $password, $is_admin);

        if ($stmt->execute()) {
            $success_message = "User added successfully.";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $checkStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User | Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Add New User</h1>
                <p class="text-gray-600 mt-1">Create a new user account</p>
            </div>
            <a href="users.php" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i> Back to Users
            </a>
        </div>

        <!-- Add User Form -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <form method="POST" action="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="firstname" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" id="firstname" name="firstname" required
                                class="w-full px-4 py-2 border rounded-md focus:ring-admin focus:border-admin"
                                placeholder="Enter first name">
                        </div>
                        <div>
                            <label for="lastname" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" id="lastname" name="lastname" required
                                class="w-full px-4 py-2 border rounded-md focus:ring-admin focus:border-admin"
                                placeholder="Enter last name">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-2 border rounded-md focus:ring-admin focus:border-admin"
                            placeholder="Enter email address">
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-2 border rounded-md focus:ring-admin focus:border-admin"
                            placeholder="Enter password">
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_admin" name="is_admin" class="h-4 w-4 text-admin focus:ring-admin border-gray-300 rounded">
                            <label for="is_admin" class="ml-2 block text-sm text-gray-700">
                                Admin privileges
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Admin users have full access to all administrative features.
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <button type="reset" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md mr-2">
                            Reset
                        </button>
                        <button type="submit" class="bg-admin hover:bg-pink-800 text-white font-medium py-2 px-4 rounded-md">
                            <i class="fas fa-user-plus mr-2"></i> Add User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-6 mt-16">
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

    <?php if ($error_message): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo $error_message; ?>',
            confirmButtonColor: '#BE185D'
        });
    </script>
    <?php endif; ?>

    <?php if ($success_message): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?php echo $success_message; ?>',
            confirmButtonColor: '#BE185D'
        });
    </script>
    <?php endif; ?>
</body>
</html>