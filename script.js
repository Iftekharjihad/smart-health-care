function fakeBook(e){
    e.preventDefault();
    alert('Appointment request submitted! (demo)');
    return false;
}

function fakeSearch(e){
    e.preventDefault();
    alert('Doctor search initiated! (demo)');
    return false;
}

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