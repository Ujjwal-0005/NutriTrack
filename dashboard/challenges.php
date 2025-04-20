<?php
session_start();
include '../db.php';

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Get user ID from email
$user_query = $conn->prepare("SELECT id FROM users WHERE email = ?");
$user_query->bind_param("s", $_SESSION['email']);
$user_query->execute();
$user_result = $user_query->get_result();

if ($user_result->num_rows > 0) {
    $user_row = $user_result->fetch_assoc();
    $user_id = $user_row['id'];
} else {
    // Handle case where user ID can't be found
    header("Location: ../login.php");
    exit();
}

// Handle challenge completion submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_challenge'])) {
    $challenge_id = (int)$_POST['challenge_id'];
    
    // Check if challenge exists and is active
    $check_challenge = $conn->prepare("SELECT * FROM challenges WHERE id = ? AND NOW() BETWEEN start_date AND end_date");
    $check_challenge->bind_param("i", $challenge_id);
    $check_challenge->execute();
    $challenge_result = $check_challenge->get_result();
    
    if ($challenge_result->num_rows > 0) {
        $challenge = $challenge_result->fetch_assoc();
        
        // Check if user has already completed this challenge
        $check_completed = $conn->prepare("SELECT * FROM completed_challenges WHERE user_id = ? AND challenge_id = ?");
        $check_completed->bind_param("ii", $user_id, $challenge_id);
        $check_completed->execute();
        $completed_result = $check_completed->get_result();
        
        if ($completed_result->num_rows == 0) {
            // Mark challenge as completed
            $complete_challenge = $conn->prepare("INSERT INTO completed_challenges (user_id, challenge_id) VALUES (?, ?)");
            $complete_challenge->bind_param("ii", $user_id, $challenge_id);
            
            if ($complete_challenge->execute()) {
                // Add points to user's total
                $points = $challenge['points'];
                
                // Check if user already has points record
                $check_points = $conn->prepare("SELECT * FROM challenge_points WHERE user_id = ?");
                $check_points->bind_param("i", $user_id);
                $check_points->execute();
                $points_result = $check_points->get_result();
                
                if ($points_result->num_rows > 0) {
                    // Update existing points
                    $update_points = $conn->prepare("UPDATE challenge_points SET points = points + ? WHERE user_id = ?");
                    $update_points->bind_param("ii", $points, $user_id);
                    $update_points->execute();
                } else {
                    // Create new points record
                    $insert_points = $conn->prepare("INSERT INTO challenge_points (user_id, points) VALUES (?, ?)");
                    $insert_points->bind_param("ii", $user_id, $points);
                    $insert_points->execute();
                }
                
                $success_message = "Congratulations! You've completed the '{$challenge['title']}' challenge and earned {$points} points!";
            } else {
                $error_message = "Error completing challenge. Please try again.";
            }
        } else {
            $error_message = "You've already completed this challenge.";
        }
    } else {
        $error_message = "Challenge not found or not active.";
    }
}

// Get user's total points
$total_points = 0;
$points_query = $conn->prepare("SELECT points FROM challenge_points WHERE user_id = ?");
$points_query->bind_param("i", $user_id);
$points_query->execute();
$points_result = $points_query->get_result();

if ($points_result->num_rows > 0) {
    $points_row = $points_result->fetch_assoc();
    $total_points = $points_row['points'];
}

// Get user's completed challenges
$completed_challenges = [];
$completed_query = $conn->prepare("SELECT challenge_id FROM completed_challenges WHERE user_id = ?");
$completed_query->bind_param("i", $user_id);
$completed_query->execute();
$completed_result = $completed_query->get_result();

while ($row = $completed_result->fetch_assoc()) {
    $completed_challenges[] = $row['challenge_id'];
}

// Get active challenges
$active_challenges_sql = "SELECT * FROM challenges 
                         WHERE NOW() BETWEEN start_date AND end_date 
                         ORDER BY difficulty ASC, points DESC";
$active_challenges_result = $conn->query($active_challenges_sql);

// Get user's rank
$rank = 0;
$rank_query = "SELECT user_id, points, 
              (@rank := IF(@prev = points, @rank, @counter)) AS user_rank,
              (@counter := @counter + 1) AS counter,
              (@prev := points) AS prev
              FROM challenge_points, (SELECT @rank := 0, @counter := 1, @prev := NULL) r
              ORDER BY points DESC";
$conn->query("SET @rank := 0, @counter := 1, @prev := NULL");
$rank_result = $conn->query($rank_query);

