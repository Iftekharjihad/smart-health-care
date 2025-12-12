<?php
// Include database connection
require_once 'db_connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    echo "<!DOCTYPE html><html><head><title>Debug Info</title></head><body>";
    echo "<h2>Debug Information</h2>";
    
    // Display all POST data
    echo "<h3>POST Data Received:</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field Name</th><th>Value</th></tr>";
    
    foreach ($_POST as $key => $value) {
        echo "<tr>";
        echo "<td><strong>$key</strong></td>";
        echo "<td>";
        if (empty($value)) {
            echo "<span style='color: red;'>EMPTY</span>";
        } else {
            echo htmlspecialchars($value);
        }
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Check for missing required fields
    $required_fields = [
        'appointment_date' => 'Date',
        'appointment_time' => 'Time',
        'speciality' => 'Speciality',
        'doctor_name' => 'Doctor Name',
        'patient_name' => 'Patient Name',
        'patient_age' => 'Age',
        'patient_gender' => 'Gender',
        'phone_number' => 'Phone Number'
    ];
    
    echo "<h3>Required Fields Check:</h3>";
    echo "<ul>";
    
    $all_fields_present = true;
    foreach ($required_fields as $field => $label) {
        if (isset($_POST[$field]) && !empty(trim($_POST[$field]))) {
            echo "<li style='color: green;'>✓ $label: Present</li>";
        } else {
            echo "<li style='color: red;'>✗ $label: MISSING</li>";
            $all_fields_present = false;
        }
    }
    
    echo "</ul>";
    
    // Show current date for comparison
    $today = date("Y-m-d");
    echo "<p><strong>Today's Date:</strong> $today</p>";
    
    // Show the submitted date
    $submitted_date = isset($_POST['appointment_date']) ? $_POST['appointment_date'] : 'NOT SET';
    echo "<p><strong>Submitted Date:</strong> $submitted_date</p>";
    
    if (!$all_fields_present) {
        echo "<h3 style='color: red;'>Error: Some required fields are missing!</h3>";
        echo '<a href="appointment.html" style="display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;">Go Back to Form</a>';
        echo "</body></html>";
        exit();
    }
    
    // Now continue with the database insertion
    $conn = getConnection();
    
    // Sanitize and validate input data
    $appointment_date = mysqli_real_escape_string($conn, $_POST['appointment_date']);
    $appointment_time = mysqli_real_escape_string($conn, $_POST['appointment_time']);
    $speciality = mysqli_real_escape_string($conn, $_POST['speciality']);
    $doctor_name = mysqli_real_escape_string($conn, $_POST['doctor_name']);
    $patient_name = mysqli_real_escape_string($conn, $_POST['patient_name']);
    $patient_age = intval($_POST['patient_age']);
    $patient_weight = isset($_POST['patient_weight']) && !empty(trim($_POST['patient_weight'])) 
        ? floatval($_POST['patient_weight']) 
        : NULL;
    $patient_gender = mysqli_real_escape_string($conn, $_POST['patient_gender']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $problem_description = isset($_POST['problem_description']) 
        ? mysqli_real_escape_string($conn, $_POST['problem_description'])
        : '';
    
    // Validate date (must be today or future)
    echo "<h3>Date Validation:</h3>";
    echo "<p>Submitted Date: $appointment_date</p>";
    echo "<p>Today's Date: $today</p>";
    
    if ($appointment_date < $today) {
        echo "<h3 style='color: red;'>Error: Appointment date cannot be in the past!</h3>";
        echo "<p>You selected: $appointment_date (which is before today: $today)</p>";
        echo '<a href="appointment.html" style="display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;">Go Back and Choose Future Date</a>';
        $conn->close();
        echo "</body></html>";
        exit();
    }
    
    // Validate age
    if ($patient_age < 1 || $patient_age > 120) {
        echo "<h3 style='color: red;'>Error: Please enter a valid age (1-120)!</h3>";
        $conn->close();
        echo "</body></html>";
        exit();
    }
    
    // Prepare SQL statement
    $sql = "INSERT INTO appointments (
        appointment_date, 
        appointment_time, 
        speciality, 
        doctor_name, 
        patient_name, 
        patient_age, 
        patient_weight, 
        patient_gender, 
        phone_number, 
        problem_description
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param(
            "sssssidsss", 
            $appointment_date, 
            $appointment_time, 
            $speciality, 
            $doctor_name, 
            $patient_name, 
            $patient_age, 
            $patient_weight, 
            $patient_gender, 
            $phone_number, 
            $problem_description
        );
        
        // Execute the statement
        if ($stmt->execute()) {
            // Get the last inserted appointment ID
            $appointment_id = $conn->insert_id;
            
            echo "<div style='padding: 20px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724; margin: 20px;'>";
            echo "<h2 style='color: #155724;'>✅ Appointment Confirmed Successfully!</h2>";
            echo "<p><strong>Appointment ID:</strong> AP" . str_pad($appointment_id, 6, '0', STR_PAD_LEFT) . "</p>";
            echo "<p><strong>Date:</strong> " . date('F j, Y', strtotime($appointment_date)) . "</p>";
            echo "<p><strong>Time:</strong> " . date('h:i A', strtotime($appointment_time)) . "</p>";
            echo "<p><strong>Doctor:</strong> " . htmlspecialchars($doctor_name) . "</p>";
            echo "<p><strong>Specialty:</strong> " . htmlspecialchars($speciality) . "</p>";
            echo "<p><strong>Patient:</strong> " . htmlspecialchars($patient_name) . " (Age: $patient_age)</p>";
            echo "<p><strong>Phone:</strong> " . htmlspecialchars($phone_number) . "</p>";
            echo "<p>You will receive a confirmation SMS shortly.</p>";
            echo '<a href="appointment.html" style="display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;">Book Another Appointment</a>';
            echo "</div>";
            
        } else {
            echo "<div style='padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; color: #721c24; margin: 20px;'>";
            echo "<h3 style='color: #721c24;'>❌ Error Booking Appointment</h3>";
            echo "<p>Error: " . $stmt->error . "</p>";
            echo "</div>";
        }
        
        $stmt->close();
    } else {
        echo "<div style='padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; color: #721c24; margin: 20px;'>";
        echo "<h3 style='color: #721c24;'>❌ Database Error</h3>";
        echo "<p>Error preparing statement: " . $conn->error . "</p>";
        echo "</div>";
    }
    
    $conn->close();
    echo "</body></html>";
    
} else {
    // If not POST request
    echo "<h3>Error: This page should be accessed via form submission.</h3>";
    echo '<a href="appointment.html">Go to Appointment Form</a>';
}
?>