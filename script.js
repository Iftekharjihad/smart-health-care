


function fakeSignup(e){
    e.preventDefault();
    alert('Account created successfully! Redirecting to login page...');
    window.location.href = "login.html";
    return false;
}

function goDashboard(e){
    e.preventDefault();
    const role = document.getElementById('role').value;
    // Redirect to dashboard, passing the selected role as a URL parameter
    window.location.href = 'dashboard.html?role=' + encodeURIComponent(role || 'patient');
    return false;
}

// Function to handle tab switching in the dashboard
function showTab(tab){
    // Remove 'active' class from all tabs
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    
    // Add 'active' class to the selected tab content
    const el = document.getElementById('tab-' + tab);
    if(el) el.classList.add('active');

    // Update active state of the navigation links
    ['patient','doctor','admin'].forEach(r => {
        const link = document.getElementById('tabLink' + r.charAt(0).toUpperCase() + r.slice(1));
        if(link) link.classList.toggle('active', r===tab);
    });
}

// Immediate function to check URL parameters and set the correct dashboard tab on load
(function(){
    // Check if we are on the dashboard page
    if (window.location.pathname.endsWith('dashboard.html')) {
        const params = new URLSearchParams(location.search);
        const role = params.get('role');
        if(role){
            // Set the dashboard tab based on the role parameter from login
            showTab(role);
        } else {
            // Default to 'patient' tab if no role is specified
            showTab('patient');
        }
    }
})();


// Form validation function


// Fake booking function (for demo without PHP)
function fakeBook(event) {
    event.preventDefault();
    alert('Appointment submitted! In live system, this would save to database.');
    return false;
}

// Fake search function
function fakeSearch(event) {
    // Remove the alert since we're now using real PHP search
    // The form will submit to search_doctors.php
    return true; // Allow form submission
}

// Set minimum date to today
// document.addEventListener('DOMContentLoaded', function() {
//     const dateField = document.getElementById('appointment_date');
//     if (dateField) {
//         const today = new Date().toISOString().split('T')[0];
//         dateField.setAttribute('min', today);
        
//         // Also set default to today
//         dateField.value = today;
//     }
    
//     // Form validation
//     const form = document.querySelector('.appointment-form');
//     if (form) {
//         form.addEventListener('submit', function(event) {
//             // Get form values
//             const phone = document.getElementById('phone_number').value;
//             const date = document.getElementById('appointment_date').value;
//             const age = document.getElementById('patient_age').value;
//             const gender = document.querySelector('input[name="patient_gender"]:checked');
            
//             // Check if form is valid
//             if (!form.checkValidity()) {
//                 alert('Please fill all required fields correctly.');
//                 return;
//             }
            
//             // Additional custom validation
//             if (!gender) {
//                 alert('Please select gender');
//                 event.preventDefault();
//                 return false;
//             }
            
//             // Phone validation
//             const phoneRegex = /^\+?8801[3-9]\d{8}$/;
//             if (!phoneRegex.test(phone)) {
//                 alert('Please enter a valid Bangladeshi phone number (e.g., +8801XXXXXXXXX)');
//                 event.preventDefault();
//                 return false;
//             }
            
//             // Date validation
//             const today = new Date().toISOString().split('T')[0];
//             if (date < today) {
//                 alert('Appointment date cannot be in the past!');
//                 event.preventDefault();
//                 return false;
//             }
            
//             // Age validation
//             if (age < 1 || age > 120) {
//                 alert('Please enter a valid age (1-120 years)');
//                 event.preventDefault();
//                 return false;
//             }
            
//             return true;
//         });
//     }
// });
// In your script.js file, add this function
function prefillAppointmentForm() {
    // Get URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const doctor = urlParams.get('doctor');
    const specialty = urlParams.get('specialty');
    
    if (doctor) {
        document.getElementById('doctor_name').value = doctor;
    }
    
    if (specialty) {
        const select = document.getElementById('speciality');
        for (let option of select.options) {
            if (option.text.includes(specialty) || option.value.includes(specialty)) {
                option.selected = true;
                break;
            }
        }
    }
}

// Call this when appointment page loads
document.addEventListener('DOMContentLoaded', function() {
    prefillAppointmentForm();
    
    // ... rest of your existing code
});