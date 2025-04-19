<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Backup directory
$backup_dir = '../backups/';
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Process backup request
$backup_message = '';
if (isset($_POST['create_backup'])) {
    $backup_file = $backup_dir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $dbuser = 'root'; // Database username
    $dbpass = 'root';
    $dbhost = 'localhost'; // Database host
    $dbname = 'fitness'; // Database name
    
    $command = "mysqldump --user={$dbuser} --password={$dbpass} --host={$dbhost} {$dbname} > {$backup_file}";
    
    exec($command, $output, $return_var);

    if ($return_var === 0) {
        $backup_message = '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">Backup created successfully at ' . $backup_file . '</div>';
    } else {
        $backup_message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">Backup failed. Error code: ' . $return_var . '</div>';
    }
}

// Process restore request
if (isset($_POST['restore_backup']) && isset($_POST['backup_file'])) {
    $selected_file = $_POST['backup_file'];
    $full_path = $backup_dir . basename($selected_file);
    
    if (file_exists($full_path)) {
        $dbuser = 'root'; // Database username
        $dbpass = 'root';
        $dbhost = 'localhost'; // Database host
        $dbname = 'fitness'; // Database name
        
        $command = "mysql --user={$dbuser} --password={$dbpass} --host={$dbhost} {$dbname} < {$full_path}";
        
        exec($command, $output, $return_var);
        
        if ($return_var === 0) {
            $backup_message = '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">Database restored successfully from ' . basename($selected_file) . '</div>';
        } else {
            $backup_message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">Restore failed. Error code: ' . $return_var . '</div>';
        }
    } else {
        $backup_message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">Backup file not found.</div>';
    }
}

// Process delete backup request
if (isset($_POST['delete_backup']) && isset($_POST['backup_file'])) {
    $selected_file = $_POST['backup_file'];
    $full_path = $backup_dir . basename($selected_file);
    
    if (file_exists($full_path)) {
        if (unlink($full_path)) {
            $backup_message = '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">Backup file deleted successfully.</div>';
        } else {
            $backup_message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">Failed to delete backup file.</div>';
        }
    } else {
        $backup_message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">Backup file not found.</div>';
    }
}

// Get list of backup files
$backup_files = [];
if (is_dir($backup_dir)) {
    $files = scandir($backup_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $backup_files[] = [
                'name' => $file,
                'size' => filesize($backup_dir . $file),
                'date' => date('Y-m-d H:i:s', filemtime($backup_dir . $file))
            ];
        }
    }
    
    // Sort by date (newest first)
    usort($backup_files, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
}

