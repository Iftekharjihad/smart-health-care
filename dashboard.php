<?php
// dashboard.php - Example dashboard with session check
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Get user info from session
$user_name = $_SESSION['full_name'];
$user_role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Smart Health Care</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .dashboard-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 90%;
            max-width: 1200px;
            overflow: hidden;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .user-info p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            border: 2px solid white;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .logout-btn:hover {
            background: white;
            color: #667eea;
        }
        
        .dashboard-content {
            padding: 40px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .dashboard-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .dashboard-card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        
        .dashboard-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .role-badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-left: 10px;
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            
            .user-info h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="user-info">
                <h1>Welcome, <?php echo htmlspecialchars($user_name); ?> 
                    <span class="role-badge"><?php echo ucfirst($user_role); ?></span>
                </h1>
                <p>Smart Health Care Dashboard</p>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        
        <div class="dashboard-content">
            <div class="dashboard-card">
                <h3>📊 Statistics</h3>
                <p>View your health statistics, appointments, and medical records.</p>
            </div>
            
            <div class="dashboard-card">
                <h3>👨‍⚕️ Appointments</h3>
                <p>Schedule, view, or cancel your medical appointments.</p>
            </div>
            
            <div class="dashboard-card">
                <h3>📋 Medical Records</h3>
                <p>Access your complete medical history and prescriptions.</p>
            </div>
            
            <div class="dashboard-card">
                <h3>⚙️ Settings</h3>
                <p>Update your profile, change password, and manage preferences.</p>
            </div>
        </div>
    </div>
</body>
</html>