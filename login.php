<?php
// login.php - Login page with authentication
session_start();
require_once 'db_connection.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Validate inputs
    if (empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required!";
    } else {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, full_name, email, password, role FROM users WHERE email = ? AND role = ?");
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin_dashboard.php");
                        break;
                    case 'doctor':
                        header("Location: doctor_dashboard.php");
                        break;
                    case 'patient':
                        header("Location: patient_dashboard.php");
                        break;
                    default:
                        header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "No account found with these credentials!";
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login • Smart Health Care</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body class="auth-body">
    <div class="auth-card">
        <h2>Log In</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="">Select role</option>
                <option value="patient" <?php echo (isset($_POST['role']) && $_POST['role'] == 'patient') ? 'selected' : ''; ?>>Patient</option>
                <option value="doctor" <?php echo (isset($_POST['role']) && $_POST['role'] == 'doctor') ? 'selected' : ''; ?>>Doctor</option>
                <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>

            <label for="email">Email</label>
            <input id="email" name="email" type="email" placeholder="you@example.com" 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="••••••••" required>

            <button type="submit">Login</button>
            <p class="auth-switch">No account? <a href="signup.php">Create one</a></p>
        </form>
    </div>
</body>
</html>