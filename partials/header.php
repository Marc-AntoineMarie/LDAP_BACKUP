<?php
// Vérification des rôles
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: ../login/login.php');
    exit;
}
$role = $_SESSION['role'];

// Récupération de l'ID du partenaire actif
$partnerId = $_GET['idpartenaires'] ?? $_SESSION['partner_id'] ?? null;

// Si un partenaire est actif, l'enregistrer dans la session
if (isset($_GET['idpartenaires'])) {
    $_SESSION['partner_id'] = intval($_GET['idpartenaires']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP</title>
    <link rel="stylesheet" href="../partials/header.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-links">
        <?php if ($role === 'Admin'): ?>
            <a href="../admin/V1_admin.php">Liste des partenaires</a>
            <a href="../clientlist/clientlist.php<?= $partnerId ? '?idpartenaires=' . htmlspecialchars($partnerId) : '' ?>">Liste des clients</a>
        <?php elseif ($role === 'Partenaire'): ?>
            <a href="../clientlist/clientlist.php?idpartenaires=<?= htmlspecialchars($partnerId) ?>">Liste des clients</a>
        <?php endif; ?>
        <a href="../login/logout.php" class="logout">Déconnexion</a>
    </div>
</nav>
</body>
</html>
