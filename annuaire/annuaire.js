document.addEventListener("DOMContentLoaded", function() {
    /////////////////////////////
    // 📌 Gestion des Contacts //
    /////////////////////////////

    const addClientBtn = document.getElementById('add-client');
    const clientList = document.getElementById('client-list');
    const selectAllCheckbox = document.getElementById('select-all');

    // 🟢 Ajouter un client (local & backend)
    document.addEventListener("DOMContentLoaded", function() {
        const submitContactBtn = document.getElementById('submit-contact');
    
        if (submitContactBtn) {
            submitContactBtn.addEventListener('click', function () {
                const firstname = document.getElementById('firstname').value;
                const lastname = document.getElementById('lastname').value;
                const email = document.getElementById('email').value;
                const extension = document.getElementById('extension').value;
                const code = document.getElementById('code').value;
    
                if (firstname && lastname && email && extension && code) {
                    fetch('annuaire.php?idclients=' + (new URLSearchParams(window.location.search)).get('idclients'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'add_contact',
                            firstname: firstname,
                            lastname: lastname,
                            email: email,
                            extension: extension,
                            code: code,
                        }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Contact ajouté avec succès');
                                location.reload();
                            } else {
                                alert('Erreur : ' + data.message);
                            }
                        })
                        .catch(error => console.error('Erreur :', error));
                } else {
                    alert('Veuillez remplir tous les champs.');
                }
            });
        }
    });

    /////////////////////////// AJOUT CONTACT //////////////////////////////
    document.getElementById('submit-contact').addEventListener('click', function () {
        const annuaireId = document.getElementById('annuaire-id').value; // ID de l'annuaire
        const prenom = document.getElementById('firstname').value;
        const nom = document.getElementById('lastname').value;
        const email = document.getElementById('email').value;
        const societe = document.getElementById('societe').value;
        const adresse = document.getElementById('adresse').value;
        const ville = document.getElementById('ville').value;
        const telephone = document.getElementById('telephone').value;
        const commentaire = document.getElementById('commentaire').value;
    
        fetch('annuaire.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'add_user_annuaire',
                annuaireId: annuaireId,
                prenom: prenom,
                nom: nom,
                email: email,
                societe: societe,
                adresse: adresse,
                ville: ville,
                telephone: telephone,
                commentaire: commentaire,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Contact ajouté avec succès');
                location.reload(); // Recharge pour actualiser la liste
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => console.error('Erreur :', error));
    });
    

    // 🟢 Suppression d'un contact existant
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const row = button.closest('tr');
            const entryId = button.getAttribute('data-id');
            
            fetch('annuaire.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete_contact',
                    id: entryId,
                }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        row.remove();
                        alert('Contact supprimé avec succès');
                    } else {
                        alert('Erreur : ' + data.message);
                    }
                })
                .catch(error => console.error('Erreur :', error));
        });
    });

    // 🟢 Sélectionner/Désélectionner tous les contacts
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.client-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
                checkbox.closest('tr').style.backgroundColor = checkbox.checked ? '#e9f5ff' : '';
            });
        });
    }

    // 🟢 Gestion de chaque case à cocher
    document.querySelectorAll('.client-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = checkbox.closest('tr');
            row.style.backgroundColor = checkbox.checked ? '#e9f5ff' : '';
        });
    });

    /////////////////////////////
    // 📌 Gestion Annuaire ID //
    /////////////////////////////

    const btnGererAnnuaire = document.getElementById('gerer-annuaire');
    if (btnGererAnnuaire) {
        btnGererAnnuaire.addEventListener('click', function(event) {
            event.preventDefault(); // Empêche le rafraîchissement de la page

            const clientId = this.getAttribute('data-id');
            if (clientId) {
                window.location.href = `../annuaire/annuaire.php?idclients=${clientId}`;
            } else {
                console.error("ID client manquant sur le bouton Gérer l'annuaire.");
            }
        });
    } else {
        console.warn("Le bouton 'Gérer l'annuaire' n'a pas été trouvé dans le DOM.");
    }
});
