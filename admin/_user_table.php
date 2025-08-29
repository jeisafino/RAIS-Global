<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">User Management</h2>
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
                    <th>Last Activity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <?php
                if (isset($users) && is_array($users)) {
                    // Use a default value if not set on the page
                    $active_threshold = isset($active_threshold) ? $active_threshold : 15;
                    $threshold_time = strtotime("-$active_threshold seconds");

                    foreach ($users as $user) {
                        $is_online = !empty($user['last_activity']) && strtotime($user['last_activity']) >= $threshold_time;
                        $statusBadgeClass = $is_online ? 'bg-success' : 'bg-secondary';
                        $statusText = $is_online ? 'Online' : 'Offline';
                        $lastActivityDisplay = !empty($user['last_activity']) ? date("M d, Y h:i A", strtotime($user['last_activity'])) : 'Never';
                        $userName = htmlspecialchars($user['firstName']) . ' ' . htmlspecialchars($user['lastName']);

                        echo "<tr id='user-row-" . htmlspecialchars($user['id']) . "'>";
                        echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['firstName']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['lastName']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td><span class=\"badge " . $statusBadgeClass . "\">" . $statusText . "</span></td>";
                        echo "<td>" . $lastActivityDisplay . "</td>";
                        echo '<td>
                                <button class="btn btn-sm btn-info me-1 edit-btn" 
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
                              </td>';
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <?php if (isset($page_context) && $page_context === 'dashboard'): ?>
        <a href="user_management.php" class="btn btn-sm btn-primary mt-3" style="background-color: var(--rais-button-maroon); border-color: var(--rais-button-maroon);">Manage All Users</a>
    <?php elseif (isset($page_context) && $page_context === 'user_management'): ?>
        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addUserModal" style="background-color: var(--rais-button-maroon); border: none;">Add New User</button>
    <?php endif; ?>

</div>