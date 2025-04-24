<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
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
        .gradient-text {
    background: linear-gradient(45deg, #4F46E5, #10B981);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.glass-effect {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.hover-scale {
    transition: transform 0.3s ease;
}
.hover-scale:hover {
    transform: scale(1.02);
}

.shadow-custom {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.text-shadow {
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.button-glow:hover {
    box-shadow: 0 0 15px rgba(79, 70, 229, 0.6);
}

.section-divider {
    position: relative;
    height: 4px;
    background: linear-gradient(90deg, #4F46E5, #10B981);
    border-radius: 2px;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}

html, body {
    scroll-behavior: smooth;
    line-height: 1.6;
}


h1, h2, h3, h4, h5, h6 {
    letter-spacing: -0.025em;
}


button, .button, a[href].button {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

button:hover, .button:hover, a[href].button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}


input, select, textarea {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

input:focus, select:focus, textarea:focus {
    border-color: #4F46E5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
}


.container {
    transition: all 0.3s ease;
}


.rounded-2xl {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.rounded-2xl:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}


nav a {
    position: relative;
    transition: all 0.3s ease;
}

nav a:after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: #4F46E5;
    transition: width 0.3s ease;
}
.alag{
    background-color: #003366;
background-image: linear-gradient(315deg, #003366 0%, #242124 74%);

}
nav a:hover:after {
    width: 100%;
}

.bg-gradient {
    background: linear-gradient(135deg, #4F46E5, #10B981);
}


.transition-custom {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.hhh{
    background-color: #50d8d7;
background-image: linear-gradient(316deg, #50d8d7 0%, #923993 74%);


}
.hover-lift {
    transition: transform 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-4px);
}
.card-hover-effect {
    transition: all 0.3s ease;
}
.card-hover-effect:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}
    </style>
</head>


<body class="bg-slate-50 font-sans">
    <div id="main" class="w-full">
        <!-- Hero Section -->
        <div class="relative h-screen">
            <div class="absolute inset-0 bg-cover s:bg-center  bg-no-repeat" style="background-image: url('https://i0.wp.com/www.muscleandfitness.com/wp-content/uploads/2017/06/dwayne-johnson-collection-ua-6-11090.jpg?quality=86&strip=all');"></div>
            
            <div class="absolute inset-0 hero-overlay"></div>

            <div class="relative h-full">
                <?php include 'navigation.php'; ?>
                
                <div class="container mx-auto px-6 h-[85%] flex flex-col justify-center">
                    <div class="max-w-3xl mx-auto text-center glass-effect p-8 rounded-xl">
                        <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 tracking-tight leading-tight text-shadow">
                            The Greatest Wealth Is Health
                        </h1>
                        <p class="text-xl text-gray-200 mb-8 max-w-2xl mx-auto">
                        The ultimate fitness challenge tracker for achieving your health and wellness goals
                        </p>
                        <div class="flex flex-wrap justify-center gap-4">
                            <a href="read.php" class="hhh px-6 py-3 bg-accent hover:bg-gray-100 text-amber-100  font-semibold  hover:font-extrabold hover:text-white rounded-lg transition-all duration-100 flex items-center gap-2 button-glow hover-scale">
                                Read More 
                                <div class="h-2 w-2 bg-black rounded-full"></div>
                            </a>
                            <a href="contact.php" class="hhh px-6 py-3 bg-white hover:bg-gray-100 text-amber-100 hover:text-white font-semibold hover:font-extrabold button-glow rounded-lg hover-scale transition-all duration-100">
                                CONTACT US
                            </a>
                        </div>
                    </div>
                </div>
                <style>
                    .troll {
                        background: rgb(176,224,230);
background: linear-gradient(159deg, rgba(176,224,230,1) 0%, rgba(70,130,180,1) 100%);
                    }
                    </style>
                
                <!-- Marquee Banner -->
                <div class="absolute bottom-0 w-full troll py-4 overflow-hidden whitespace-nowrap shadow-lg">
                    <div class="marquee-container flex gap-8 items-center">
                        <div class="marque flex gap-4 items-center">
                             <i class="ri-heart-line text-xl"></i>
                            <h1 class="font-bold text-dark tracking-wider hover:text-white transition-colors">Take care of your body.</h1>
                            <div class="h-2 w-2 rounded-full bg-dark animate-pulse"></div>
                        </div>
                        <div class="marque flex gap-4 items-center">
                            <i class="ri-run-line text-xl"></i>
                            <h1 class="font-bold text-dark tracking-wider hover:text-white transition-colors">The only bad workout is the one that didn't happen.</h1>
                            <div class="h-2 w-2 rounded-full bg-dark animate-pulse"></div>
                        </div>
                        <div class="marque flex gap-4 items-center">
                            <i class="ri-lightbulb-line text-xl"></i>
                            <h1 class="font-bold text-dark tracking-wider hover:text-white transition-colors">You have to think about it before you can do it.</h1>
                            <div class="h-2 w-2 rounded-full bg-dark animate-pulse"></div>
                        </div>
                        <div class="marque flex gap-4 items-center">
                            <i class="ri-run-line text-xl"></i>
                            <h1 class="font-bold text-dark tracking-wider hover:text-white transition-colors">You did not wake up today to be mediocre.</h1>
                            <div class="h-2 w-2 rounded-full bg-dark animate-pulse"></div>
                        </div>
                        <div class="marque flex gap-4 items-center">
                            <i class="ri-sun-line text-xl"></i>
                            <h1 class="font-bold text-dark tracking-wider hover:text-white transition-colors">You have to think about it before you can do it.</h1>
                            <div class="h-2 w-2 rounded-full bg-dark animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
       
        
        <!-- BMI Calculator Section -->
        <div class="py-20 px-6" id="bmi" style="background-image: url('https://png.pngtree.com/background/20220716/original/pngtree-tiktok-the-publicity-background-of-national-fitness-campaign-picture-image_1631407.jpg'); background-size: cover; background-position: center;">
            <div class="container mx-auto max-w-7xl">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl text-white font-bold mb-4 text-dark">BMI Calculator</h2>
                    <p class="text-gray-600 max-w-2xl text-white mx-auto">Check your Body Mass Index and understand what it means for your health.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- BMI Form -->
                    <div class="alag rounded-2xl p-8 shadow-lg" >
                        <h3 class="text-2xl text-white  font-bold mb-6">Calculate Your BMI</h3>
                        <p class="text-white mb-6">
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
            <input type="number" id="age" placeholder="Enter age" required class="w-full bg-transparent outline-none text-gray-800">
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

<!-- BMI Result Output -->
<div id="result" class="hidden mt-6">
    <div id="bmi-value" class="text-lg font-bold"></div>
    <div id="bmi-category" class="mt-2 font-medium"></div>
</div>

<!-- ✅ Correct Script Tag -->
<script>
    function calculateBMI() {
        const height = parseFloat(document.getElementById('height').value);
        const weight = parseFloat(document.getElementById('weight').value);
        const age = parseInt(document.getElementById('age').value);

        if (isNaN(height) || isNaN(weight) || isNaN(age) || height <= 0 || weight <= 0 || age <= 0) {
            alert('Please enter valid height, weight, and age values.');
            return;
        }

        const bmi = (weight / (height * height)) * 703;
        const bmiRounded = Math.round(bmi * 10) / 10;

        const resultDiv = document.getElementById('result');
        const bmiValueDiv = document.getElementById('bmi-value');
        const bmiCategoryDiv = document.getElementById('bmi-category');

        let category;
        let colorClass;

        if (age >= 18) {
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
        } else {
            category = 'BMI-for-age required. Consult growth charts or a doctor.';
            colorClass = 'text-purple-500';
        }

        resultDiv.classList.remove('hidden');
        bmiValueDiv.textContent = `BMI: ${bmiRounded}`;
        bmiValueDiv.className = `text-3xl font-bold ${colorClass}`;
        bmiCategoryDiv.textContent = category;
        bmiCategoryDiv.className = `mt-2 font-medium ${colorClass}`;
    }
</script>

                        
                        <div id="result" class="mt-6 p-4 bg-white rounded-lg border border-gray-200 hidden">
                            <h4 class="font-bold text-lg mb-2">Your Result:</h4>
                            <div id="bmi-value" class="text-3xl font-bold text-primary"></div>
                            <div id="bmi-category" class="mt-2 font-medium"></div>
                        </div>
                    </div>
                    
                    <!-- BMI Information -->
                    <div class="alag text-white rounded-2xl p-8 shadow-lg">
                        <h3 class="text-2xl font-bold mb-8">BMI Categories</h3>
                        
                        <div class="space-y-6">
                            <div class="flex justify-between items-center border-b border-white/20 pb-3">
                                <div class="font-medium">Below 18.5</div>
                                <div class="font-bold bg-white/20 py-1 px-4 rounded-full">Underweight</div>
                            </div>
                            
                            <div class="flex justify-between items-center border-b border-white/20 pb-3">
                                <div class="font-medsium">18.5 - 24.9</div>
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
        <div class="py-20 px-6 bg-gradient-to-tl from-blue-600 to-blue-950 text-white">
            <div class="container mx-auto max-w-7xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h2 class="text-4xl font-bold mb-4">Ready to Start Your Fitness Journey?</h2>
                        <p class="text-xl opacity-90 mb-8">Join our community today and transform your body with ultimate tracking and anlysis of your fitness.</p>
                        <p class="text-xl opacity-90 mb-4">* If not registerd yet</p>

                        <div class="flex flex-wrap gap-4">
                            <a href="register.php" class="px-6 py-3 bg-accent hover:bg-[#a5c104] text-black font-semibold rounded-lg transition-all duration-300">
                                Join Now
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