if ($rank_result && $rank_result->num_rows > 0) {
    while ($rank_row = $rank_result->fetch_assoc()) {
        if ($rank_row['user_id'] == $user_id) {
            $rank = $rank_row['user_rank'];
            break;
        }
    }
}

// Get completed challenges history
$history_sql = "SELECT cc.completed_at, c.title, c.points, c.badge_image, c.category, c.difficulty
               FROM completed_challenges cc
               JOIN challenges c ON cc.challenge_id = c.id
               WHERE cc.user_id = ?
               ORDER BY cc.completed_at DESC";
$history_query = $conn->prepare($history_sql);
$history_query->bind_param("i", $user_id);
$history_query->execute();
$history_result = $history_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Challenges | NutriTrack 2025</title>
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
<body class="bg-gray-100 min-h-screen">
    <!-- Top Navigation -->
    <nav class="das text-white shadow-md">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <span class="text-2xl font-bold mr-6">NutriTrack</span>
                <div class="hidden md:flex space-x-6 font-medium text-sm">
                    <a href="user.php" class="py-2 px-3 hover:bg-white hover:text-blue-900 rounded-md">Dashboard</a>
                    <a href="../log/food.php" class="py-2 px-3 hover:bg-white hover:text-blue-900 rounded-md">Meal Tracker</a>
                    <a href="../log/workout.php" class="py-2 px-3 hover:bg-white hover:text-blue-900 rounded-md">Workouts</a>
                    <a href="user.php" class="py-2 px-3 hover:bg-white hover:text-blue-900 rounded-md">Progress</a>
                    
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative group">
                    <button class="flex items-center px-3 py-2  space-x-2">
                        <div class="w-8 h-8 rounded-full bg-indigo-700 flex items-center justify-center">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="hidden md:inline-block"><?php echo $_SESSION['firstname']; ?></span>
                        <i class="fas fa-chevron-down text-xs"></i>

                    </button>
                    <div class="absolute right-0 w-48 bg-white rounded-md shadow-lg py-1 mt-2 z-10 hidden group-hover:block">
                        <a href="../settings/user.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-cog mr-2"></i> Profile Settings
                        </a>
                        <a href="../logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </nav>
        <style>
            .neww{
                background-color: #0073cf;
background-image: linear-gradient(315deg, #0073cf 0%,rgb(236, 224, 218) 74%);


            }
            </style>
    <!-- Main Content -->
    <div class="container neww mx-auto px-6 py-8">
        <!-- Page Header with User Points -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Fitness Challenges</h1>
                <p class="text-gray-600 mt-1">Complete challenges to earn points and badges</p>
            </div>
            <div class="mt-4 md:mt-0 bg-white p-4 rounded-lg shadow-md flex items-center">
                <div class="mr-4">
                    <div class="text-sm text-gray-500">Your Rank</div>
                    <div class="text-xl font-bold text-indigo-600">
                        <?php echo $rank > 0 ? "#" . $rank : "Not Ranked"; ?>
                    </div>
                </div>
                <div class="border-l border-gray-200 pl-4 flex items-center">
                    <div class="bg-yellow-100 rounded-full p-2 mr-3">
                        <i class="fas fa-star text-yellow-500"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Total Points</div>
                        <div class="text-xl font-bold text-indigo-600"><?php echo $total_points; ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Alert Messages -->
        <?php if (!empty($success_message)): ?>
        <div class="mb-6 p-4 rounded-md bg-green-100 text-green-700 flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i>
            <div>
                <p class="font-medium"><?php echo $success_message; ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
        <div class="mb-6 p-4 rounded-md bg-red-100 text-red-700 flex items-center">
            <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
            <div>
                <p class="font-medium"><?php echo $error_message; ?></p>
            </div>

        </div>
        <?php endif; ?>

        <!-- Challenge Categories -->
        <div class="bg-white shadow-md rounded-lg mb-8 overflow-hidden">
            <div class="p-5 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Challenge Categories</h2>
            </div>
            <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="#" class="category-filter flex flex-col items-center p-4 bg-blue-100 rounded-lg hover:bg-blue-200 transition" data-category="all">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-layer-group text-white text-xl"></i>
                    </div>
                    <span class="font-medium text-blue-800">All</span>
                </a>
                <a href="#" class="category-filter flex flex-col items-center p-4 bg-green-100 rounded-lg hover:bg-green-200 transition" data-category="cardio">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-running text-white text-xl"></i>
                    </div>
                    <span class="font-medium text-green-800">Cardio</span>
                </a>
                <a href="#" class="category-filter flex flex-col items-center p-4 bg-red-100 rounded-lg hover:bg-red-200 transition" data-category="strength">
                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-dumbbell text-white text-xl"></i>
                    </div>
                    <span class="font-medium text-red-800">Strength</span>
                </a>
                <a href="#" class="category-filter flex flex-col items-center p-4 bg-purple-100 rounded-lg hover:bg-purple-200 transition" data-category="nutrition">
                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-apple-alt text-white text-xl"></i>
                    </div>
                    <span class="font-medium text-purple-800">Nutrition</span>
                </a>
            </div>
        </div>

        <!-- Active Challenges -->
        <div class="bg-white shadow-md rounded-lg mb-8 overflow-hidden">
            <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Active Challenges</h2>
                <div class="flex space-x-2">
                    <button id="toggleCompleted" class="text-sm bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded-full">
                        <i class="fas fa-eye-slash mr-1"></i> Hide Completed
                    </button>
                    
                </div>
            </div>
            
            <?php if ($active_challenges_result && $active_challenges_result->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6" id="challengeContainer">
                    <?php while($challenge = $active_challenges_result->fetch_assoc()): 
                        $is_completed = in_array($challenge['id'], $completed_challenges);
                        $challenge_class = $is_completed ? 'challenge-completed' : '';
                        
                        // Calculate days remaining
                        $end_date = new DateTime($challenge['end_date']);
                        $today = new DateTime();
                        $days_remaining = $today->diff($end_date)->days;
                        
                        // Set gradient based on category
                        $gradient = 'from-blue-500 to-indigo-600';
                        switch($challenge['category']) {
                            case 'cardio':
                                $gradient = 'from-green-400 to-green-600';
                                break;
                            case 'strength':
                                $gradient = 'from-red-400 to-red-600';
                                break;
                            case 'flexibility':
                                $gradient = 'from-purple-400 to-purple-600';
                                break;
                            case 'nutrition':
                                $gradient = 'from-yellow-400 to-yellow-600';
                                break;
                            case 'wellness':
                                $gradient = 'from-blue-400 to-blue-600';
                                break;
                            case 'weight_loss':
                                $gradient = 'from-pink-400 to-pink-600';
                                break;
                            case 'hydration':
                                $gradient = 'from-cyan-400 to-cyan-600';
                                break;
                            case 'step_count':
                                $gradient = 'from-emerald-400 to-emerald-600';
                                break;
                        }
                    ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow <?php echo $challenge_class; ?>" 
                         data-category="<?php echo $challenge['category']; ?>"
                         data-difficulty="<?php echo $challenge['difficulty']; ?>">
                        <div class="h-32 bg-gradient-to-r <?php echo $gradient; ?> relative">
                            <?php if(!empty($challenge['badge_image'])): ?>
                                <div class="absolute -bottom-10 left-5 w-20 h-20 rounded-full border-4 border-white bg-white shadow-md flex items-center justify-center overflow-hidden">
                                    <img src="../uploads/badges/<?php echo $challenge['badge_image']; ?>" alt="Badge" class="max-w-full max-h-full">
                                </div>
                            <?php else: ?>
                                <div class="absolute -bottom-10 left-5 w-20 h-20 rounded-full border-4 border-white bg-gradient-to-r from-indigo-500 to-purple-600 shadow-md flex items-center justify-center">
                                    <i class="fas fa-trophy text-white text-2xl"></i>
                                </div>
                            <?php endif; ?>
                            
                            
                            <?php if($is_completed): ?>
                                <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center">
                                    <span class="bg-white rounded-full py-1 px-4 text-sm font-medium text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i> Completed
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-5 pt-12">
                            <div class="flex items-center mb-3">
                                <span class="bg-<?php echo $challenge['category']; ?>-100 text-<?php echo $challenge['category']; ?>-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                    <?php echo ucfirst($challenge['category']); ?>
                                </span>
                                <div class="ml-auto text-xs text-gray-500 flex items-center">
                                    <i class="far fa-clock mr-1"></i>
                                    <?php echo $days_remaining; ?> days left
                                </div>
                            </div>
                            
                            <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($challenge['title']); ?></h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3"><?php echo htmlspecialchars($challenge['description']); ?></p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="bg-yellow-100 rounded-full p-1 mr-1">
                                        <i class="fas fa-star text-yellow-500 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium"><?php echo $challenge['points']; ?> points</span>
                                </div>
                                
                                <?php if(!$is_completed): ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="challenge_id" value="<?php echo $challenge['id']; ?>">
                                        <button type="submit" name="complete_challenge" class="px-3 py-1 bg-primary text-white text-sm rounded-full hover:bg-indigo-700 transition-colors">
                                            Complete
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-green-600 text-sm font-medium">
                                        <i class="fas fa-medal mr-1"></i> Earned
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="p-12 text-center">
                    <div class="inline-flex bg-blue-100 rounded-full p-6 mb-4">
                        <i class="fas fa-calendar-alt text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No active challenges</h3>
                    <p class="text-gray-500 mb-6">Check back later for new challenges to complete!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Your Challenge History -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-5 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Your Challenge History</h2>
            </div>
            
            <?php if ($history_result && $history_result->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Completed</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Challenge</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                               
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Badge</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while($history = $history_result->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M d, Y', strtotime($history['completed_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo $history['title']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo ucfirst($history['category']); ?>
                                        </span>
                                    </td>
                                  
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $history['points']; ?> points
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($history['badge_image']): ?>
                                            <img src="../uploads/badges/<?php echo $history['badge_image']; ?>" alt="Badge" class="h-8 w-8 rounded-full">
                                        <?php else: ?>
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <i class="fas fa-trophy text-indigo-600 text-xs"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-12 text-center">
                    <div class="inline-flex bg-blue-100 rounded-full p-6 mb-4">
                        <i class="fas fa-trophy text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No challenges completed yet</h3>
                    <p class="text-gray-500 mb-6">Complete your first challenge to see your history here!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-6 ">
        <?php include '../footer.php'; ?>
    </footer>

    <script>
        // Toggle completed challenges
        const toggleButton = document.getElementById('toggleCompleted');
        let showingCompleted = true;
        
        if (toggleButton) {
            toggleButton.addEventListener('click', function() {
                const completedChallenges = document.querySelectorAll('.challenge-completed');
                
                if (showingCompleted) {
                    completedChallenges.forEach(el => el.classList.add('hidden'));
                    this.innerHTML = '<i class="fas fa-eye mr-1"></i> Show Completed';
                } else {
                    completedChallenges.forEach(el => el.classList.remove('hidden'));
                    this.innerHTML = '<i class="fas fa-eye-slash mr-1"></i> Hide Completed';
                }
                showingCompleted = !showingCompleted;
            });
        }

        // Filter challenges by category
        const categoryFilters = document.querySelectorAll('.category-filter');
        const challengeContainer = document.getElementById('challengeContainer');
        const challenges = challengeContainer ? challengeContainer.querySelectorAll('div[data-category]') : [];
        const difficultyFilter = document.getElementById('difficultyFilter');

        if (challengeContainer && difficultyFilter) {
            const allChallenges = Array.from(challenges);
            
            const filterChallenges = (category, difficulty) => {
                allChallenges.forEach(challenge => {
                    const challengeCategory = challenge.getAttribute('data-category');
                    const challengeDifficulty = challenge.getAttribute('data-difficulty');
                    
                    if ((category === 'all' || challengeCategory === category) && 
                        (difficulty === 'all' || challengeDifficulty === difficulty)) {
                        challenge.classList.remove('hidden');
                    } else {
                        challenge.classList.add('hidden');
                    }
                });
            };
            
            categoryFilters.forEach(filter => {
                filter.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all filters
                    categoryFilters.forEach(f => f.classList.remove('active', 'ring-2', 'ring-offset-2'));
                    
                    // Add active class to clicked filter
                    this.classList.add('active', 'ring-2', 'ring-offset-2', 'ring-primary');
                    
                    const selectedCategory = this.getAttribute('data-category');
                    const selectedDifficulty = difficultyFilter.value;
                    
                    filterChallenges(selectedCategory, selectedDifficulty);
                });
            });
            
            difficultyFilter.addEventListener('change', function() {
                const selectedCategory = document.querySelector('.category-filter.active') ? 
                    document.querySelector('.category-filter.active').getAttribute('data-category') : 'all';
                const selectedDifficulty = this.value;
                
                filterChallenges(selectedCategory, selectedDifficulty);
            });
            
            // Set "All" as default active filter
            categoryFilters[0].classList.add('active', 'ring-2', 'ring-offset-2', 'ring-primary');
        }
    </script>
</body>
</html>
