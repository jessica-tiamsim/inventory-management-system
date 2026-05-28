<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Users</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/users.css">
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
                    <div class="search-container">
                        <span class="search-icon">🔍</span>
                        <input type="text" name="search" placeholder="Search by Username" class="search-input" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">  
                    </div>
                </form>
                
                <button class="create-btn" id="openUserOverlay">
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

    <div class="modal-overlay" id="newUserOverlay">
        <div class="modal-content">
            <h2 class="modal-title">Create Account</h2>
            
            <form id="userForm" action="<?= BASE_URL ?>/users/add" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-input" required placeholder="Enter Full Name">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-input" required placeholder="example@gmail.com">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-input" required placeholder="......">
                </div>

                <div class="form-group">
                    <label for="role">System Role</label>
                    <select name="role" id="role" class="form-select">
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" id="closeUserOverlay">Cancel</button>
                    <button type="submit" class="add-btn">Add Employee</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('newUserOverlay');
        const openBtn = document.getElementById('openUserOverlay');
        const closeBtn = document.getElementById('closeUserOverlay');

        // Add the .show class from your CSS when opened
        openBtn.addEventListener('click', () => {
            modal.classList.add('show');
        });

        // Remove the .show class when closed
        closeBtn.addEventListener('click', () => {
            modal.classList.remove('show');
        });

        // Close if clicking outside the white box
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });
    </script>
</body>
</html>