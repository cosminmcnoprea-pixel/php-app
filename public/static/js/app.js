document.addEventListener('DOMContentLoaded', function() {
    console.log('App loaded from GCS static bucket');
    
    const timestamp = document.getElementById('timestamp');
    if (timestamp) {
        timestamp.textContent = new Date().toLocaleString();
    }
});
