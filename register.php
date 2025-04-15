<?php
    session_start();
    include 'db.php';
    if(isset($_SESSION['email'])) {
        header("Location: dashboard.php");
        exit();
    }
    if($_SERVER["REQUEST_METHOD"] == "POST"){
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
    
        $sql = "INSERT INTO users(firstname, lastname, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $password);
    
        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        $conn->close();
    }
?>
<!doctype html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
  </head>
  <body>
    <div id="cont" class="h-screen w-[100%]  bg-radial-[at_25%_25%] from-white to-zinc-900 to-70% relative overflow-hidden">
        <?php include 'navigation.php'; ?>
        <div id="form" class="h-[73%] w-[69%]   absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 sm:flex overflow-hidden rounded-[3vw] sm:rounded-[1vw]">
            <div id="left" class="hidden sm:block sm:w-[50%] sm:h-[100%] p-[1.8vw]  sm:bg-cover sm:bg-no-repeat sm:bg-center sm:bg-[url(https://t3.ftcdn.net/jpg/05/81/04/30/360_F_581043016_tcn4H3NC4TtJZBf3FUi3pgjFN1vKNuFv.jpg)]">
                <h1 class="text-[2.7vw] font-bold mb-[1.3vw]">Hello, </h1>
                <div id="motivate" class=" w-[100%]">
                    <p class="font-semibold text-[1.2vw]">"Sign up today and take the first step towards a healthier, fitter you. Compete with friends, track your workouts, and stay motivated!"</p>
                </div>
               <a href="login.php"> <button class="border border-zinc-800 text-white font-semibold px-[2.6vw] py-[.5vw] bg-zinc-800 rounded-[1.6vw] mt-[1.4vw]">Login</button></a> 
            </div>
            <div id="right" class="w-[100%] h-[100%] bg-zinc-200 sm:w-[50%] sm:h-[100%]   relative"> 
                    <form action="register.php" method="POST" class="w-[95%] h-[95%]   absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                        <h1 class="uppercase text-[4vw] sm:text-[1.7vw] font-bold text-center underline">Sign up</h1>
                        <div id="div" class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-[2vw] w-[100%] mt-[3vw] sm:mt-[1.6vw]  ">
                            <div id="div" class="flex flex-col items-center sm:items-start sm:w-[50%]  mb-[2vw] sm:mb-0  ">
                                <h3 class="text-[3.3vw] font-[600]  sm:text-[1.2vw]  ">First Name</h3>
                                <input type="text" name="firstname" placeholder="Enter first name" class="w-[80%]   opacity-[.7] p-[2vw] sm:p-[.5vw] sm:text-[1.2vw] text-[2vw] border-1 border-zinc-300 sm:border-zinc-400 sm:rounded-[1vw] rounded-[4vw]">
                            </div> 
                            <div id="div left" class="flex flex-col items-center    sm:items-end sm:w-[50%] ">
                                <h3 class="text-[3.3vw]  font-[600] sm:text-[1.2vw] ">Last Name</h3>
                                <input type="text" name="lastname" placeholder="Enter last name" class="w-[80%] sm:p-[.5vw] sm:text-[1.2vw] opacity-[.7] p-[2vw] text-[2vw] border-1 border-zinc-300 sm:border-zinc-400 rounded-[4vw] sm:rounded-[1vw]">
                            </div>
                        </div>
                        <div id="div" class="flex flex-col items-center mt-[2vw] sm:mt-0">
                            <h3 class="text-[3.3vw] sm:text-[1.2vw] font-[600]">Email Adddress</h3>
                            <input type="email" name="email"  placeholder="Enter email" class="w-[80%] sm:w-[100%] opacity-[.7] sm:p-[.5vw] sm:text-[1.2vw] p-[2vw] text-[2vw] border-1 border-zinc-300 sm:border-zinc-400 rounded-[4vw] sm:rounded-[1vw]">
                        </div>
                        <div id="div" class="flex flex-col items-center mt-[2vw] sm:mt-[1.6vw]">
                            <h3 class="text-[3.3vw] sm:text-[1.2vw] font-[600]">Enter Password</h3>
                            <input type="password" name="password" placeholder="Enter password" class=" sm:w-[100%] w-[80%] opacity-[.7] sm:p-[.5vw] sm:text-[1.2vw] p-[2vw] text-[2vw] border-1 border-zinc-300 sm:border-zinc-400 rounded-[4vw] sm:rounded-[1vw]">
                        </div>
                       
                        <div class="flex items-center justify-center mt-[7vw] sm:mt-[3vw]"><button type="submit" class="text-[4vw] sm:text-[1.3vw] font-semibold sm:w-[100%]  w-[80%] sm:py-[.6vw] border border-zinc-400 text-center rounded-[4vw]">Register</button></div> 

                    </form>

 
            </div>
        </div>
        
 
    </div>

    </div>
  </body>
</html>