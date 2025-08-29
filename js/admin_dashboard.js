document.addEventListener('DOMContentLoaded', function () {
    function updateUserCounts() {
        fetch('../admin/get_active_users.php')
            .then(response => response.json())
            .then(data => {
                const activeCountElement = document.getElementById('activeUserCount');
                const inactiveCountElement = document.getElementById('inactiveUserCount');
                if (activeCountElement) {
                    activeCountElement.textContent = data.active_users;
                }
                if (inactiveCountElement) {
                    inactiveCountElement.textContent = data.inactive_users;
                }
            })
            .catch(error => console.error('Error fetching user counts:', error));
    }

    function refreshUserTable() {
        const userTableBody = document.getElementById('userTableBody');
        fetch('../admin/get_user_table.php')
            .then(response => response.text())
            .then(html => {
                if (userTableBody) {
                    userTableBody.innerHTML = html;
                }
            })
            .catch(error => console.error('Error refreshing user table:', error));
    }

    setInterval(updateUserCounts, 7000); // Refresh cards every 7 seconds
    setInterval(refreshUserTable, 7000); // Refresh table every 7 seconds
});