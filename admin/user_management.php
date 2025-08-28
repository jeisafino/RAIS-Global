<?php
// Page-specific data
$page_title = "RAIS Admin - User Management";
$active_page = "user_management"; // A variable to help set the 'active' class in the sidebar

// In a real application, you would fetch this data from your database.
$users = [
    ['id' => 1, 'firstName' => 'John', 'lastName' => 'Doe', 'email' => 'john.doe@example.com', 'status' => 'Active'],
    ['id' => 2, 'firstName' => 'Jane', 'lastName' => 'Smith', 'email' => 'jane.smith@example.com', 'status' => 'Inactive'],
    ['id' => 3, 'firstName' => 'Peter', 'lastName' => 'Jones', 'email' => 'peter.j@example.com', 'status' => 'Active'],
    ['id' => 4, 'firstName' => 'Alice', 'lastName' => 'Brown', 'email' => 'alice.b@example.com', 'status' => 'Inactive'],
    ['id' => 5, 'firstName' => 'Michael', 'lastName' => 'Clark', 'email' => 'm.clark@example.com', 'status' => 'Active'],
    ['id' => 6, 'firstName' => 'Sarah', 'lastName' => 'Davis', 'email' => 's.davis@example.com', 'status' => 'Active'],
];

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="icon" href="../img/logoulit.png" />
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="main-wrapper">
        
        <?php require_once 'sidebar.php'; ?>

        <div class="content-area">
            
            <?php require_once 'header.php'; ?>

            <main class="main-content">
                <h1>User Management</h1>

                <div class="content-card">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                        <h2 class="mb-3 mb-md-0">All Users</h2>
                        <div class="input-group w-100" style="max-width: 400px;">
                            <input type="text" class="form-control" placeholder="Search by ID, name, or email..." id="searchInput">
                            <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody">
                                <?php
                                foreach ($users as $user) {
                                    $statusClass = 'bg-warning text-dark';
                                    if ($user['status'] === 'Active' || $user['status'] === 'Approved') {
                                        $statusClass = 'bg-success';
                                    } elseif ($user['status'] === 'Inactive') {
                                        $statusClass = 'bg-secondary';
                                    }
                                    $userName = htmlspecialchars($user['firstName']) . ' ' . htmlspecialchars($user['lastName']);

                                    echo "<tr id='user-row-" . htmlspecialchars($user['id']) . "'>"; // Add unique ID to each row
                                    echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($user['firstName']) . "</td>";
                                    echo "<td>" . htmlspecialchars($user['lastName']) . "</td>";
                                    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                                    echo "<td><span class=\"badge " . $statusClass . "\">" . htmlspecialchars($user['status']) . "</span></td>";
                                    echo '<td>
                                            <div class="d-flex flex-column flex-md-row gap-2">
                                                <button class="btn btn-sm btn-info edit-btn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editUserModal"
                                                        data-user-id="' . htmlspecialchars($user['id']) . '"
                                                        data-first-name="' . htmlspecialchars($user['firstName']) . '"
                                                        data-last-name="' . htmlspecialchars($user['lastName']) . '"
                                                        data-email="' . htmlspecialchars($user['email']) . '"
                                                        data-status="' . htmlspecialchars($user['status']) . '">Edit</button>
                                                <button class="btn btn-sm btn-danger delete-btn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteConfirmModal"
                                                        data-user-id="' . htmlspecialchars($user['id']) . '"
                                                        data-user-name="' . $userName . '">Delete</button>
                                            </div>
                                          </td>';
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal" style="background-color: var(--rais-button-maroon); border: none;">Add New User</button>
                </div>

            </main>
        </div>
    </div>

    <!-- Modals -->

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3"><label for="addFirstName" class="form-label">First Name</label><input type="text" class="form-control" id="addFirstName" required></div>
                        <div class="mb-3"><label for="addLastName" class="form-label">Last Name</label><input type="text" class="form-control" id="addLastName" required></div>
                        <div class="mb-3"><label for="addEmail" class="form-label">Email address</label><input type="email" class="form-control" id="addEmail" required></div>
                        <div class="mb-3"><label for="addStatus" class="form-label">Status</label><select class="form-select" id="addStatus"><option>Active</option><option>Inactive</option></select></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addUserForm" class="btn btn-primary">Save User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" id="editUserId">
                        <div class="mb-3"><label for="editFirstName" class="form-label">First Name</label><input type="text" class="form-control" id="editFirstName" required></div>
                        <div class="mb-3"><label for="editLastName" class="form-label">Last Name</label><input type="text" class="form-control" id="editLastName" required></div>
                        <div class="mb-3"><label for="editEmail" class="form-label">Email address</label><input type="email" class="form-control" id="editEmail" required></div>
                        <div class="mb-3"><label for="editStatus" class="form-label">Status</label><select class="form-select" id="editStatus"><option>Active</option><option>Inactive</option></select></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="editUserForm" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="deleteModalBody">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- CHANGE 1: ADDED CONFIRMATION MODAL -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    User details have been updated successfully.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    
    <script src="togglemodeScript.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userTableBody = document.getElementById('userTableBody');
            
            // --- CHANGE 2: CREATE JAVASCRIPT INSTANCE OF THE NEW MODAL ---
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));


            // --- Helper Functions ---
            function getNextUserId() {
                const userRows = userTableBody.querySelectorAll('tr');
                let maxId = 0;
                userRows.forEach(row => {
                    const currentId = parseInt(row.cells[0].textContent, 10);
                    if (currentId > maxId) maxId = currentId;
                });
                return maxId + 1;
            }

            function escapeHTML(str) {
                const p = document.createElement("p");
                p.textContent = str;
                return p.innerHTML;
            }


            // Search functionality
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('keyup', function () {
                const searchTerm = this.value.toLowerCase();
                const tableRows = userTableBody.getElementsByTagName('tr');
                for (let i = 0; i < tableRows.length; i++) {
                    const rowText = tableRows[i].textContent.toLowerCase();
                    tableRows[i].style.display = rowText.includes(searchTerm) ? "" : "none";
                }
            });

            // Pass data to Edit User Modal
            const editUserModal = document.getElementById('editUserModal');
            editUserModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const firstName = button.getAttribute('data-first-name');
                const lastName = button.getAttribute('data-last-name');
                const email = button.getAttribute('data-email');
                const status = button.getAttribute('data-status');
                
                editUserModal.querySelector('.modal-title').textContent = 'Edit User: ' + firstName + ' ' + lastName;
                editUserModal.querySelector('#editUserId').value = userId;
                editUserModal.querySelector('#editFirstName').value = firstName;
                editUserModal.querySelector('#editLastName').value = lastName;
                editUserModal.querySelector('#editEmail').value = email;
                editUserModal.querySelector('#editStatus').value = status;
            });

            // Pass data to Delete Confirmation Modal
            const deleteConfirmModal = document.getElementById('deleteConfirmModal');
            deleteConfirmModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');
                deleteConfirmModal.querySelector('#deleteModalBody').textContent = `Are you sure you want to delete ${userName}? This action cannot be undone.`;
                deleteConfirmModal.querySelector('#confirmDeleteButton').setAttribute('data-user-id-to-delete', userId);
            });

            // Handle the actual deletion
            document.getElementById('confirmDeleteButton').addEventListener('click', function () {
                const userIdToDelete = this.getAttribute('data-user-id-to-delete');
                document.getElementById('user-row-' + userIdToDelete)?.remove();
                bootstrap.Modal.getInstance(deleteConfirmModal).hide();
            });

            
            // --- FORM SUBMISSION LOGIC ---

            // Handle ADD USER submission
            const addUserForm = document.getElementById('addUserForm');
            addUserForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const firstName = document.getElementById('addFirstName').value;
                const lastName = document.getElementById('addLastName').value;
                const email = document.getElementById('addEmail').value;
                const status = document.getElementById('addStatus').value;

                const newUserId = getNextUserId();
                const userName = escapeHTML(firstName) + ' ' + escapeHTML(lastName);
                let statusClass = (status === 'Active') ? 'bg-success' : 'bg-secondary';
                
                const newRow = document.createElement('tr');
                newRow.id = 'user-row-' + newUserId;
                newRow.innerHTML = `
                    <td>${newUserId}</td>
                    <td>${escapeHTML(firstName)}</td>
                    <td>${escapeHTML(lastName)}</td>
                    <td>${escapeHTML(email)}</td>
                    <td><span class="badge ${statusClass}">${escapeHTML(status)}</span></td>
                    <td>
                        <div class="d-flex flex-column flex-md-row gap-2">
                            <button class="btn btn-sm btn-info edit-btn" data-bs-toggle="modal" data-bs-target="#editUserModal" data-user-id="${newUserId}" data-first-name="${escapeHTML(firstName)}" data-last-name="${escapeHTML(lastName)}" data-email="${escapeHTML(email)}" data-status="${escapeHTML(status)}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" data-user-id="${newUserId}" data-user-name="${userName}">Delete</button>
                        </div>
                    </td>
                `;

                userTableBody.appendChild(newRow);
                addUserForm.reset();
                bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
            });

            // --- CHANGE 3: UPDATED EDIT USER SUBMISSION LOGIC ---
            const editUserForm = document.getElementById('editUserForm');
            editUserForm.addEventListener('submit', function(event) {
                event.preventDefault();

                // Get new data from the form
                const userId = document.getElementById('editUserId').value;
                const newFirstName = document.getElementById('editFirstName').value;
                const newLastName = document.getElementById('editLastName').value;
                const newEmail = document.getElementById('editEmail').value;
                const newStatus = document.getElementById('editStatus').value;

                // Find the user's row in the table
                const userRow = document.getElementById('user-row-' + userId);
                if (userRow) {
                    // Update the table cells
                    userRow.cells[1].textContent = newFirstName;
                    userRow.cells[2].textContent = newLastName;
                    userRow.cells[3].textContent = newEmail;
                    
                    // Update the status badge text and color
                    const badge = userRow.cells[4].querySelector('.badge');
                    badge.textContent = newStatus;
                    badge.className = newStatus === 'Active' ? 'badge bg-success' : 'badge bg-secondary';
                    
                    // Update the data attributes on the edit button
                    const editButton = userRow.querySelector('.edit-btn');
                    editButton.setAttribute('data-first-name', newFirstName);
                    editButton.setAttribute('data-last-name', newLastName);
                    editButton.setAttribute('data-email', newEmail);
                    editButton.setAttribute('data-status', newStatus);
                }

                // Hide the edit modal and show the confirmation modal
                bootstrap.Modal.getInstance(editUserModal).hide();
                confirmationModal.show();
            });
        });
    </script>
</body>

</html>