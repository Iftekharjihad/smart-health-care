<?php
// Include database connection
require_once 'db_connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Dhaka');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'/>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
        <title>Search Results • Smart Health Care</title>
        <link rel='stylesheet' href='style.css'/>
    </head>
    <body>
        <nav class='navbar'>
            <div class='nav-inner'>
                <div class='logo'>Smart Health Care Portal</div>
                <ul class='nav-center'>
                    <li><a href='index.html'>🏠 Home</a></li>
                    <li><a class='active' href='doctor.html'>👨‍⚕️ Doctors</a></li>
                    <li><a href='appointment.html'>📅 Appointment</a></li>
                    <li><a href='services.html'>🧩 Services</a></li>
                </ul>
                <a href='login.html' class='login-btn'>🔐 Log In</a>
            </div>
        </nav>";
    
    echo "<section class='search-results'>";
    echo "<div class='appointment-container'>";
    
    // Display search criteria
    echo "<div class='appointment-info'>";
    echo "<h2>Search Results</h2>";
    echo "<p>Doctors matching your criteria:</p>";
    
    // Display search criteria
    if (isset($_POST['speciality']) && !empty($_POST['speciality'])) {
        echo "<p><strong>Speciality:</strong> " . htmlspecialchars($_POST['speciality']) . "</p>";
    }
    if (isset($_POST['location']) && !empty($_POST['location'])) {
        echo "<p><strong>Location:</strong> " . htmlspecialchars($_POST['location']) . "</p>";
    }
    if (isset($_POST['available_time']) && !empty($_POST['available_time'])) {
        echo "<p><strong>Available Time:</strong> " . htmlspecialchars($_POST['available_time']) . "</p>";
    }
    
    echo "</div>";
    
    // Get connection
    $conn = getConnection();
    
    // Build SQL query based on search criteria
    $sql = "SELECT d.*, s.name as specialty_name 
            FROM doctors d 
            LEFT JOIN specialties s ON d.specialty_id = s.id 
            WHERE 1=1";
    
    $params = [];
    $types = "";
    
    // Add conditions based on search criteria
    if (isset($_POST['speciality']) && !empty($_POST['speciality'])) {
        $sql .= " AND s.name LIKE ?";
        $params[] = "%" . $_POST['speciality'] . "%";
        $types .= "s";
    }
    
    if (isset($_POST['doctor_name']) && !empty($_POST['doctor_name'])) {
        $sql .= " AND d.name LIKE ?";
        $params[] = "%" . $_POST['doctor_name'] . "%";
        $types .= "s";
    }
    
    if (isset($_POST['doctor_age']) && !empty($_POST['doctor_age'])) {
        $sql .= " AND d.age = ?";
        $params[] = intval($_POST['doctor_age']);
        $types .= "i";
    }
    
    if (isset($_POST['gender']) && !empty($_POST['gender'])) {
        $sql .= " AND d.gender = ?";
        $params[] = $_POST['gender'];
        $types .= "s";
    }
    
    if (isset($_POST['location']) && !empty($_POST['location'])) {
        $sql .= " AND d.location LIKE ?";
        $params[] = "%" . $_POST['location'] . "%";
        $types .= "s";
    }
    
    if (isset($_POST['available_time']) && !empty($_POST['available_time'])) {
        $sql .= " AND TIME(d.available_time) = ?";
        $params[] = $_POST['available_time'];
        $types .= "s";
    }
    
    // Order by experience (most experienced first)
    $sql .= " ORDER BY d.experience_years DESC";
    
    // Prepare and execute statement
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // Bind parameters if there are any
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<div class='doctors-grid'>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<div class='doctor-card'>";
                echo "<div class='doctor-header'>";
                echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                echo "<span class='doctor-specialty'>" . htmlspecialchars($row['specialty_name']) . "</span>";
                echo "</div>";
                
                echo "<div class='doctor-details'>";
                echo "<p><strong>👤 Gender:</strong> " . htmlspecialchars($row['gender']) . "</p>";
                echo "<p><strong>🎂 Age:</strong> " . $row['age'] . " years</p>";
                echo "<p><strong>📍 Location:</strong> " . htmlspecialchars($row['location']) . "</p>";
                echo "<p><strong>⏰ Available Time:</strong> " . date('h:i A', strtotime($row['available_time'])) . "</p>";
                echo "<p><strong>📅 Experience:</strong> " . $row['experience_years'] . " years</p>";
                echo "</div>";
                
                // Book appointment button
                echo "<div class='doctor-actions'>";
                echo "<form action='appointment.html' method='GET' style='display: inline;'>";
                echo "<input type='hidden' name='doctor' value='" . urlencode($row['name']) . "'>";
                echo "<input type='hidden' name='specialty' value='" . urlencode($row['specialty_name']) . "'>";
                echo "<button type='submit' class='book-btn'>📅 Book Appointment</button>";
                echo "</form>";
                echo "</div>";
                
                echo "</div>";
            }
            
            echo "</div>"; // Close doctors-grid
            
        } else {
            echo "<div class='no-results'>";
            echo "<h3>No doctors found matching your criteria.</h3>";
            echo "<p>Try adjusting your search parameters:</p>";
            echo "<ul>";
            echo "<li>Try a different location or time</li>";
            echo "<li>Check spelling of doctor names</li>";
            echo "<li>Select a different specialty</li>";
            echo "</ul>";
            echo '<a href="doctor.html" class="back-btn">← Back to Search</a>';
            echo "</div>";
        }
        
        $stmt->close();
    } else {
        echo "<div class='error-message'>";
        echo "<h3>Database Error</h3>";
        echo "<p>Error preparing search query: " . $conn->error . "</p>";
        echo "</div>";
    }
    
    $conn->close();
    
    echo "</div>"; // Close appointment-container
    echo "</section>"; // Close search-results
    
    // Display footer
    echo "<footer>
        <div class='footer-container'>
            <div class='footer-about'>
                <h3>Smart Health Care</h3>
                <p>Providing modern digital solutions for better health care —
                    book appointments, consult doctors, and access your reports online.</p>
            </div>
            <div class='footer-links'>
                <h4>Quick Links</h4>
                <ul>
                    <li><a href='index.html'>🏠 Home</a></li>
                    <li><a href='doctor.html'>👨‍⚕️ Doctors</a></li>
                    <li><a href='appointment.html'>📅 Appointments</a></li>
                    <li><a href='services.html'>🧩 Services</a></li>
                </ul>
            </div>
            <div class='footer-contact'>
                <h4>Contact Us</h4>
                <p><strong>Email:</strong> support@smarthealthcare.com</p>
                <p><strong>Phone:</strong> +880 1610-177059</p>
                <p><strong>Address:</strong> 123 Health Street, Chattagram, Bangladesh</p>
            </div>
        </div>
        <div class='footer-bottom'>
            <p>© 2025 Smart Health Care Portal. All rights reserved.</p>
        </div>
    </footer>";
    
    echo "</body></html>";
    
} else {
    // If not POST request, redirect to doctor search page
    header("Location: doctor.html");
    exit();
}
?>