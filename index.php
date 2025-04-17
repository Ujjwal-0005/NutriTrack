<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriTrack2025 | Your Ultimate Fitness Companion</title>
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
         .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        .hero-overlay {
            background: linear-gradient(to right, rgba(17, 24, 39, 0.8), rgba(17, 24, 39, 0.4));
        }
    </style>
</head>

<body class="bg-slate-50 font-sans">
    <div id="main" class="w-full">
        <!-- Hero Section -->
        <div class="relative h-screen">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://true-elevate.com/wp-content/uploads/2024/12/1.jpg')"></div>
            
            <div class="absolute inset-0 hero-overlay"></div>

            <div class="relative h-full">
                <?php include 'navigation.php'; ?>
                
                <div class="container mx-auto px-6 h-[85%] flex flex-col justify-center">
                    <div class="max-w-3xl mx-auto text-center">
                        <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 tracking-tight leading-tight">
                            IT'S GROUP FITNESS REINVENTED
                        </h1>
                        <p class="text-xl text-gray-200 mb-8 max-w-2xl mx-auto">
                            Transform your body, elevate your mind. Join the revolution in fitness training.
                        </p>
                        <div class="flex flex-wrap justify-center gap-4">
                            <a href="#programs" class="px-6 py-3 bg-accent hover:bg-[#a5c104] text-black font-semibold rounded-lg transition-all duration-300 flex items-center gap-2">
                                Read More 
                                <div class="h-2 w-2 bg-black rounded-full"></div>
                            </a>
                            <a href="#contact" class="px-6 py-3 bg-white hover:bg-gray-100 text-dark font-semibold rounded-lg transition-all duration-300">
                                CONTACT US
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Marquee Banner -->
                <div class="absolute bottom-0 w-full bg-accent py-3 overflow-hidden whitespace-nowrap">
                    <div class="marquee-container flex gap-5">
                        <div class="marque flex gap-3 items-center">
                            <h1 class="font-semibold">YOGA SERVICE GYM</h1>
                            <div class="h-2 w-2 rounded-full bg-black"></div>
                        </div>
                        <div class="marque flex gap-3 items-center">
                            <h1 class="font-semibold">HEALTH AND GYM</h1>
                            <div class="h-2 w-2 rounded-full bg-black"></div>
                        </div>
                        <div class="marque flex gap-3 items-center">
                            <h1 class="font-semibold">FITNESS AND GYM</h1>
                            <div class="h-2 w-2 rounded-full bg-black"></div>
                        </div>
                        <div class="marque flex gap-3 items-center">
                            <h1 class="font-semibold">PURE GYM SPACE</h1>
                            <div class="h-2 w-2 rounded-full bg-black"></div>
                        </div>
                        
                        <!-- Duplicates for Seamless Loop -->
                        <div class="marque flex gap-3 items-center">
                            <h1 class="font-semibold">YOGA SERVICE GYM</h1>
                            <div class="h-2 w-2 rounded-full bg-black"></div>
                        </div>
                        <div class="marque flex gap-3 items-center">
                            <h1 class="font-semibold">HEALTH AND GYM</h1>
                            <div class="h-2 w-2 rounded-full bg-black"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<!--         <!-- Products Section -->
        <div class="py-20 px-6 border-b border-gray-200" id="products">
            <div class="container mx-auto max-w-7xl">
                <div class="mb-16 text-center">
                    <h2 class="text-4xl md:text-5xl font-bold mb-4 text-dark">Our Products</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Discover our premium fitness equipment and accessories designed for optimal performance.</p>
                </div>
                
                <div class="relative">
                    <!-- Navigation Controls -->
                    <div class="absolute -top-14 right-0 flex space-x-3 z-10">
                    <button id="prevBtn" class="p-3 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 transition-all duration-300">
                        <i class="ri-arrow-left-s-line text-xl"></i>
                    </button>
                    <button id="nextBtn" class="p-3 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 transition-all duration-300">
                        <i class="ri-arrow-right-s-line text-xl"></i>
                    </button>
                    </div>

                    <!-- Slider Container with Backdrop Blur and Gradient -->
                    <div id="sliderContainer" class="overflow-x-hidden scrollbar-hide relative">
                    <div class="absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-slate-50 to-transparent z-10"></div>
                    <div class="absolute inset-y-0 right-0 w-16 bg-gradient-to-l from-slate-50 to-transparent z-10"></div>
                    
                    <div id="imageSlider" class="flex gap-6 transition-transform duration-500 ease-out py-4">
                        <!-- Slides will be dynamically inserted here -->
                    </div>
                    </div>
                    
                    <!-- Pagination Dots -->
                    <div class="flex justify-center mt-8 gap-2" id="paginationDots">
                    <!-- Dots will be dynamically inserted here -->
                    </div>
                </div>
            </div>
        </div> -->
        
        <!-- BMI Calculator Section -->
        <div class="py-20 px-6 bg-white" id="bmi">
            <div class="container mx-auto max-w-7xl">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-4 text-dark">BMI Calculator</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Check your Body Mass Index and understand what it means for your health.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- BMI Form -->
                    <div class="bg-slate-50 rounded-2xl p-8 shadow-lg">
                        <h3 class="text-2xl font-bold mb-6">Calculate Your BMI</h3>
                        <p class="text-gray-600 mb-6">
                            Body Mass Index (BMI) is a measure used to determine if a person's weight is in a healthy range.
                        </p>
                        
                        <form action="" class="mt-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <div class="border border-gray-200 px-4 py-3 bg-white rounded-lg shadow-sm">
                                    <label for="height" class="block text-xs text-gray-500 mb-1">Height (Inches)</label>
                                    <input type="number" id="height" placeholder="Enter height" class="w-full bg-transparent outline-none text-gray-800">
                                </div>
                                <div class="border border-gray-200 px-4 py-3 bg-white rounded-lg shadow-sm">
                                    <label for="weight" class="block text-xs text-gray-500 mb-1">Weight (lbs)</label>
                                    <input type="number" id="weight" placeholder="Enter weight" class="w-full bg-transparent outline-none text-gray-800">
                                </div>
                            </div>
                
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="border border-gray-200 px-4 py-3 bg-white rounded-lg shadow-sm">
                                    <label for="age" class="block text-xs text-gray-500 mb-1">Age</label>
                                    <input type="number" id="age" placeholder="Enter age" class="w-full bg-transparent outline-none text-gray-800">
                                </div>
                                <div class="border border-gray-200 px-4 py-3 bg-white rounded-lg shadow-sm">
                                    <label for="gender" class="block text-xs text-gray-500 mb-1">Gender</label>
                                    <select id="gender" class="w-full bg-transparent outline-none text-gray-800">
                                        <option value="">Select gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                
                            <button type="button" onclick="calculateBMI()" 
                                class="mt-8 flex gap-2 px-6 py-3 bg-primary hover:bg-indigo-600 text-white font-semibold rounded-lg transition-colors">
                                <span>Calculate BMI</span>
                                <i class="fas fa-calculator"></i>
                            </button>
                        </form>
                        
                        <div id="result" class="mt-6 p-4 bg-white rounded-lg border border-gray-200 hidden">
                            <h4 class="font-bold text-lg mb-2">Your Result:</h4>
                            <div id="bmi-value" class="text-3xl font-bold text-primary"></div>
                            <div id="bmi-category" class="mt-2 font-medium"></div>
                        </div>
                    </div>
                    
                    <!-- BMI Information -->
                    <div class="bg-gradient-to-br from-primary to-indigo-700 text-white rounded-2xl p-8 shadow-lg">
                        <h3 class="text-2xl font-bold mb-8">BMI Categories</h3>
                        
                        <div class="space-y-6">
                            <div class="flex justify-between items-center border-b border-white/20 pb-3">
                                <div class="font-medium">Below 18.5</div>
                                <div class="font-bold bg-white/20 py-1 px-4 rounded-full">Underweight</div>
                            </div>
                            
                            <div class="flex justify-between items-center border-b border-white/20 pb-3">
                                <div class="font-medium">18.5 - 24.9</div>
                                <div class="font-bold bg-accent/80 text-dark py-1 px-4 rounded-full">Healthy</div>
                            </div>
                            
                            <div class="flex justify-between items-center border-b border-white/20 pb-3">
                                <div class="font-medium">25 - 29.9</div>
                                <div class="font-bold bg-white/20 py-1 px-4 rounded-full">Overweight</div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <div class="font-medium">30.0 & Above</div>
                                <div class="font-bold bg-white/20 py-1 px-4 rounded-full">Obese</div>
                            </div>
                        </div>
                        
                        <div class="mt-12 bg-white/10 p-6 rounded-xl">
                            <h4 class="text-xl font-bold mb-4">What does BMI tell you?</h4>
                            <p class="text-white/80">
                                BMI is a measurement of a person's leanness or corpulence based on their height and weight, and is intended to quantify tissue mass. It is widely used as a general indicator of whether a person has a healthy body weight for their height.
                            </p>
                            <div class="mt-4 flex items-center">
                                <span class="mr-2 text-white/80">BMR</span>
                                <span class="font-bold">Metabolic Rate</span>
                                <span class="mx-2 text-white/80">/</span>
                                <span class="mr-2 text-white/80">BMI</span>
                                <span class="font-bold">Body Mass Index</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- CTA Section -->
        <div class="py-20 px-6 bg-gradient-to-r from-primary to-indigo-800 text-white">
            <div class="container mx-auto max-w-7xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h2 class="text-4xl font-bold mb-4">Ready to Start Your Fitness Journey?</h2>
                        <p class="text-xl opacity-90 mb-8">Join our community today and transform your body with expert guidance and support.</p>
                        <div class="flex flex-wrap gap-4">
                            <a href="#membership" class="px-6 py-3 bg-accent hover:bg-[#a5c104] text-black font-semibold rounded-lg transition-all duration-300">
                                Join Now
                            </a>
                            <a href="#programs" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-lg transition-all duration-300 backdrop-blur-sm">
                                View Programs
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:flex justify-end">
                        <div class="w-72 h-72 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-dumbbell text-6xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"
    integrity="sha512-7eHRwcbYkK4d9g/6tD/mhkf++eoTHwpNM9woBxtPUBWm67zeAfFC+HrdoE2GanKeocly/VxeLvIqwvCdk7qScg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <script>
        // Product slider implementation
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('imageSlider');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const paginationDots = document.getElementById('paginationDots');
            
            // Example products - replace with your actual products
            const products = [
                { name: "Fitness Mat", image: "https://images.unsplash.com/photo-1518611012118-696072aa579a?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80", price: "$29.99" },
                { name: "Resistance Bands", image: "https://images.unsplash.com/photo-1517344884509-a0c97ec11bcc?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80", price: "$19.99" },
                { name: "Kettlebell Set", image: "https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80", price: "$89.99" },
                { name: "Protein Powder", image: "https://images.unsplash.com/photo-1563805042-7684c019e1cb?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80", price: "$39.99" },
                { name: "Smart Water Bottle", image: "https://images.unsplash.com/photo-1523362628745-0c100150b504?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80", price: "$49.99" },
                { name: "Jump Rope", image: "https://images.unsplash.com/photo-1515238152791-8216bfdf89a7?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80", price: "$12.99" },
            ];
            
            // Create product cards
            products.forEach((product, index) => {
                const card = document.createElement('div');
                card.className = 'min-w-[280px] bg-white rounded-2xl shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105';
                card.innerHTML = `
                    <div class="h-44 overflow-hidden">
                        <img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">${product.name}</h3>
                        <div class="flex justify-between items-center">
                            <span class="text-primary font-bold">${product.price}</span>
                            <button class="bg-primary hover:bg-indigo-600 text-white py-2 px-4 rounded-lg text-sm transition-colors">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                `;
                slider.appendChild(card);
                
                // Create pagination dot
                const dot = document.createElement('button');
                dot.className = 'h-3 w-3 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors';
                dot.onclick = () => goToSlide(index);
                paginationDots.appendChild(dot);
            });
            
            // Slider functionality
            let currentSlide = 0;
            const slideWidth = 300; // Width of each slide plus gap
            
            function updateSlider() {
                slider.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
                
                // Update pagination dots
                Array.from(paginationDots.children).forEach((dot, index) => {
                    if (index === currentSlide) {
                        dot.classList.add('bg-primary');
                        dot.classList.remove('bg-gray-300');
                    } else {
                        dot.classList.add('bg-gray-300');
                        dot.classList.remove('bg-primary');
                    }
                });
            }
            
            function goToSlide(index) {
                currentSlide = Math.min(Math.max(index, 0), products.length - 1);
                updateSlider();
            }
            
            prevBtn.addEventListener('click', () => {
                currentSlide = Math.max(currentSlide - 1, 0);
                updateSlider();
            });
            
            nextBtn.addEventListener('click', () => {
                currentSlide = Math.min(currentSlide + 1, products.length - 1);
                updateSlider();
            });
            
            // Initialize slider
            updateSlider();
        });
        
        // BMI Calculator
        function calculateBMI() {
            const height = parseFloat(document.getElementById('height').value);
            const weight = parseFloat(document.getElementById('weight').value);
            
            if (isNaN(height) || isNaN(weight) || height <= 0 || weight <= 0) {
                alert('Please enter valid height and weight values.');
                return;
            }
            
            // BMI calculation (imperial formula)
            const bmi = (weight / (height * height)) * 703;
            const bmiRounded = Math.round(bmi * 10) / 10;
            
            let category;
            let colorClass;
            
            if (bmi < 18.5) {
                category = 'Underweight';
                colorClass = 'text-blue-500';
            } else if (bmi >= 18.5 && bmi < 25) {
                category = 'Healthy Weight';
                colorClass = 'text-green-500';
            } else if (bmi >= 25 && bmi < 30) {
                category = 'Overweight';
                colorClass = 'text-yellow-500';
            } else {
                category = 'Obese';
                colorClass = 'text-red-500';
            }
            
            const resultDiv = document.getElementById('result');
            const bmiValueDiv = document.getElementById('bmi-value');
            const bmiCategoryDiv = document.getElementById('bmi-category');
            
            resultDiv.classList.remove('hidden');
            bmiValueDiv.textContent = bmiRounded;
            bmiValueDiv.className = `text-3xl font-bold ${colorClass}`;
            bmiCategoryDiv.textContent = category;
            bmiCategoryDiv.className = `mt-2 font-medium ${colorClass}`;
        }
        
        // Marquee animation
        const marquee = document.querySelector('.marquee-container');
        gsap.to(marquee, {
            x: "-50%",
            repeat: -1,
            duration: 15,
            ease: "linear"
        });
    </script>
</body>
</html>
