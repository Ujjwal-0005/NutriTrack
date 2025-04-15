<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Progress</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-600">Your Fitness Progress</h1>
        
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Overall Progress Card -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Overall Progress</h2>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-blue-700">Total Tasks Completed</span>
                            <span class="text-sm font-medium text-gray-500">12/20</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 60%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-green-700">Weekly Goal Progress</span>
                            <span class="text-sm font-medium text-gray-500">8/10 hours</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-600 h-2.5 rounded-full" style="width: 80%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Tasks Card -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Recent Tasks</h2>
                <ul class="divide-y divide-gray-200">
                    <li class="py-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Running</p>
                                <p class="text-xs text-gray-500">30 minutes, Cardio</p>
                            </div>
                            <span class="text-green-600 text-sm">Completed</span>
                        </div>
                    </li>
                    <li class="py-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Weight Training</p>
                                <p class="text-xs text-gray-500">45 minutes, Strength</p>
                            </div>
                            <span class="text-green-600 text-sm">Completed</span>
                        </div>
                    </li>
                    <li class="py-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Yoga</p>
                                <p class="text-xs text-gray-500">60 minutes, Flexibility</p>
                            </div>
                            <span class="text-gray-500 text-sm">Pending</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>