<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head> 
    
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-blue-600">Create Fitness Task</h1>
        
        <form action="process_task.php" method="POST" class="space-y-4">
            <div>
                <label for="task-name" class="block text-gray-700 font-semibold mb-2">Task Name</label>
                <input type="text" id="task-name" name="task_name" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="task-type" class="block text-gray-700 font-semibold mb-2">Task Type</label>
                <select id="task-type" name="task_type" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="cardio">Cardio</option>
                    <option value="strength">Strength Training</option>
                    <option value="flexibility">Flexibility</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div>
                <label for="duration" class="block text-gray-700 font-semibold mb-2">Duration (minutes)</label>
                <input type="number" id="duration" name="duration" min="1" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="difficulty" class="block text-gray-700 font-semibold mb-2">Difficulty Level</label>
                <select id="difficulty" name="difficulty" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
            
            <div>
                <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
                <textarea id="description" name="description" rows="3" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <button type="submit" 
                class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-300">
                Create Task
            </button>
        </form>
    </div>
</body>
</html>
 
</html>