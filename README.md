# NutriTrack 2025

NutriTrack 2025 is your ultimate fitness and nutrition companion. This web application helps users track their fitness goals, monitor progress, and stay motivated through challenges, analytics, and rewards. It is designed for individuals, fitness enthusiasts, and administrators to manage and participate in fitness-related activities.

## Features

### User Features
- **Dashboard**: Personalized dashboard to track calories consumed, burned, and net balance with interactive charts.
- **Challenges**: Join and complete fitness challenges categorized by difficulty and type (e.g., cardio, nutrition, wellness).
- **Food Log**: Log daily meals with details like meal type, food name, and calorie count.
- **Workout Log**: Track workouts with details like intensity, duration, and notes.
- **Milestone Achievements**: Earn badges and points for completing challenges and achieving fitness goals.
- **Progress Analytics**: Visualize progress over time with detailed analytics and charts.

### Admin Features
- **Manage Challenges**: Create, edit, and delete fitness challenges with options for difficulty, category, and rewards.
- **User Management**: View and manage user accounts and their activities.
- **Food and Workout Logs**: Monitor food and workout entries submitted by users.
- **Analytics Dashboard**: View monthly statistics for new users, food entries, and workout entries.

### Additional Features
- **Responsive Design**: Fully responsive UI built with Tailwind CSS for seamless use across devices.
- **Interactive Charts**: Dynamic charts powered by Chart.js for data visualization.
- **Secure Authentication**: User authentication with session management.
- **Customizable Themes**: Tailwind CSS configuration for easy theme customization.

## Installation

1. Clone the repository to your local machine:
   ```bash
   git clone https://github.com/your-repo/nutritrack2025.git
   ```
2. Move to the project directory:
   ```bash
   cd nutritrack2025
   ```
3. Set up a local server (e.g., XAMPP) and place the project in the `htdocs` folder.
4. Import the database:
   - Open `phpMyAdmin`.
   - Create a new database (e.g., `nutritrack`).
   - Import the provided SQL file into the database.
5. Configure the database connection in `config.php`:
   ```php
   // filepath: c:\xampp\htdocs\project\config.php
   $db_host = 'localhost';
   $db_user = 'root';
   $db_pass = '';
   $db_name = 'nutritrack';
   ```
6. Start the local server and access the application at `http://localhost/project`.

## Usage

### For Users
1. Register or log in to your account.
2. Navigate to the dashboard to view your progress.
3. Join challenges, log food and workouts, and earn rewards.

### For Admins
1. Log in with admin credentials.
2. Manage challenges, users, and logs through the admin panel.
3. View analytics to monitor user engagement and activity.

## Technologies Used
- **Frontend**: HTML, CSS, Tailwind CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Charts**: Chart.js
- **Icons**: Font Awesome

## Screenshots
- **Dashboard**: Interactive charts and progress tracking.
- **Challenges**: List of challenges with filters for category and difficulty.
- **Admin Panel**: Manage challenges, users, and logs.

## Contributing
Contributions are welcome! Please fork the repository and submit a pull request with your changes.

## License
This project is licensed under the MIT License. See the `LICENSE` file for details.

## Contact
For any inquiries or support, please contact us at [info@fittrack2025.com](mailto:info@fittrack2025.com).
