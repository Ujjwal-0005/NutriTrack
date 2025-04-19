
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriTrack - Fitness Challenge Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#10B981',
                        accent: '#F59E0B',
                        dark: '#1F2937',
                    }
                }
            }
        }
    </script>
    <style>
      
        </style>
</head>
<body class="bg-gray-50 font-sans ">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-primary">NutriTrack</span>
                       
                    </div>
                </div>
             
                <div class="flex items-center space-x-4">
                   
                    <a href="index.php" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition">Home</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-50 to-green-50 py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex items-center justify-between">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-bold text-dark mb-6">Transform Your Fitness Journey</h1>
                    <p class="text-lg text-gray-600 mb-8">Join challenges, track progress, and achieve your fitness goals with NutriTrack's powerful challenge tracker.</p>
                    <div class="flex space-x-4">
                        <!-- <a href="register.php" class="px-8 py-3 bg-primary text-white rounded-lg hover:bg-blue-600 transition">Get Started</a> -->
                        <!-- <a href="#" class="px-8 py-3 border border-primary text-primary rounded-lg hover:bg-blue-50 transition">Learn More</a> -->
                    </div>
                </div>
                <div class="md:w-1/2">
                    <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" alt="Fitness Challenge" class="rounded-xl shadow-xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-dark mb-4">Powerful Features</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Everything you need to create, join, and track fitness challenges</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="bg-gray-50 p-8 rounded-xl hover:shadow-lg transition">
                    <div class="w-14 h-14 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-3">Eat better and hit your goals</h3>
                    <p class="text-gray-600">Learn which foods help you feel your best, and get tailored weekly meal plans!</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-gray-50 p-8 rounded-xl hover:shadow-lg transition">
                    <div class="w-14 h-14 bg-secondary bg-opacity-10 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-3">Progress Analytics</h3>
                    <p class="text-gray-600">Detailed analytics and visualizations to monitor your improvement over time and stay motivated.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-gray-50 p-8 rounded-xl hover:shadow-lg transition">
                    <div class="w-14 h-14 bg-accent bg-opacity-10 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-3">Milestone Achievements</h3>
                    <p class="text-gray-600">Earn scores and badges for achieving your fitness milestones and challenge goals.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-dark mb-4">How NutriTrack Works</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Simple steps to transform your fitness routine</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">1</div>
                    <h3 class="text-xl font-semibold text-dark mb-3">Create or Join</h3>
                    <p class="text-gray-600">Start your own fitness challenge or join existing ones from our community.</p>
                </div>
                
                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-secondary text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">2</div>
                    <h3 class="text-xl font-semibold text-dark mb-3">Track Progress</h3>
                    <p class="text-gray-600">Log your activities, workouts, and nutrition to track your challenge progress.</p>
                </div>
                
                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-accent text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">3</div>
                    <h3 class="text-xl font-semibold text-dark mb-3">Achieve</h3>
                    <p class="text-gray-600">Complete challenges and celebrate your fitness achievements.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-dark mb-4">What Our Users Say</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Join thousands of fitness enthusiasts transforming their lives</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-gray-50 p-8 rounded-xl">
                    <div class="flex items-center mb-6">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Sarah J." class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-semibold text-dark">Sarah J.</h4>
                            <p class="text-gray-500">Fitness Coach</p>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">"NutriTrack has revolutionized how I run challenges for my clients. The tracking features and reward system keep everyone motivated!"</p>
                    <div class="flex mt-4 text-yellow-400">
                        ★★★★★
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="bg-gray-50 p-8 rounded-xl">
                    <div class="flex items-center mb-6">
                        <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Michael T." class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-semibold text-dark">Michael T.</h4>
                            <p class="text-gray-500">Marathon Runner</p>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">"I've joined 5 challenges this year and each one helped me push my limits. The community support is incredible!"</p>
                    <div class="flex mt-4 text-yellow-400">
                        ★★★★★
                    </div>
                </div>
            </div>
        </div>
    </section>

 
</body>
</html>
