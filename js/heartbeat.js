function sendUserHeartbeat() {
    fetch('../admin/update_activity.php')
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                console.error('Heartbeat failed:', data.message);
            }
        })
        .catch(error => console.error('Error sending heartbeat:', error));
}
sendUserHeartbeat();
setInterval(sendUserHeartbeat, 5000); // Send heartbeat every 5 seconds