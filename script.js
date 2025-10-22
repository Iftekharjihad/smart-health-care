function fakeBook(e){
  e.preventDefault();
  alert('Appointment request submitted! (demo)');
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
  window.location.href = 'deshboard.html?role=' + encodeURIComponent(role || 'patient');
  return false;
}

function showTab(tab){
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  const el = document.getElementById('tab-' + tab);
  if(el) el.classList.add('active');

  ['patient','doctor','admin'].forEach(r => {
    const link = document.getElementById('tabLink' + r.charAt(0).toUpperCase() + r.slice(1));
    if(link) link.classList.toggle('active', r===tab);
  });
}

(function(){
  const params = new URLSearchParams(location.search);
  const role = params.get('role');
  if(role){
    const tab = document.getElementById('tab-' + role);
    if(tab) showTab(role);
  }
})();