// Get system info
$server_info = $_SERVER['SERVER_SOFTWARE'];
$php_version = phpversion();
$mysql_version = mysqli_get_server_info($conn);
$max_upload = ini_get('upload_max_filesize');
$post_max_size = ini_get('post_max_size');
$total_backups = count($backup_files);
$total_backup_size = array_sum(array_column($backup_files, 'size'));

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup | Admin Panel</title>
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
                    <a href="backup.php" class="py-2 px-3 bg-admin rounded-md text-white">Backup</a>
                    <a href="settings.php" class="py-2 px-3 hover:bg-gray-700 rounded-md">Settings</a>
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
                <h1 class="text-3xl font-bold text-gray-800">Database Backup</h1>
                <p class="text-gray-600 mt-1">Backup, restore, and manage your database</p>
            </div>
            <div>
                <form method="post">
                    <button type="submit" name="create_backup" class="bg-admin hover:bg-pink-800 text-white px-4 py-2 rounded-md flex items-center text-sm">
                        <i class="fas fa-download mr-2"></i> Create New Backup
                    </button>
                </form>
            </div>
        </div>

        <?php echo $backup_message; ?>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-3 space-y-8">
                <!-- Backup Management -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Backup Management</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filename</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Created</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($backup_files)): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No backup files found. Create your first backup.
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($backup_files as $file): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-database text-blue-600"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($file['name']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M d, Y H:i:s', strtotime($file['date'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo formatBytes($file['size']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-2">
                                                <form method="post" onsubmit="return confirm('Are you sure you want to restore the database from this backup?')">
                                                    <input type="hidden" name="backup_file" value="<?php echo htmlspecialchars($file['name']); ?>">
                                                    <button type="submit" name="restore_backup" class="text-blue-600 hover:text-blue-900" title="Restore">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                                <a href="<?php echo $backup_dir . htmlspecialchars($file['name']); ?>" download class="text-green-600 hover:text-green-900" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <form method="post" onsubmit="return confirm('Are you sure you want to delete this backup file?')">
                                                    <input type="hidden" name="backup_file" value="<?php echo htmlspecialchars($file['name']); ?>">
                                                    <button type="submit" name="delete_backup" class="text-red-600 hover:text-red-900" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Restore from Upload -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Restore from Upload</h2>
                    
                    <form action="upload_restore.php" method="post" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="file">
                                Select SQL Backup File
                            </label>
                            <input type="file" name="backup_file" id="file" accept=".sql" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <p class="text-gray-500 text-xs mt-1">Max file size: <?php echo $max_upload; ?></p>
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="submit" name="upload_restore" class="bg-admin hover:bg-pink-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Upload & Restore
                            </button>
                            <div class="text-red-500 text-sm">
                                <i class="fas fa-exclamation-triangle mr-1"></i> 
                                This will overwrite your current database
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Backup Schedule -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Backup Schedule</h2>
                    
                    <form action="schedule_backup.php" method="post">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Automatic Backup Frequency
                            </label>
                            <div class="mt-2">
                                <div class="flex items-center">
                                    <input type="radio" id="daily" name="frequency" value="daily" class="h-4 w-4 text-admin border-gray-300 focus:ring-admin">
                                    <label for="daily" class="ml-2 block text-sm text-gray-700">Daily</label>
                                </div>
                                <div class="flex items-center mt-2">
                                    <input type="radio" id="weekly" name="frequency" value="weekly" class="h-4 w-4 text-admin border-gray-300 focus:ring-admin" checked>
                                    <label for="weekly" class="ml-2 block text-sm text-gray-700">Weekly</label>
                                </div>
                                <div class="flex items-center mt-2">
                                    <input type="radio" id="monthly" name="frequency" value="monthly" class="h-4 w-4 text-admin border-gray-300 focus:ring-admin">
                                    <label for="monthly" class="ml-2 block text-sm text-gray-700">Monthly</label>
                                </div>
                                <div class="flex items-center mt-2">
                                    <input type="radio" id="off" name="frequency" value="off" class="h-4 w-4 text-admin border-gray-300 focus:ring-admin">
                                    <label for="off" class="ml-2 block text-sm text-gray-700">Off</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="retention">
                                Backup Retention (days)
                            </label>
                            <input type="number" id="retention" name="retention" min="1" max="365" value="30" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <p class="text-gray-500 text-xs mt-1">Backups older than this many days will be automatically deleted</p>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <button type="submit" name="save_schedule" class="bg-dark hover:bg-gray-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save Schedule
                            </button>
                            <p class="text-gray-500 text-sm">
                                <i class="fas fa-info-circle mr-1"></i>
                                Requires cron job setup on server
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Right Sidebar Column -->
            <div class="space-y-8">
                <!-- Backup Stats -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Backup Statistics</h2>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Total Backups</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo $total_backups; ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Total Storage Used</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo formatBytes($total_backup_size); ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Latest Backup</span>
                                <span class="text-sm font-medium text-gray-800">
                                    <?php 
                                        echo !empty($backup_files) ? date('M d, Y', strtotime($backup_files[0]['date'])) : 'None';
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Oldest Backup</span>
                                <span class="text-sm font-medium text-gray-800">
                                    <?php 
                                        echo !empty($backup_files) ? date('M d, Y', strtotime($backup_files[count($backup_files)-1]['date'])) : 'None';
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">System Information</h2>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Server</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($server_info); ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">PHP Version</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($php_version); ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">MySQL Version</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($mysql_version); ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Max Upload</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($max_upload); ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-600">Max POST Size</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($post_max_size); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Backup Help</h2>
                    <div class="space-y-3">
                        <div class="p-3 bg-gray-50 rounded-md">
                            <h3 class="font-medium text-gray-800">Creating Backups</h3>
                            <p class="text-sm text-gray-600 mt-1">Click the "Create New Backup" button to backup your current database.</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-md">
                            <h3 class="font-medium text-gray-800">Restoring Backups</h3>
                            <p class="text-sm text-gray-600 mt-1">Click the restore icon next to a backup to restore your database to that point. This will overwrite your current data.</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-md">
                            <h3 class="font-medium text-gray-800">Backup Schedule</h3>
                            <p class="text-sm text-gray-600 mt-1">Set up automatic backups using the scheduling tools. Requires server-side cron job configuration.</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-md">
                            <h3 class="font-medium text-blue-800">Need Help?</h3>
                            <p class="text-sm text-blue-600 mt-1">Contact your system administrator for assistance with database backups and restoration.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-6 mt-8">
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

</body>
</html>

<?php
// Helper function to format bytes
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>