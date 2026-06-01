<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Users</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/profile.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sidebar_header.css">
</head>
<body>
    <?php include __DIR__ . '/sidebar_header.php'; ?>

    <div class="content">
        <div class="action-bar">
            <div class="page-header">
                <h1 class="page-title">Users</h1>
                <p class="page-subtitle">Manage your Employees</p>
            </div>
            
            <div class="search-and-button-group">
                <form method="GET" action="<?= BASE_URL ?>/profile" class="search-form">
                    <div class="search-container" style="display: flex; align-items: center;">
                        <span class="search-icon">🔍</span>
                        <input type="text" name="search" placeholder="Search by Username" class="search-input" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="border: none; outline: none; padding-left: 8px;">
                        <button type="submit" style="background: none; border: none; color: var(--maroon); font-weight: bold; cursor: pointer; margin-left: 10px;">Search</button>
                    </div>
                </form>
                
                <button class="create-btn" onclick="openModal('newUserOverlay')">
                    <span class="plus-icon">👤+</span> Create Account
                </button>
            </div>
        </div>

        <div class="table-container">
            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="empty-state">No employees found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="prism-overlay" id="newUserOverlay">
        <div class="prism-modal large">
            <div class="modal-header"><h2>Create Account</h2></div>
            <form action="<?= BASE_URL ?>/profile/add" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required placeholder="Enter Full Name">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" required placeholder="example@gmail.com">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="••••••">
                    </div>
                    <div class="form-group">
                        <label>System Role</label>
                        <select name="role">
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel-outline" onclick="closeModal('newUserOverlay')">Cancel</button>
                    <button type="submit" class="btn-save-maroon">Add Employee</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id)  { document.getElementById(id).classList.add('show'); }
        function closeModal(id) { document.getElementById(id).classList.remove('show'); }
        document.querySelectorAll('.prism-overlay').forEach(function(overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeModal(overlay.id);
            });
        });
    </script>
</body>
</html>