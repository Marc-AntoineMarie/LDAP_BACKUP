<?php

//Gestion de l'affichage des clients
class ShowClientForm {

    private $pdo; 
    private $ClientsRecoverySQLRequest = "SELECT * FROM Clients";
    private $ClientsRecoveryByPartenaireSQLRequest = "SELECT * FROM Clients WHERE partenaires_idpartenaires = [0] ";
    private $ClientsRecoveryByIdRequest = "SELECT * FROM Clients WHERE idclients = [0] ";
    private $ClientsUpdateRequest = "UPDATE Clients SET Nom = \"[1]\", Email = \"[2]\", Telephone = \"[3]\", 
    		Adresse = \"[4]\", Plateforme = \"[5]\", PlateformeURL = \"[6]\" WHERE idclients = [0] ";
    private $PlateformeRecoverySQLRequest = "SELECT * FROM Plateformes ORDER BY PlateformeNom ASC ";

    //Constructeur pour initialiser la connexion PDO
    function __construct($pdo) {
        $this->pdo = $pdo; 
    }

    //Récupération de tous les clients
    function ClientsRecovery(){

        $stmt = $this->pdo->prepare($this->ClientsRecoverySQLRequest);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupération des clients d'un partenaire
    function ClientsRecoveryByPartenaire($idpartenaire) {
				$sqlrequest = str_replace("[0]", $idpartenaire,$this->ClientsRecoveryByPartenaireSQLRequest);
        $stmt = $this->pdo->prepare($sqlrequest);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
     // Récupération d'un client par son id
    function ClientsRecoveryById($idclient) {
				$sqlrequest = str_replace("[0]", $idclient,$this->ClientsRecoveryByIdRequest);
        $stmt = $this->pdo->prepare($sqlrequest);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Enregistrement des données d'un client
    function ClientsUpdate($idclient,$nom="",$email="",$tel="",$adresse="",$plateforme="",$plateformeurl="") {
				$sqlrequest = str_replace("[0]", $idclient,$this->ClientsUpdateRequest);
				$sqlrequest = str_replace("[1]", $nom,$sqlrequest);
				$sqlrequest = str_replace("[2]", $email,$sqlrequest);
				$sqlrequest = str_replace("[3]", $tel,$sqlrequest);
				$sqlrequest = str_replace("[4]", $adresse,$sqlrequest);
				$sqlrequest = str_replace("[5]", $plateforme,$sqlrequest);
				$sqlrequest = str_replace("[6]", $plateformeurl,$sqlrequest);
        $stmt = $this->pdo->prepare($sqlrequest);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //Récupération des plateformes possibles pour affichage
    function PlateformeRecovery(){

        $stmt = $this->pdo->prepare($this->PlateformeRecoverySQLRequest);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Ajouter un client à partir d'un formulaire
    function AddClientRecovery($nom, $email, $telephone, $adresse, $partenaires_idpartenaires) {
        //préparation de la requête SQL
        $sql_clients = "INSERT INTO Clients (Nom, Email, Telephone, Adresse, partenaires_idpartenaires)
                        VALUES (:Nom, :Email, :Telephone, :Adresse, :Partenaires_idpartenaires)";

        //préparation de la requete avec PDO
        $stmt_client = $this->pdo->prepare($sql_clients);

        //lier les paramètres aux valeurs provenant du formulaire
        $stmt_client->bindParam(":Nom", $nom, PDO::PARAM_STR);
        $stmt_client->bindParam(":Email", $email, PDO::PARAM_STR);
        $stmt_client->bindParam(":Telephone", $telephone, PDO::PARAM_INT);
        $stmt_client->bindParam(":Adresse", $adresse, PDO::PARAM_STR);
        $stmt_client->bindParam(":Partenaires_idpartenaires", $partenaires_idpartenaires, PDO::PARAM_INT);

        try  {
            $result = $stmt_client->execute();
            if($result) {
                return true;
        } else {
            $errorInfo = $stmt_client->errorInfo();
            return "Erreur lors de l'insertion : " . $errorInfo[2];
        }
    } catch (PDOException $e) {
        return "Erreur PDO : ". $e->getMessage();
        }
    }

    //Traitement du formulaire d'ajout clients
    function processClientsForm($formData) {
        //validation des données
        $nom = htmlspecialchars($formData['Nom']);
        $email = htmlspecialchars($formData['Email']);
        $telephone = intval(preg_replace('/\D/', '', $formData['Telephone']));
        $adresse = htmlspecialchars($formData['Adresse']);
        $partenaires_idpartenaires = isset($formData['Partenaire_idpartenaires']) ? htmlspecialchars($formData['Partenaire_idpartenaires']) : null;

        if (empty($nom) || empty($email) || empty($telephone) || empty($adresse) || empty($partenaires_idpartenaires)) {
            return "Veuillez remplir tous les champs obligatoires.";
   			}

        //Ajouter le partenaire
        return $this->AddClientRecovery($nom, $email, $telephone, $adresse, $partenaires_idpartenaires);
    }
}

// Instance de la class ShowClientForm
$ClientsForm = new ShowClientForm($pdo);




//gestion d'ajout des clients en lien avec addclients_form.php
class ClientsHandler {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Récupérer tous les clients
    public function getAllClients() {
        $sql = "SELECT * FROM Clients";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les clients d'un partenaire spécifique
    public function getClientsByPartner($partnerId) {
        $sql = "SELECT * FROM Clients WHERE partenaires_idpartenaires = :partnerId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':partnerId', $partnerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter un client
    public function addClient($nom, $email, $telephone, $adresse, $plateforme, $plateformeURL, $partnerId) {
        try {
            $sql = "INSERT INTO Clients (Nom, Email, Telephone, Adresse, Plateforme, PlateformeURL, partenaires_idpartenaires) 
                    VALUES (:nom, :email, :telephone, :adresse, :plateforme, :plateformeURL, :partnerId)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':plateforme', $plateforme);
            $stmt->bindParam(':plateformeURL', $plateformeURL);
            $stmt->bindParam(':partnerId', $partnerId);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return "Erreur lors de l'ajout du client : " . $e->getMessage();
        }
    }

    // Mise à jour d'un client
    public function updateClient($clientId, $nom, $email, $telephone, $adresse, $plateforme, $plateformeURL) {
        $sql = "UPDATE Clients 
                SET Nom = :nom, Email = :email, Telephone = :telephone, Adresse = :adresse, Plateforme = :plateforme, PlateformeURL = :plateformeURL 
                WHERE idclients = :clientId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':plateforme', $plateforme);
        $stmt->bindParam(':plateformeURL', $plateformeURL);
        $stmt->execute();
        return true;
    }

    // Récupération d'un client par ID
    public function getClientById($clientId) {
        $sql = "SELECT * FROM Clients WHERE idclients = :clientId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupération des plateformes
    public function getPlatforms() {
        return [
            'Wazo' => [
                'Aquitaine numérique' => '141.94.251.137',
                'Aquitaine numérique 2' => '141.94.69.47',
                'LDS Solution' => '51.77.245.92',
                'Profibre' => '146.59.153.66',
                'Squartis' => '217.182.68.135'
            ],
            'OVH' => ['OVH URL' => 'fr.proxysip.eu'],
            'Yeastar' => ['Yeastar URL' => '192.168.1.150']
        ];
    }

    // Traitement du formulaire
    public function processAddClientForm($formData, $partnerId) {
        $nom = trim($formData['Nom'] ?? '');
        $email = trim($formData['Email'] ?? '');
        $telephone = trim($formData['Telephone'] ?? '');
        $adresse = trim($formData['Adresse'] ?? '');
        $plateforme = trim($formData['Plateforme'] ?? '');
        $plateformeURL = trim($formData['PlateformeURL'] ?? '');

        if (empty($nom) || empty($email) || empty($telephone) || empty($plateforme)) {
            return "Tous les champs obligatoires doivent être remplis.";
        }

        return $this->addClient($nom, $email, $telephone, $adresse, $plateforme, $plateformeURL, $partnerId);
    }

    //récupérer le nom du partenaire :)
    public function getPartnerNameById($partnerId) {
        $sql = "SELECT Nom FROM Partenaires WHERE idpartenaires = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $partnerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['Nom'] ?? 'Inconnu';
    }

    // Suppression d'un client par ID
    public function deleteClient($clientId) {
        try {
            // Vérification pour éviter la suppression d'un client non autorisé au cas où on donnerais un accès a un partenaire un jours. (comme ça c'est fais)
            if ($_SESSION['role'] === 'Partenaire') {
                $sql = "SELECT partenaires_idpartenaires FROM Clients WHERE idclients = :clientId";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($result['partenaires_idpartenaires'] != $_SESSION['partner_id']) {
                    return "Vous n'avez pas l'autorisation de supprimer ce client.";
                }
            }
    
            $sql = "DELETE FROM Clients WHERE idclients = :clientId";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return "Erreur lors de la suppression du client : " . $e->getMessage();
        }
    }
    

    
}

$clientsHandler = new ClientsHandler($pdo);
?>
