<?php
require_once("../database/db.php");
include("../database/Annuaire_request.php");
include '../partials/header.php';

if (!isset($pdo)) {
    die("Erreur : La connexion PDO n'est toujours pas initialisée.");
}

try {
    $annuaireManager = new AnnuaireManager($pdo);
    $clientsId = intval($_GET['idclients']);
    $contacts = $annuaireManager->getAnnuaireByClient($clientsId);

    if (empty($contacts)) {
        $message = "Aucun contact trouvé pour le client ID : $clientsId.";
    }
} catch (Exception $e) {
    die("Erreur lors de la récupération des contacts : " . $e->getMessage());
}

////////////////API AJOUT D'UN USER///////////////

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input['action'] === 'add_user_annuaire') {
        $annuaireId = intval($input['annuaireId']);
        $prenom = $input['prenom'];
        $nom = $input['nom'];
        $email = $input['email'];
        $societe = $input['societe'];
        $adresse = $input['adresse'];
        $ville = $input['ville'];
        $telephone = $input['telephone'];
        $commentaire = $input['commentaire'];

        $userAnnuaireManager = new UserAnnuaireManager($pdo);
        $result = $userAnnuaireManager->addUserToAnnuaire($annuaireId, $prenom, $nom, $email, $societe, $adresse, $ville, $telephone, $commentaire);

        echo json_encode(['success' => $result]);
        exit;
    }

    if ($input['action'] === 'delete_user_annuaire') {
        $userId = intval($input['userId']);

        $userAnnuaireManager = new UserAnnuaireManager($pdo);
        $result = $userAnnuaireManager->deleteUserFromAnnuaire($userId);

        echo json_encode(['success' => $result]);
        exit;
    }
}
///////////////////////////////////////////////////////////////////////

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Annuaires</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="annuaire.css">
</head>
<body>
    <aside class="sidebar bg-light">
        <div class="sidebar-header text-center">
            <img src="../logo/Logo-ldap.png" alt="Logo" class="img-fluid mb-3">
            <?php
            if (isset($_GET['idclients'])) {
                $clientName = $annuaireManager->getClientName($clientsId)[0]['Nom'] ?? 'Client inconnu';
                echo "<h5>$clientName</h5>";
            } else {
                echo "<p>Aucun identifiant de client fourni.</p>";
                exit;
            }
            ?>
        </div>
    </aside>

    <div class="container-fluid">
        <div class="row">
            <main class="col-md-9 offset-md-3">
                <header class="d-flex justify-content-between align-items-center mt-4">
                    <h2>Administration des Annuaires</h2>
                    <div>
                        <form method="post" style="display: inline;">
                            <button name="export_csv" class="btn btn-outline-primary">Exporter CSV</button>
                        </form>
                        <button id="add-contact-btn" class="btn btn-primary">Ajouter un contact</button>
                    </div>
                </header>

                <!-- Tableau des contacts -->
                <section class="mt-4">
                    <h4>Liste des Contacts</h4>
                    <table class="table table-striped table-hover mt-3">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Numéro</th>
                                <th>Code</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="contact-list">
                            <?php
                            foreach ($contacts as $contact) {
                                echo "<tr>
                                    <td><input type='checkbox' class='contact-checkbox'></td>
                                    <td>" . htmlspecialchars($contact['Nom']) . "</td>
                                    <td>" . htmlspecialchars($contact['Adresse']) . "</td>
                                    <td>" . htmlspecialchars($contact['Telephone']) . "</td>
                                    <td>" . htmlspecialchars($contact['Email']) . "</td>
                                    <td>
                                        <button class='btn btn-danger btn-sm btn-delete' data-id='" . $contact['idAnnuaire'] . "'>✖</button>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </section>
            </main>
        </div>
    </div>

    <!-- Modal pour Ajouter un Contact -->
    <div class="modal fade" id="addContactModal" tabindex="-1" aria-labelledby="addContactModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un nouveau contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-contact-form">
                        <div class="mb-3">
                            <label for="firstname" class="form-label">Prénom :</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Nom :</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email :</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="extension" class="form-label">Extension :</label>
                            <input type="text" class="form-control" id="extension" name="extension">
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Code :</label>
                            <input type="text" class="form-control" id="code" name="code">
                        </div>
                        <button type="button" class="btn btn-primary" id="submit-contact">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="annuaire.js"></script>
</body>
</html>
