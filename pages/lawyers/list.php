<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Avocats - Cabinet Juridique Excellence</title>
    <link rel="stylesheet" href="../../styles/main.css">
    <link rel="stylesheet" href="../../styles/lawyers.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Cabinet Juridique Excellence</div>
            <ul>
                <li><a href="../../index.html">Accueil</a></li>
                <li><a href="pages/lawyers/list.php" class="active">Nos Avocats</a></li> 
                <li><a href="../../index.html" class="active">deconnexion</a></li> 
            </ul>
        </nav>
    </header>

    <main class="lawyers-container">
        <h1>Nos Avocats</h1>
        
        <div class="lawyers-grid">
            <?php
            // Connexion à la base de données
            $conn = mysqli_connect("localhost", "root", "", "lawyer");

            if (!$conn) {
                die("Connexion échouée : " . mysqli_connect_error());
            }

            // Récupération des avocats et de leurs détails
            $query = "SELECT U.id_user, U.nom, A.location, A.biographie, A.specialites, A.experience
                      FROM Utilisateur U
                      JOIN avocat_detailles A ON U.id_user = A.id_avocat
                      WHERE U.role = 'avocat'";

            $result = mysqli_query($conn, $query);

            // Affichage des avocats récupérés
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='lawyer-card'>";
                    echo "<div class='lawyer-image'>";
                    echo "<img src='../../images/lawyer_placeholder.jpg' alt='Me. " . $row['nom'] . "'>";
                    echo "</div>";
                    echo "<div class='lawyer-info'>";
                    echo "<h2>Me. " . $row['nom'] . "</h2>";
                    echo "<p class='specialty'>Spécialités : " . $row['specialites'] . "</p>";
                    echo "<p class='experience'>" . $row['experience'] . " d'expérience</p>";
                    echo "<p class='location'>Localisation : " . $row['location'] . "</p>";
                    echo "<p class='bio'>" . $row['biographie'] . "</p>";
                    echo "<a href='profile.php?id=" . $row['id_user'] . "' class='btn-primary'>Voir le profil</a>";
                    echo "<a href='reservation.php?lawyer=" . $row['id_user'] . "' class='btn-secondary'>Prendre rendez-vous</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "Aucun avocat trouvé.";
            }

            // Fermer la connexion
            mysqli_close($conn);
            ?>
        </div>
    </main>

    <footer>
        <div class="copyright">
            <p>&copy; 2024 Cabinet Juridique Excellence. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
