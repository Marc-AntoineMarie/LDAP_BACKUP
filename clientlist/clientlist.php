<?php
require_once '../database/db.php';

include '../database/partner_request.php';
include '../database/clients_request.php';

///////////////////// vérif des rôles ///////////////////
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Partenaire')) {
    header('Location: ../login/login.php');
    exit;
}

// Vérification : Un partenaire ne peut accéder qu'à ses propres données
if ($_SESSION['role'] === 'Partenaire') {
    if (!isset($_GET['idpartenaires']) || $_GET['idpartenaires'] != $_SESSION['partner_id']) {
        header('Location: ../login/login.php');
        exit;
    }
}
///////////////////// FIN vérif des rôles ///////////////////

//Temporaire pour le développement :
if (isset($_GET['idpartenaires'])) $idpartenaire = $_GET['idpartenaires'];
else $idpartenaire = 2;

if (isset($_POST['idpartenaire'])) $idpartenaire = $_POST['idpartenaire'];

$clientsHandler = new ClientsHandler($pdo);

// Gestion de la suppression via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $clientId = intval($_POST['id']);
    $result = $clientsHandler->deleteClient($clientId);

    header('Content-Type: application/json');
    if ($result === true) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression.']);
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI Example</title>
    <link rel="stylesheet" href="clientlist.css">
    <script src="clientlist.js"></script>
</head>
<body>
    <img src="../admin/logo/Logo-ldap.png" alt="Logo" class="logo-header">
    <?php include '../partials/header.php'; ?>

    <section class="main-section">
        <div class="title-container">
            <h1>Liste des clients pour : 
            <?php
            if (isset($idpartenaire)) {
                $partnerId = intval($idpartenaire);
                $partnerName = $clientsHandler->getPartnerNameById($partnerId);
                echo htmlspecialchars($partnerName);
            } else {
                echo "Aucun identifiant de partenaire fourni.";
                exit;
            }
            ?>
            </h1> 
        </div>

        <div class="button-container">
            <?php if ($_SESSION['role'] === 'Admin'): ?>
                <a href="addclients_form.php?idpartenaires=<?php echo $idpartenaire; ?>" class="add-button" id="add-client" style="text-decoration:none">Ajouter un client</a>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Logo</th> 
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <?php
                        	if (isset($idpartenaire)) {
                        		if ($idpartenaire == 0){
                        				echo "<th>Partenaire</th>";
                        			}
                        	}
                        ?>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="client-list">
                <?php
                if (isset($idpartenaire)) {
                    $Clients = $clientsHandler->getClientsByPartner($idpartenaire);
                    foreach ($Clients as $client) {
                        echo "<tr>";
                        echo "<td><input type=\"checkbox\" class=\"client-checkbox\"></td>";
                        echo "<td class=\"logo-cell\">";
                        echo "<div class=\"logo-placeholder\"></div>";
                        echo "</td>";
                        echo "<td class=\"card-content\">";
                        echo "<a href=\"../clientdetail/clientdetail.php?idclient=$client[idclients]\">";
                        echo "<h2>$client[Nom]</h2>";
                        echo "</a>";
                        echo "</td>";
                        echo "<td>$client[Telephone]</td>";
                        echo "<td>$client[Adresse]</td>";
                        if ($idpartenaire == 0){
                            echo "<td>$client[partenaires_idpartenaires]</td>";
                        }
                        echo "<td><button class=\"btn-delete\" data-client-id=\"$client[idclients]\">✖</button></td>";
                        echo "</tr>";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </section>

    <a href="javascript:history.back()" class="back-button">Revenir en arrière</a>
</body>
</html>
