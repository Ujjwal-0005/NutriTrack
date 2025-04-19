<?php
session_start();
include 'db.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$tableCheckQuery = "SHOW TABLES LIKE 'users'";
$result = $conn->query($tableCheckQuery);

if ($result->num_rows == 0) {
    $createTableQuery = "
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            firstname VARCHAR(100) NOT NULL,
            lastname VARCHAR(100) NOT NULL,
            admin TINYINT(1) DEFAULT 0
        )
    ";
    if (!$conn->query($createTableQuery)) {
        die("Table creation failed: " . $conn->error);
    }
}

if (isset($_SESSION['email'])) {
    header("Location: dashboard/user.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT firstname, lastname, password FROM users WHERE email = ?";
    if (!$stmt = $conn->prepare($sql)) {
        die("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        die("Query execution failed: " . $stmt->error);
    }

    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($firstname, $lastname, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;
            setcookie("email", $email, time() + (86400 * 30), "/");
            setcookie("firstname", $firstname, time() + (86400 * 30), "/");
            setcookie("lastname", $lastname, time() + (86400 * 30), "/");
            setcookie("admin", 0, time() + (86400 * 30), "/");
            header("Location: dashboard/user.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | NutriTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#10B981',
                        accent: '#bfe305',
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
        .hero-overlay {
            background: linear-gradient(to right, rgba(17, 24, 39, 0.8), rgba(17, 24, 39, 0.4));
        }
    </style>
</head>
<style>
    body {
        background-image: url('https://img.freepik.com/premium-photo/photo-portrait-highend-fitness-website-background_1077802-309192.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }
</style>
<body class="bg-slate-500 font-sans">
    <div class="min-h-screen flex flex-col">
        <?php include 'navigation.php'; ?>
        
        <div class="flex-grow flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-5xl flex overflow-hidden rounded-3xl shadow-2xl">
                <!-- Left side - Image with overlay -->
                <div class="hidden md:block w-1/2 bg-cover bg-center relative">
                    <img src="https://cbx-prod.b-cdn.net/COLOURBOX61567836.jpg?width=800&height=800&quality=70" alt="Fitness" class="w-full h-full object-cover">
                    <div class="absolute inset-0 hero-overlay flex flex-col justify-between p-8">
                        <div class="mb-auto">
                            <h2 class="text-4xl font-bold text-white mb-6">Welcome Back</h2>
                            <p class="text-xl text-gray-100 max-w-md">
                                "Your fitness journey continues here. Sign in to access your workouts and track your progress."
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-200 mb-4">Don't have an account yet?</p>
                            <a href="register.php" class="px-6 py-3 bg-accent hover:bg-[#a5c104] text-black font-semibold rounded-lg transition-all duration-300 inline-block">
                                Register Now
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Right side - Login form -->
                <div class="w-full md:w-1/2 bg-white p-8 md:p-12">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl md:text-4xl font-bold text-dark">Login to Your Account</h1>
                        <p class="text-gray-600 mt-2">Enter your credentials to continue</p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                        <div class="p-4 mb-6 text-sm rounded-lg bg-red-50 text-red-600 border border-red-200">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="ri-mail-line text-gray-400"></i>
                                </div>
                                <input type="email" id="email" name="email" required 
                                    class="block w-full pl-10 px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none"
                                    placeholder="you@example.com">
                            </div>
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="ri-lock-line text-gray-400"></i>
                                </div>
                                <input type="password" id="password" name="password" required 
                                    class="block w-full pl-10 px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none"
                                    placeholder="••••••••">
                            </div>
                        </div>
                        
                        
                        
                        <button type="submit" 
                            class="w-full flex justify-center items-center px-6 py-3 bg-primary hover:bg-indigo-600 text-white font-semibold rounded-lg transition-all duration-300">
                            <span>Sign In</span>
                            <i class="ri-arrow-right-line ml-2"></i>
                        </button>
                    </form>
                    
                    <!-- Mobile only register link -->
                    <div class="mt-8 text-center md:hidden">
                        <p class="text-gray-600">Don't have an account?</p>
                        <a href="register.php" class="mt-2 block w-full px-6 py-3 bg-gray-100 hover:bg-gray-200 text-dark font-medium rounded-lg transition-all duration-300">
                            Register Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

