<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$dbname = "lawyer";
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = trim($_POST['role']);
    $location = isset($_POST['location']) ? trim($_POST['location']) : null;
    $biographie = isset($_POST['biographie']) ? trim($_POST['biographie']) : null;
    $specialites = isset($_POST['specialites']) ? trim($_POST['specialites']) : null;
    $experience = isset($_POST['experience']) ? trim($_POST['experience']) : null;

    if (empty($nom) || empty($email) || empty($telephone) || empty($password) || empty($confirm_password)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }

    $stmt = $conn->prepare("SELECT id_user FROM Utilisateur WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "Cet email est déjà utilisé.";
    }
    $stmt->close();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO Utilisateur (nom, email, mot_de_passe, téléphone, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nom, $email, $hashed_password, $telephone, $role);

        if ($stmt->execute()) {
            $id_avocat = $conn->insert_id;
        
            // Si le rôle est "avocat", insérer les détails supplémentaires
            if ($role === "avocat" && $location && $biographie && $specialites && $experience) {
                $stmt = $conn->prepare("INSERT INTO avocat_detailles (id_avocat, location, biographie, specialites, experience) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $id_avocat, $location, $biographie, $specialites, $experience);
                $stmt->execute();
                $stmt->close();
            }
        
            // Redirection dynamique en fonction du rôle
            if ($role === "avocat") {
                header("Location: pages/dashboard/lawyer.php");
            } elseif ($role === "client") {
                header("Location: pages/lawyers/list.php");
            } else {
                header("Location: index.php"); // Par défaut si le rôle n'est pas spécifié ou inattendu
            }
            exit;
        }
         else {
            $errors[] = "Erreur lors de l'inscription. Veuillez réessayer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Cabinet Juridique Excellence</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .hidden {
            display: none;
        }
        .form-group {
            margin-bottom: 1em;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Cabinet Juridique Excellence</div>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                                <li><a href="login.php" class="active">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-container">
    <h2>Inscription</h2>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirmer le mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="form-group">
            <label for="role">Rôle</label>
            <select id="role" name="role" required>
                <option value="client">Client</option>
                <option value="avocat">Avocat</option>
            </select>
        </div>

        <!-- Formulaire des détails avocat -->
        <div id="avocat-details" class="hidden">
            <h3>Détails Avocat</h3>
            <div class="form-group">
                <label for="location">Localisation</label>
                <input type="text" id="location" name="location">
            </div>
            <div class="form-group">
                <label for="biographie">Biographie</label>
                <textarea id="biographie" name="biographie"></textarea>
            </div>
            <div class="form-group">
                <label for="specialites">Spécialités</label>
                <input type="text" id="specialites" name="specialites">
            </div>
            <div class="form-group">
                <label for="experience">Années d'expérience</label>
                <input type="text" id="experience" name="experience">
            </div>
        </div>

        <button type="submit">S'inscrire</button>
    </form>
</main>
<script>
    const roleSelect = document.getElementById('role');
    const avocatDetails = document.getElementById('avocat-details');

    roleSelect.addEventListener('change', () => {
        if (roleSelect.value === 'avocat') {
            avocatDetails.classList.remove('hidden');
        } else {
            avocatDetails.classList.add('hidden');
        }
    });
</script>
</body>
</html>
