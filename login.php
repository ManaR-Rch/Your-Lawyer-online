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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Validation
    $errors = [];
    
    if (empty($email) || empty($password)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }

    if (empty($errors)) {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id_user, nom, email, mot_de_passe, role FROM Utilisateur WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['mot_de_passe'])) {
                // Store user information in session
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_name'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] === 'avocat') {
                    header("Location: pages/dashboard/lawyer.php");
                } else {
                    header("Location:pages/lawyers/list.php");
                }
                exit;
            } else {
                $errors[] = "Email ou mot de passe incorrect.";
            }
        } else {
            $errors[] = "Email ou mot de passe incorrect.";
        }
        
        $stmt->close();
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
</head>
<body>
    <header>
        <nav>
            <div class="logo">Cabinet Juridique Excellence</div>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <!-- <li><a href="reservation.html">Réservation</a></li> -->
                <li><a href="login.php" class="active">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-container">
        <div class="auth-form">
            <h2>Connexion</h2>
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
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-primary">Se connecter</button>
                <p class="form-footer">
                    Pas encore de compte ? <a href="register.php">S'inscrire</a>
                </p>
            </form>
        </div>
    </main>

    <footer>
        <div class="copyright">
            <p>&copy; 2024 Cabinet Juridique Excellence. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>