<?php
// signup.php - Registration page with form
session_start();
require_once 'db_connection.php';
$error = $success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    
    // Validate inputs
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (!in_array($role, ['patient', 'doctor', 'admin'])) {
        $error = "Invalid role selected!";
    } else {
        // Check if email already exists
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();
        
        if ($check_email->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $success = "Registration successful! You can now login.";
                // Clear form
                $full_name = $email = $role = '';
            } else {
                $error = "Registration failed. Please try again.";
            }
            
            $stmt->close();
        }
        $check_email->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Sign Up | Smart Health Care</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body class="auth-body">
    <div class="auth-card">
        <h2>Create Your Account</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <label for="role">Register As</label>
            <select id="role" name="role" required>
                <option value="">-- Select Role --</option>
                <option value="patient" <?php echo (isset($_POST['role']) && $_POST['role'] == 'patient') ? 'selected' : ''; ?>>Patient</option>
                <option value="doctor" <?php echo (isset($_POST['role']) && $_POST['role'] == 'doctor') ? 'selected' : ''; ?>>Doctor</option>
                <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>

            <label for="full_name">Full Name</label>
            <input id="full_name" name="full_name" type="text" placeholder="Enter your full name" 
                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>

            <label for="email">Email</label>
            <input id="email" name="email" type="email" placeholder="Enter your email" 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Create password" required>

            <label for="confirm_password">Confirm Password</label>
            <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm password" required>

            <button type="submit">Sign Up</button>
        </form>
        <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>