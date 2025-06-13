document.addEventListener('DOMContentLoaded', function() {
    const userForm = document.getElementById('userForm');
    const userTable = document.getElementById('userTable') ? document.getElementById('userTable').querySelector('tbody') : null;

    function loadUsers() {
        fetch('user_management.php?action=read')
            .then(response => response.json())
            .then(data => {
                if (userTable) {
                    userTable.innerHTML = '';
                    data.forEach(user => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${user.id}</td>
                            <td>${user.nom}</td>
                            <td>${user.prenom}</td>
                            <td>${user.identifiant}</td>
                            <td>${user.role}</td>
                            <td>
                                <button onclick="editUser(${user.id})">Modifier</button>
                                <button onclick="deleteUser(${user.id})">Supprimer</button>
                            </td>
                        `;
                        userTable.appendChild(row);
                    });
                }
            });
    }

    if (userForm) {
        userForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(userForm);
            fetch('user_management.php?action=' + (formData.get('userId') ? 'update' : 'create'), {
                    method: 'POST',
                    body: formData
                }).then(response => response.text())
                .then(() => {
                    userForm.reset();
                    if (userTable) {
                        loadUsers();
                    } else {
                        window.location.href = "liste.html";
                    }
                });
        });
    }

    window.editUser = function(id) {
        fetch('user_management.php?action=read&id=' + id)
            .then(response => response.json())
            .then(user => {
                userForm.querySelector('#userId').value = user.id;
                userForm.querySelector('#nom').value = user.nom;
                userForm.querySelector('#prenom').value = user.prenom;
                userForm.querySelector('#identifiant').value = user.identifiant;
                userForm.querySelector('#role').value = user.role;
            });
    };

    window.deleteUser = function(id) {
        fetch('user_management.php?action=delete&id=' + id)
            .then(response => response.text())
            .then(() => {
                loadUsers();
            });
    };

    if (userTable) {
        loadUsers();
    }
});
