<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['admin_id'] = 1; 
}

$current_admin_id = $_SESSION['admin_id'];
$is_authorized = false;
$auth_error = '';

$stmt_role = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt_role->bind_param("i", $current_admin_id);
$stmt_role->execute();
$current_admin = $stmt_role->get_result()->fetch_assoc();
$stmt_role->close();

if ($current_admin && $current_admin['role'] === 'Super Admin') {
    $_SESSION['admin_page_authorized'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['super_admin_password'])) {
    $stmt_super = $conn->prepare("SELECT password FROM users WHERE role = 'Super Admin'");
    $stmt_super->execute();
    $result = $stmt_super->get_result();
    $stmt_super->close();

    $isValid = false;
    while ($super_admin = $result->fetch_assoc()) {
        if (password_verify($_POST['super_admin_password'], $super_admin['password'])) {
            $isValid = true;
            break;
        }
    }

    if ($isValid) {
        $_SESSION['admin_page_authorized'] = true;
    } else {
        $auth_error = 'Incorrect password. Access has been denied.';
    }
}

$is_authorized = !empty($_SESSION['admin_page_authorized']);

$page_title = "RAIS Admin - Admin Profile Management";
$active_page = "admin_profile";
$admin_accounts = [];

if ($is_authorized) {
    $sql = "SELECT id, firstName, lastName, email, role as type, status FROM users WHERE role LIKE '%Admin%' ORDER BY id ASC";
    if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        // If this row is the logged-in Super Admin, force the display status to Active
        if ($row['id'] == $current_admin_id && $row['type'] === 'Super Admin') {
        $row['status'] = 'Active';


        }
        $admin_accounts[] = $row;
    }
    $result->free();
}
}

echo "<pre>";
print_r($admin_accounts);
echo "</pre>";

