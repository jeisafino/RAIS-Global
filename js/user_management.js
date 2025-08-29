document.addEventListener('DOMContentLoaded', function () {
    const userTableBody = document.getElementById('userTableBody');
    const addUserModal = document.getElementById('addUserModal');
    const editUserModal = document.getElementById('editUserModal');
    const deleteConfirmModal = document.getElementById('deleteConfirmModal');
    const confirmationModal = document.getElementById('confirmationModal') ? new bootstrap.Modal(document.getElementById('confirmationModal')) : null;

    // This function is now only in admin_dashboard.js to prevent conflicts.
    // We will call it after a successful action.
    function refreshUserTable() {
        // You can have a copy of this function here, or trigger the one in admin_dashboard.js
        // For simplicity, let's include it here as well.
        fetch('../admin/get_user_table.php') // Using your relative path
            .then(response => response.text())
            .then(html => {
                if (userTableBody) userTableBody.innerHTML = html;
            })
            .catch(error => console.error('Error refreshing user table:', error));
    }

    // Event handling for populating modals
    if (userTableBody) {
        userTableBody.addEventListener('click', function (event) {
            const button = event.target.closest('button');
            if (!button) return;
            const userRow = button.closest('tr');
            if (button.classList.contains('edit-btn') && editUserModal) {
                editUserModal.querySelector('#editUserId').value = button.dataset.userId;
                editUserModal.querySelector('#editFirstName').value = userRow.cells[1].textContent;
                editUserModal.querySelector('#editLastName').value = userRow.cells[2].textContent;
                editUserModal.querySelector('#editEmail').value = userRow.cells[3].textContent;
            }
            if (button.classList.contains('delete-btn') && deleteConfirmModal) {
                const userId = button.dataset.userId;
                const userName = button.dataset.userName;
                deleteConfirmModal.querySelector('#deleteModalBody').textContent = `Are you sure you want to delete ${userName}?`;
                deleteConfirmModal.querySelector('#confirmDeleteButton').dataset.userIdToDelete = userId;
            }
        });
    }

    // Handle ADD USER submission
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function (event) {
            event.preventDefault();
            // ... (AJAX logic for adding a user)
        });
    }

    // Handle EDIT USER submission
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = {
                userId: document.getElementById('editUserId').value,
                firstName: document.getElementById('editFirstName').value,
                lastName: document.getElementById('editLastName').value,
                email: document.getElementById('editEmail').value
            };
            fetch('../admin/edit_user.php', { // Using your relative path
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                bootstrap.Modal.getInstance(editUserModal).hide();
                if (data.status === 'success') {
                    if (confirmationModal) confirmationModal.show();
                    refreshUserTable(); // <<<--- THIS IS THE FIX
                } else {
                    alert('Error updating user.');
                }
            });
        });
    }
    
    // Handle DELETE USER confirmation
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', function () {
            const userId = this.dataset.userIdToDelete;
            fetch('../admin/delete_user.php', { // Using your relative path
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ userId: userId })
            })
            .then(response => response.json())
            .then(data => {
                bootstrap.Modal.getInstance(deleteConfirmModal).hide();
                if (data.status === 'success') {
                    refreshUserTable(); // <<<--- THIS IS THE FIX
                }
            });
        });
    }
});