<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->

    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
     
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />

</head>

<body>
    <div id="main" class="w-[100%]">
        <div id="page1"
            class="h-[75vh] relative md:min-h-screen bg-[url(https://plus.unsplash.com/premium_photo-1661265933107-85a5dbd815af?q=80&w=2018&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D)] w-full object-contain bg-cover bg-center bg-no-repeat">

            <div id="nav" class="flex  p-[2vw] sm:p-[1.6vw] items-center justify-between    ">
                <h1 class="cursor-pointer sm:text-[2.5vw] text-[9vw] text-white"><span
                        class="font-bold text-white">F</span>i<span class="text-[#bfe305]">t</span>ne<span
                        class="text-[#bfe305]">ss</span></h1>
                <div class="hidden sm:text-white   sm:block sm:flex sm:items-center sm:gap-[1.3vw]">
                    <h4
                        class="text-[1.2vw] text-grey-700 hover:text-[#bfe305] font-semibold transition-colors duration-300 ease-in-out cursor-pointer ">
                        About</h4>
                    <h4
                        class="text-[1.2vw] text-grey-700 hover:text-[#bfe305] font-semibold transition-colors duration-300 ease-in-out cursor-pointer">
                        Create Task</h4>
                    <h4
                        class="text-[1.2vw] text-grey-700 hover:text-[#bfe305] font-semibold transition-colors duration-300 ease-in-out cursor-pointer">
                        CheckProgress</h4>
                    <h4
                        class="text-[1.2vw] text-grey-700 hover:text-[#bfe305] font-semibold transition-colors duration-300 ease-in-out cursor-pointer">
                        Contact Us</h4>
                </div>
                <div class="flex items-center gap-[1.2vw]">
                    <div>

                        <i id="search" class="cursor-pointer text-white text-[4.6vw] sm:text-[1.6vw] text-grey-600 ri-search-line"></i>
                        <div id="sear" class=" w-[24vw] hidden absolute top-[12%] right-[45%] h-[7vw] sm:right-0 ">
                            <input type="text"
                                class=" sm:text-[1.2vw] sm:py-[1vw] border-2 border-zinc-100 text-white font-bold rounded-[4vw] px-[2vw] py-[2vw]   text-[2.8vw] opacity-[1]"
                                name="" id="" placeholder="search">
                        </div>
                    </div>
                    <div class=" flex items-center py-[.5vw] px-[2vw]   sm:px-[1.4vw]  bg-[#bfe305]">
                        <button class="text-[2.7vw] sm:text-[1.1vw] text-black cursor-pointer font-semibold">JOIN</button>
                    </div>
                </div>
                <i id="btn" class="cursor-pointer ri-menu-line text-[8vw] text-zinc-200  sm:hidden block"></i>

                <i id="cross" class="cursor-pointer ri-close-fill sm:hidden text-zinc-200 hidden text-[9vw]"></i>
            </div>

            <div id="main"
                class="w-[85vw] sm:h-[25vw] sm:w-[45vw]   absolute top-1/2 left-1/2 p-9 -translate-x-1/2 -translate-y-1/2">
                <h1
                    class="text-[7.8vw] tracking-tighter leading-[7vw]  sm:text-[4vw] text-center font-bold sm:tracking-tighter  sm:leading-[4.3vw] text-white">
                    IT'S GROUP
                    FITNESS REINVENTED</h1>
                <div class="flex items-center gap-3 justify-center mt-5">
                    <div class="inline-flex gap-[1vw] p-2   items-center bg-[#bfe305] w-auto">
                        <button class=" ctext-[3.7vw] cursor-pointer sm:text-[1.1vw] font-semibold">Read More </button>
                        <div class=" h-[1.4vw] w-[1.4vw]  sm:h-[.7vw] sm:w-[.7vw] bg-black rounded-full "></div>
                    </div>
                    <div class="inline-flex gap-[1vw] p-2 items-center bg-white w-auto">
                        <button class="cursor-pointer text-[3.7vw] sm:text-[1.1vw] font-semibold">CONTACT US </button>
                    </div>
                </div>
            </div>

            <div
                class="absolute bottom-0 w-full h-[10vh] bg-[#bfe305] flex items-center p-3 overflow-hidden whitespace-nowrap">
                <!-- Marquee Wrapper -->
                <div class="marquee-container flex gap-5">
                    <div class="marque flex gap-3 items-center">
                        <h1 class="font-semibold">YOGA SERVICE GYM</h1>
                        <div class="h-[.7vw] w-[.7vw] rounded-full bg-black"></div>
                    </div>
                    <div class="marque flex gap-3 items-center">
                        <h1 class="font-semibold">HEALTH AND GYM</h1>
                        <div class="h-[.7vw] w-[.7vw] rounded-full bg-black"></div>
                    </div>
                    <div class="marque flex gap-3 items-center">
                        <h1 class="font-semibold">FITNESS AND GYM</h1>
                        <div class="h-[.7vw] w-[.7vw] rounded-full bg-black"></div>
                    </div>
                    <div class="marque flex gap-3 items-center">
                        <h1 class="font-semibold">PURE GYM SPACE</h1>
                        <div class="h-[.7vw] w-[.7vw] rounded-full bg-black"></div>
                    </div>

                    <!-- Duplicates for Seamless Loop -->
                    <div class="marque flex gap-3 items-center">
                        <h1 class="font-semibold">YOGA SERVICE GYM</h1>
                        <div class="h-[.7vw] w-[.7vw] rounded-full bg-black"></div>
                    </div>
                    <div class="marque flex gap-3 items-center">
                        <h1 class="font-semibold">HEALTH AND GYM</h1>
                        <div class="h-[.7vw] w-[.7vw] rounded-full bg-black"></div>
                    </div>
                </div>
            </div>



            <nav id="resnav"
                class="drop-shadow-lg w-2/3  rounded-bl-[2vw]  rounded-tl-[2vw] p-[5vw]     h-[100vh] bg-black sm:hidden absolute right-0   hidden">
                <h4 class="mb-[3vw] text-white  hover:text-grey-600 hover:border-1 hover:border-zinc-500 rounded-xl hover:px-2  cursor-pointer">About</h4>
                <h4 class="mb-[3vw] text-white  hover:text-grey-600 hover:border-1 hover:border-zinc-500 rounded-xl hover:px-2 cursor-pointer">Create Task</h4>
                <h4 class="mb-[3vw] text-white hover:text-grey-600 hover:border-1 hover:border-zinc-500 rounded-xl hover:px-2 cursor-pointer">CheckProgress</h4>
                <h4 class="mb-[3vw] text-white hover:text-grey-600 hover:border-1 hover:border-zinc-500 rounded-xl hover:px-2 cursor-pointer">Contact Us</h4>
            </nav>
        </div>
        <div class="h-[80vh] md:h-screen p-[4vw]  relative">
            <h1 class="text-center mb-6 md:mb-4 text-[7vw] md:text-[3.5vw] font-bold ">Our Product</h1>
            <div class="absolute top-[15vw] right-6 md:right-9  md:top-[7vw]  ">
                 <button id="leftbtn" class="cursor-pointer  text-[6.5vw] md:text-[2.5vw]"><i class="ri-arrow-left-circle-fill"></i></button>
                 <button id="rightbtn" class="cursor-pointer text-[6.5vw] md:text-[2.5vw]"><i class="ri-arrow-right-circle-fill"></i></button>
            </div>
            <div id="imageContainer" class="imagecont flex space-x-2 p-4 overflow-x-scroll scroll-smooth ">

            </div>
        </div>
        <div id="page3" class="w-full h-[80vh]   md:flex">
            <div id="part1" class="flex-1 bg-pink h-full w-full  px-[3vw] py-[4vw] ">
                <div class="px-4  ">
                    <h1 class="text-[4vw] font-bold">WHAT IS BMI.</h1>
                    <p class="text-sm opacity-58">It is a long established fact that the reader will be distracted by the readable content of the page when looking 
                        at its page when at its layout.
                    </p>
                    <form action="" class="mt-6">
                        <div class="flex items-center   gap-3 mb-4">
    
                            <div class="border-1 w-full border-zinc-200 px-2 py-2 bg-zinc-200 text-sm font-semibold rounded"><input type="number" placeholder="Height/Inch" name="" id="height"></div>
                             <div class="border-1 w-full border-zinc-200 px-2 py-2 bg-zinc-200 text-sm font-semibold rounded"><input type="number" placeholder="Weight" name="" id="weight"></div>
                        </div>
                        <div class="flex items-center  gap-3">
    
                            <div class="border-1 w-full border-zinc-200 px-2 py-2 bg-zinc-200 text-sm font-semibold rounded"><input type="number" placeholder="Age" name="" id="age"></div>
                             <div class="border-1 w-full border-zinc-200 px-2 py-2 bg-zinc-200 text-sm font-semibold rounded"><input type="text" placeholder="Gender" name="" id=""></div>
                        </div>
                        <button type="button" onclick="calculateBMI()" class="flex gap-2 px-3 py-2 bg-[#bfe305] w-fit items-center justify-center mt-5 rounded">
                            <span class="text-sm opacity-[.8] font-semibold">Calculate</span>
                            <div class="h-2 w-2 bg-black rounded-full"></div>
                        </button>
                        <!-- <input type="text" placeholder="Weight/lbs" name="" class="border-1 border-zinc-200 py-2 px-1 bg-zinc-300" id=""> -->
                    </form>

                </div>
                <div id="result" class="mt-4 font-semibold text-sm ml-5"></div>
            </div>
            <div id="part2" class="flex-1 bg-pink h-full w-full   px-[3vw] py-[4vw]">
                <div class="px-4   flex gap-3">
                    <div class="w-1/2">
                        <h1 class="font-bold text-[1.4vw] mb-7">BMI</h1>
                        <h3 class="text-sm mb-6 opacity-[.6]">Below 18.5</h3>
                        <h3 class="text-sm mb-6 opacity-[.6]">18.5-24.9</h3>
                        <h3 class="text-sm mb-6 opacity-[.6]">25-29.9</h3>
                        <h3 class="text-sm mb-6 opacity-[.6]">30.0-above</h3>
                    </div>
                    <div class="w-1/2">
                        <h1 class="font-bold text-[1.4vw] mb-7">WEIGHT STATUS</h1>
                        <h3 class="text-sm mb-6 opacity-[.6]">Underweight</h3>
                        <h3 class="text-sm mb-6 opacity-[.6]">Healthy</h3>
                        <h3 class="text-sm mb-6 opacity-[.6]">Overweight</h3>
                        <h3 class="text-sm mb-6 opacity-[.6]">Obese</h3>
                    </div>
                </div>
                <h4 class="text-sm opacity-[.6]"><span class="font-bold">BMR</span> Metabolic Rate / <span class="font-bold">BMI</span> Body Mass Index</h4>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"
    integrity="sha512-7eHRwcbYkK4d9g/6tD/mhkf++eoTHwpNM9woBxtPUBWm67zeAfFC+HrdoE2GanKeocly/VxeLvIqwvCdk7qScg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="script.js"></script> 
</body>

</html>