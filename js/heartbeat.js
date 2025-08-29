// This function sends the heartbeat to the server
function sendUserHeartbeat() {
    fetch('../admin/update_activity.php')
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                console.error('Heartbeat failed:', data.message);
                // If we get an auth error, stop trying.
                if (data.message === 'User not authenticated.') {
                    clearInterval(heartbeatInterval);
                }
            }
        })
        .catch(error => console.error('Error sending heartbeat:', error));
}

// Call the function once immediately when the script loads
sendUserHeartbeat();

// Store the interval in a global variable
const heartbeatInterval = setInterval(sendUserHeartbeat, 5000);