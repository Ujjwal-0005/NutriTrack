<?php
session_start();
include '../db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['email']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Initialize variables
$message = '';
$messageType = '';
$redirectDelay = 3; // seconds to wait before redirecting

// Check if ID parameter exists
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Get challenge info before deletion (to handle badge image)
    $get_challenge = $conn->prepare("SELECT badge_image FROM challenges WHERE id = ?");
    $get_challenge->bind_param("i", $id);
    $get_challenge->execute();
    $result = $get_challenge->get_result();
    
    if ($result->num_rows > 0) {
        $challenge = $result->fetch_assoc();
        
        // Delete the challenge from database
        $stmt = $conn->prepare("DELETE FROM challenges WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // If challenge had a badge image, delete the file
            if (!empty($challenge['badge_image'])) {
                $image_path = "../uploads/badges/" . $challenge['badge_image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            $message = "Challenge deleted successfully.";
            $messageType = "success";
        } else {
            $message = "Error deleting challenge: " . $stmt->error;
            $messageType = "error";
        }
        
        $stmt->close();
    } else {
        $message = "Challenge not found.";
        $messageType = "error";
    }
    
    $get_challenge->close();
} else {
    $message = "Invalid challenge ID.";
    $messageType = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Challenge | NutriTrack 2025</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta http-equiv="refresh" content="<?php echo $redirectDelay; ?>;url=challenges.php">
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
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 <?php echo $messageType === 'success' ? 'bg-green-50' : 'bg-red-50'; ?>">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <?php if ($messageType === 'success'): ?>
                            <i class="fas fa-check-circle text-2xl text-green-500"></i>
                        <?php else: ?>
                            <i class="fas fa-exclamation-circle text-2xl text-red-500"></i>
                        <?php endif; ?>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium <?php echo $messageType === 'success' ? 'text-green-800' : 'text-red-800'; ?>">
                            <?php echo $messageType === 'success' ? 'Challenge Deleted' : 'Error'; ?>
                        </h3>
                        <p class="mt-1 text-sm <?php echo $messageType === 'success' ? 'text-green-700' : 'text-red-700'; ?>">
                            <?php echo $message; ?>
                        </p>
                    </div>
                </div>
                
                <div class="mt-4 border-t border-gray-200 pt-4">
                    <p class="text-sm text-gray-600">
                        Redirecting to challenges page in <span id="countdown"><?php echo $redirectDelay; ?></span> seconds...
                    </p>
                    <div class="mt-4">
                        <a href="challenges.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-admin hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-admin">
                            Go back now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Countdown timer
        let seconds = <?php echo $redirectDelay; ?>;
        const countdownDisplay = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            seconds--;
            countdownDisplay.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(timer);
            }
        }, 1000);
    </script>
</body>
</html>