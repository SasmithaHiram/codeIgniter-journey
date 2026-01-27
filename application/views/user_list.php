<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }

        h1 {
            margin-bottom: 20px;
        }

        .btn {
            padding: 8px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .btn-danger {
            background-color: #dc3545;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .actions {
            display: flex;
            gap: 6px;
        }

        /* Modal */
        #userModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            min-width: 320px;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            margin-bottom: 4px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .modal-footer {
            margin-top: 10px;
            text-align: right;
        }

        #message {
            margin-bottom: 15px;
            color: green;
        }
    </style>
</head>

<body>
    <h1>User Management</h1>

    <div id="message"></div>

    <button id="openModalBtn" class="btn btn-primary">Create New User</button>

    <div style="margin-top: 12px; margin-bottom: 12px; display: flex; gap: 8px; align-items: center;">
        <input type="text" id="searchInput" placeholder="Search by name or email" style="flex: 1; padding: 8px;" />
        <button class="btn btn-secondary" id="searchBtn">Search</button>
        <button class="btn" id="clearSearchBtn">Clear</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="userTableBody"></tbody>
    </table>

    <!-- Modal -->
    <div id="userModal">
        <div class="modal-content">
            <span id="closeModalBtn" class="close-btn">&times;</span>
            <h2 id="modalTitle">Create User</h2>
            <form id="userForm">
                <input type="hidden" id="userId" name="id">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveUserBtn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const baseUrl = '<?php echo site_url('user'); ?>';

        const openModalBtn = document.getElementById('openModalBtn');
        const userModal = document.getElementById('userModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const userForm = document.getElementById('userForm');
        const userIdInput = document.getElementById('userId');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const modalTitle = document.getElementById('modalTitle');
        const messageDiv = document.getElementById('message');
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        const clearSearchBtn = document.getElementById('clearSearchBtn');

        function openModal(mode, userData) {
            if (mode === 'create') {
                modalTitle.textContent = 'Create User';
                userIdInput.value = '';
                nameInput.value = '';
                emailInput.value = '';
            } else if (mode === 'edit' && userData) {
                modalTitle.textContent = 'Edit User';
                userIdInput.value = userData.id;
                nameInput.value = userData.name;
                emailInput.value = userData.email;
            }
            userModal.style.display = 'flex';
        }

        function closeModal() {
            userModal.style.display = 'none';
        }

        openModalBtn.addEventListener('click', function() {
            openModal('create');
        });

        closeModalBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        window.addEventListener('click', function(event) {
            if (event.target === userModal) {
                closeModal();
            }
        });

        // Render helpers
        function renderUsers(users) {
            const tbody = document.getElementById('userTableBody');
            tbody.innerHTML = '';

            (users || []).forEach(function(user) {
                const tr = document.createElement('tr');
                tr.setAttribute('data-id', user.id);
                tr.setAttribute('data-name', user.name || '');
                tr.setAttribute('data-email', user.email || '');

                tr.innerHTML = `
                    <td>${user.id}</td>
                    <td>${escapeHtml(user.name || '')}</td>
                    <td>${escapeHtml(user.email || '')}</td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-secondary edit-btn">Edit</button>
                            <button class="btn btn-danger delete-btn">Delete</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            // rebind action buttons after render
            bindRowActions();
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }

        function showMessage(msg, ok = true) {
            messageDiv.style.color = ok ? 'green' : 'red';
            messageDiv.textContent = msg;
            setTimeout(() => {
                messageDiv.textContent = '';
            }, 2500);
        }

        async function loadUsers(q) {
            try {
                const url = q ? `${baseUrl}?q=${encodeURIComponent(q)}` : baseUrl;
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data && data.success) {
                    renderUsers(data.data || []);
                } else {
                    renderUsers([]);
                    showMessage('Failed to load users', false);
                }
            } catch (e) {
                console.error(e);
                showMessage('Error loading users', false);
            }
        }

        // Search handlers
        function triggerSearch() {
            const q = (searchInput.value || '').trim();
            loadUsers(q || undefined);
        }
        searchBtn.addEventListener('click', triggerSearch);
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            loadUsers();
        });
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                triggerSearch();
            }
        });

        // Handle form submit for create / edit
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const id = userIdInput.value;
            const name = nameInput.value.trim();
            const email = emailInput.value.trim();

            if (!name || !email) {
                return;
            }

            const payload = {
                name: name,
                email: email
            };

            if (id) {
                // Update existing user via REST PUT /user?id={id}
                fetch(baseUrl + '?id=' + encodeURIComponent(id), {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json().catch(() => null))
                    .then(() => {
                        closeModal();
                        showMessage('User updated');
                        loadUsers();
                    })
                    .catch(err => {
                        console.error(err);
                        showMessage('Update failed', false);
                    });
            } else {
                // Create via REST POST /user
                fetch(baseUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json().catch(() => null))
                    .then(() => {
                        closeModal();
                        showMessage('User created');
                        loadUsers();
                    })
                    .catch(err => {
                        console.error(err);
                        showMessage('Create failed', false);
                    });
            }
        });

        // Edit & Delete buttons
        function bindRowActions() {
            document.querySelectorAll('.edit-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const id = row.getAttribute('data-id');
                    const name = row.getAttribute('data-name');
                    const email = row.getAttribute('data-email');

                    openModal('edit', {
                        id,
                        name,
                        email
                    });
                });
            });

            document.querySelectorAll('.delete-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const id = row.getAttribute('data-id');

                    if (!confirm('Are you sure you want to delete this user?')) {
                        return;
                    }

                    fetch(baseUrl + '?id=' + encodeURIComponent(id), {
                            method: 'DELETE'
                        })
                        .then(res => {
                            if (res.status === 204) {
                                // remove row on no content
                                row.parentNode.removeChild(row);
                                showMessage('User deleted');
                            } else {
                                showMessage('Delete failed', false);
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showMessage('Delete error', false);
                        });
                });
            });
        }

        // Initial load
        loadUsers();
    </script>

</body>

</html>