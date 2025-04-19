<?php
session_start();
include 'db.php';

if (isset($_SESSION['email'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = htmlspecialchars($_POST['firstname']);
    $last_name = htmlspecialchars($_POST['lastname']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $checkTableSql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(255) NOT NULL,
        lastname VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if (!$conn->query($checkTableSql)) {
        die("Error creating table: " . $conn->error);
    }

    $checkEmailSql = "SELECT id FROM users WHERE email = ?";
    $checkStmt = $conn->prepare($checkEmailSql);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'User already exists',
                text: 'An account with this email already exists.'
            });
        });
        </script>";
    } else {
        $sql = "INSERT INTO users(firstname, lastname, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $password);

        if ($stmt->execute()) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Registered Successfully',
                    text: 'Redirecting to login...',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'login.php';
                });
            });
            </script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $checkStmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        .hell {
            background-color: #0cbaba;
            background-image: linear-gradient(315deg, #0cbaba 0%, #380036 74%);
        }
        .alag {
            background-color: #0cbaba;
            background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkxWUuvtuj543KcI5DXLZysTOhlpxGGANsCQ&s');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body>
    <div class="h-screen w-full hell">
        <?php include 'navigation.php'; ?>
        <div class="h-[73%] w-[69%] absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 sm:flex overflow-hidden rounded-[3vw] sm:rounded-[1vw]">
            <div class="hidden sm:block sm:w-[50%] sm:h-full p-[1.8vw] bg-cover bg-no-repeat bg-center bg-[url(https://a.storyblok.com/f/80044/525x329/b2a11b66dc/01.jpg)]">
                <h1 class="text-[2.7vw] text-white font-bold mb-[1.3vw]">Hello,</h1>
                <p class="font-semibold text-white text-[1.2vw]">"Sign up today and take the first step towards a healthier, fitter you. Compete with friends, track your workouts, and stay motivated!"</p>
            </div>
            <div class="alag w-full h-full bg-zinc-200 sm:w-[50%] sm:h-full relative"> 
                <form action="register.php" method="POST" onsubmit="return validateForm()" class="w-[95%] text-white h-[95%] absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                    <h1 class="uppercase text-[4vw] sm:text-[1.7vw] font-bold text-center">Sign up</h1>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-[2vw] w-full mt-[3vw] sm:mt-[1.6vw]">
                        <div class="flex flex-col items-center sm:items-start sm:w-[50%] mb-[2vw] sm:mb-0">
                            <h3 class="text-[3.3vw] font-[600] sm:text-[1.2vw]">First Name</h3>
                            <input type="text" required name="firstname" placeholder="Enter first name" class="w-[80%]  text-black opacity-70 p-[2vw] sm:p-[.5vw] sm:text-[1.2vw] text-[2vw] border border-zinc-300 sm:border-zinc-400 sm:rounded-[1vw] rounded-[4vw]">
                        </div>
                        <div class="flex flex-col items-center sm:items-end sm:w-[50%]">
                            <h3 class="text-[3.3vw] font-[600] sm:text-[1.2vw]">Last Name</h3>
                            <input type="text" required name="lastname" placeholder="Enter last name" class="w-[80%] opacity-70 text-black p-[2vw] sm:p-[.5vw] sm:text-[1.2vw] text-[2vw] border border-zinc-300 sm:border-zinc-400 sm:rounded-[1vw] rounded-[4vw]">
                        </div>
                    </div>
                    <div class="flex flex-col items-center mt-[2vw] sm:mt-0">
                        <h3 class="text-[3.3vw] sm:text-[1.2vw] font-[600]">Email Address</h3>
                        <input type="email" required name="email" placeholder="Enter email" class="w-[80%] sm:w-full opacity-70  text-black sm:p-[.5vw] sm:text-[1.2vw] p-[2vw] text-[2vw] border border-zinc-300 sm:border-zinc-400 sm:rounded-[1vw] rounded-[4vw]">
                    </div>
                    <div class="flex flex-col items-center mt-[2vw] sm:mt-[1.6vw]">
                        <h3 class="text-[3.3vw] sm:text-[1.2vw] font-[600]">Enter Password</h3>
                        <input type="password" required name="password" placeholder="Enter password" class="sm:w-full w-[80%]  text-black opacity-70 sm:p-[.5vw] sm:text-[1.2vw] p-[2vw] text-[2vw] border border-zinc-300 sm:border-zinc-400 sm:rounded-[1vw] rounded-[4vw]">
                    </div>
                    <div class="flex items-center justify-center mt-[7vw] sm:mt-[3vw]">
                        <button type="submit" class="text-[4vw] sm:text-[1.3vw] hover:bg-blue-900  font-semibold sm:w-full w-[80%] sm:py-[.6vw] border border-zinc-400 text-center rounded-[4vw]">Register</button>
                    </div> 
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector('form');

        form.addEventListener("submit", function (e) {
            const firstName = document.querySelector('input[name="firstname"]').value.trim();
            const lastName = document.querySelector('input[name="lastname"]').value.trim();
            const email = document.querySelector('input[name="email"]').value.trim();
            const password = document.querySelector('input[name="password"]').value.trim();

            const namePattern = /^[A-Za-z]{2,}$/;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!namePattern.test(firstName)) {
                alert('First name must contain only letters and be at least 2 characters long');
                e.preventDefault();
                return;
            }

            if (!namePattern.test(lastName)) {
                alert('Last name must contain only letters and be at least 2 characters long');
                e.preventDefault();
                return;
            }

            if (!emailPattern.test(email)) {
                alert('Please enter a valid email address');
                e.preventDefault();
                return;
            }

            if (!passwordPattern.test(password)) {
                alert('Password must be at least 8 characters long and contain at least one letter, one number, and one special character.');
                e.preventDefault();
                return;
}
        });
    });
</script>

</body>
</html>