$conn->close();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-table-image { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <?php require_once 'sidebar.php'; ?>
        <div class="content-area">
            <?php require_once 'header.php'; ?>
            <main class="main-content">
                <?php if ($is_authorized): ?>
                
                <h1>Admin Profile Management</h1>
                <div class="content-card">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                        <h2 class="mb-3 mb-md-0">Existing Admin Accounts</h2>
                        <div class="input-group w-100" style="max-width: 400px;"><input type="text" class="form-control" placeholder="Search..." id="searchInput"></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th></th><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Type</th><th>Status</th><th>Password</th><th>Actions</th></tr></thead>
                            <tbody id="adminAccountsTableBody"></tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary mt-3" id="addNewAdminBtn" style="background-color: var(--rais-button-maroon);">Add New Admin</button>
                </div>

                <?php else: ?>
                
                <div class="content-card">
                    <div class="text-center"><i class="bi bi-shield-lock-fill" style="font-size: 4rem; color: var(--rais-primary-green);"></i></div>
                    <h1 class="text-center mt-3">Authorization Required</h1>
                    <p class="text-center text-muted">You must enter the Super Admin's password to manage admin profiles.</p>
                    <?php if ($auth_error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($auth_error); ?></div><?php endif; ?>
                    <form method="POST" action="admin_profile.php" class="mx-auto" style="max-width: 400px;">
                        <div class="mb-3"><label for="super_admin_password" class="form-label">Super Admin Password</label><input type="password" name="super_admin_password" id="super_admin_password" class="form-control" required autofocus></div>
                        <div class="d-grid"><button type="submit" class="btn btn-primary" style="background-color: var(--rais-button-maroon);">Authorize</button></div>
                    </form>
                </div>

                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php if ($is_authorized): ?>
    <div class="modal fade" id="adminModal" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="adminModalLabel">Add New Admin</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="adminForm" novalidate><input type="hidden" id="adminId"><div class="row"><div class="col-md-6 mb-3"><label for="adminFirstName" class="form-label">First Name</label><input type="text" class="form-control" id="adminFirstName" required></div><div class="col-md-6 mb-3"><label for="adminLastName" class="form-label">Last Name</label><input type="text" class="form-control" id="adminLastName" required></div></div><div class="mb-3"><label for="adminEmail" class="form-label">Email Address</label><input type="email" class="form-control" id="adminEmail" required></div><div class="mb-3"><label for="adminType" class="form-label">Admin Type</label><select class="form-select" id="adminType"><option value="Admin">Admin</option><option value="Support Admin">Support Admin</option><option value="Finance Admin">Finance Admin</option><option value="Super Admin">Super Admin</option></select></div><div class="mb-3"><label for="adminPassword" class="form-label">Password</label><input type="password" class="form-control" id="adminPassword" minlength="8"><small class="form-text text-muted" id="passwordHint"></small></div><hr><p class="text-muted">Enter your password to authorize this change.</p><div class="mb-3"><label for="adminFormVerificationPassword" class="form-label">Your Current Password</label><input type="password" class="form-control" id="adminFormVerificationPassword" required></div></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" form="adminForm" class="btn btn-primary" style="background-color: var(--rais-button-maroon);">Save Admin</button></div></div></div></div>
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Confirm Deletion</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p id="deleteConfirmModalBody"></p><hr><p class="text-muted">To confirm, please enter your password.</p><div class="mb-3"><label for="deleteVerificationPassword" class="form-label">Your Current Password</label><input type="password" class="form-control" id="deleteVerificationPassword" required></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Admin</button></div></div></div></div>
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="resetPasswordModalLabel">Reset User Password</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="resetPasswordForm" novalidate><input type="hidden" id="resetPasswordUserId"><p>You are resetting the password for <strong id="resetPasswordUserName"></strong>.</p><div class="mb-3"><label for="newPassword" class="form-label">New Password</label><input type="password" class="form-control" id="newPassword" required minlength="8"><div class="invalid-feedback">Password must be at least 8 characters.</div></div><div class="mb-3"><label for="confirmNewPassword" class="form-label">Confirm New Password</label><input type="password" class="form-control" id="confirmNewPassword" required><div class="invalid-feedback">Passwords do not match.</div></div><hr><p class="text-muted">Enter your password to authorize this change.</p><div class="mb-3"><label for="resetVerificationPassword" class="form-label">Your Current Password</label><input type="password" class="form-control" id="resetVerificationPassword" required><div class="invalid-feedback">Your authorizing password is required.</div></div></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" form="resetPasswordForm" class="btn btn-primary" style="background-color: var(--rais-button-maroon);">Authorize & Save</button></div></div></div></div>
    <div class="modal fade" id="successModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Success</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="successModalBody"></div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button></div></div></div></div>
    <div class="modal fade" id="errorModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header bg-danger text-white"><h5 class="modal-title">Error</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="errorModalBody"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const App = { adminAccounts: <?php echo json_encode($admin_accounts); ?>, currentAdminId: <?php echo json_encode($current_admin_id); ?>, modals: {} };
            const modalIds = ['adminModal', 'deleteConfirmModal', 'successModal', 'errorModal', 'resetPasswordModal'];
            modalIds.forEach(id => App.modals[id] = new bootstrap.Modal(document.getElementById(id)));
            
            function renderAdminAccounts() {
    const tableBody = document.getElementById('adminAccountsTableBody');
    tableBody.innerHTML = '';
    App.adminAccounts.forEach(admin => {
        const isCurrentUser = admin.id == App.currentAdminId;

        // 🔒 Force Active if this is the logged-in Super Admin
        let status = admin.status;
        if (isCurrentUser && admin.type.toLowerCase().includes("super")) {
            status = "Active";
        }

        const statusText = isCurrentUser ? `${status} (You)` : status;
        const statusBadge = status === 'Active' ? 'bg-success' : 'bg-danger';
        const deleteBtn = isCurrentUser 
            ? `<button class="btn btn-sm btn-danger" disabled>Delete</button>` 
            : `<button class="btn btn-sm btn-danger delete-admin-btn" data-admin-id="${admin.id}" data-admin-name="${admin.firstName} ${admin.lastName}">Delete</button>`;

        const row = tableBody.insertRow();
        row.innerHTML = `
            <td>
                <img src="https://placehold.co/40x40/004d40/FFFFFF?text=${admin.firstName.charAt(0)}${admin.lastName.charAt(0)}" class="admin-table-image">
            </td>
            <td>${admin.id}</td>
            <td>${admin.firstName}</td>
            <td>${admin.lastName}</td>
            <td>${admin.email}</td>
            <td>${admin.type}</td>
            <td><span class="badge ${statusBadge}">${statusText}</span></td>
            <td>
                <button class="btn btn-sm btn-outline-secondary reset-password-btn" data-admin-id="${admin.id}" data-admin-name="${admin.firstName} ${admin.lastName}">Reset</button>
            </td>
            <td>
                <button class="btn btn-sm btn-info me-1 edit-admin-btn" style="background-color: var(--rais-primary-green); color: white;" data-admin-id="${admin.id}">Edit</button>
                ${deleteBtn}
            </td>
        `;
    });
}

            
            async function performApiRequest(payload, submitBtn) {
                if(submitBtn) submitBtn.disabled = true;
                try {
                    const response = await fetch('../api/manageAdmins.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
                    const result = await response.json();
                    if (result.status === 'success') {
                        App.adminAccounts = result.admins;
                        renderAdminAccounts();
                        Object.values(App.modals).forEach(modal => modal.hide());
                        document.getElementById('successModalBody').textContent = result.message;
                        App.modals.successModal.show();
                    } else { throw new Error(result.message); }
                } catch (error) { document.getElementById('errorModalBody').textContent = error.message; App.modals.errorModal.show(); } finally { if(submitBtn) submitBtn.disabled = false; }
            }
            
            function showAdminForm(adminData = null) {
                const form = document.getElementById('adminForm');
                form.reset();
                form.classList.remove('was-validated');
                if (adminData) {
                    document.getElementById('adminModalLabel').textContent = `Edit Admin: ${adminData.firstName} ${adminData.lastName}`;
                    document.getElementById('adminId').value = adminData.id; document.getElementById('adminFirstName').value = adminData.firstName; document.getElementById('adminLastName').value = adminData.lastName; document.getElementById('adminEmail').value = adminData.email; document.getElementById('adminType').value = adminData.type;
                    document.getElementById('adminPassword').removeAttribute('required');
                    document.getElementById('passwordHint').textContent = 'Leave blank to keep current password.';
                } else {
                    document.getElementById('adminModalLabel').textContent = 'Add New Admin';
                    document.getElementById('adminPassword').setAttribute('required', 'true');
                    document.getElementById('passwordHint').textContent = 'Required for new admins (min 8 characters).';
                }
                App.modals.adminModal.show();
            }

            document.getElementById('addNewAdminBtn').addEventListener('click', () => showAdminForm());

            document.getElementById('adminAccountsTableBody').addEventListener('click', function(e) {
                const target = e.target.closest('button');
                if (!target) return;
                if (target.classList.contains('edit-admin-btn')) { const admin = App.adminAccounts.find(a => a.id == target.dataset.adminId); if (admin) showAdminForm(admin); }
                if (target.classList.contains('delete-admin-btn')) {
                    document.getElementById('deleteConfirmModalBody').textContent = `Are you sure you want to delete ${target.dataset.adminName}? This cannot be undone.`;
                    document.getElementById('confirmDeleteBtn').dataset.adminId = target.dataset.adminId;
                    App.modals.deleteConfirmModal.show();
                }
                if (target.classList.contains('reset-password-btn')) {
                    const form = document.getElementById('resetPasswordForm');
                    form.reset(); form.classList.remove('was-validated');
                    document.getElementById('resetPasswordUserId').value = target.dataset.adminId;
                    document.getElementById('resetPasswordUserName').textContent = target.dataset.adminName;
                    App.modals.resetPasswordModal.show();
                }
            });

            document.getElementById('adminForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('adminId').value;
                if (!id && (!document.getElementById('adminPassword').value || document.getElementById('adminPassword').value.length < 8)) { this.classList.add('was-validated'); return; }
                if (!this.checkValidity()) { this.classList.add('was-validated'); return; }
                const payload = {
                    action: id ? 'update' : 'add',
                    data: { id: id, firstName: document.getElementById('adminFirstName').value, lastName: document.getElementById('adminLastName').value, email: document.getElementById('adminEmail').value, type: document.getElementById('adminType').value, password: document.getElementById('adminPassword').value },
                    verificationPassword: document.getElementById('adminFormVerificationPassword').value
                };
                performApiRequest(payload, e.submitter);
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', function(e) {
                const verificationPassword = document.getElementById('deleteVerificationPassword').value;
                if (!verificationPassword) { alert('You must enter your password to confirm deletion.'); return; }
                const payload = { action: 'delete', data: { id: this.dataset.adminId }, verificationPassword: verificationPassword };
                performApiRequest(payload, e.target);
            });

            document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const newPass = document.getElementById('newPassword'); const confirmPass = document.getElementById('confirmNewPassword'); const adminPass = document.getElementById('resetVerificationPassword');
                let isValid = true;
                [newPass, confirmPass, adminPass].forEach(el => el.classList.remove('is-invalid'));
                if (newPass.value.length < 8) { newPass.classList.add('is-invalid'); isValid = false; }
                if (newPass.value !== confirmPass.value) { confirmPass.classList.add('is-invalid'); isValid = false; }
                if (!adminPass.value) { adminPass.classList.add('is-invalid'); isValid = false; }
                if (!isValid) return;
                const payload = { action: 'reset_password', data: { id: document.getElementById('resetPasswordUserId').value, password: newPass.value }, verificationPassword: adminPass.value };
                performApiRequest(payload, e.submitter);
            });
            
            renderAdminAccounts();
        });
    </script>
    <?php endif; ?>
</body>
</html>