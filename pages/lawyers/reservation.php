<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation - Cabinet Juridique Excellence</title>
    <link rel="stylesheet" href="../../styles/main.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Cabinet Juri dique Excellence</div>
            <ul>
                <li><a href="../index.html">Accueil</a></li>
                <li><a href="../index.html">deconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main class="reservation-container">
        <h2>Réserver une consultation</h2>
        <div class="reservation-form">
            <?php
            // Connexion à la base de données
            $conn = mysqli_connect("localhost", "root", "", "lawyer");

            if (!$conn) {
                die("Connexion échouée : " . mysqli_connect_error());
            }

            // Récupération de l'ID de l'avocat
            $lawyer_id = isset($_GET['lawyer']) ? intval($_GET['lawyer']) : null;

            // Récupération des avocats pour le champ de sélection
            $lawyers_query = "SELECT U.id_user, U.nom, A.specialites 
            FROM Utilisateur U 
            JOIN avocat_detailles A ON U.id_user = A.id_avocat 
            WHERE U.role = 'avocat'";
            $lawyers_result = mysqli_query($conn, $lawyers_query);
            ?>

            <form method="POST" action="new.php">
                <div class="form-group">
                    <label for="avocat">Choisir un avocat</label>
                    <select id="avocat" name="avocat" required>
            <option value="">Sélectionnez un avocat</option>
                 <?php
            while ($row = mysqli_fetch_assoc($lawyers_result)) {
                     $selected = (isset($_GET['lawyer']) && intval($_GET['lawyer']) === intval($row['id_user'])) ? "selected" : "";
                    echo "<option value='" . $row['id_user'] . "' $selected>Me. " . $row['nom'] . " - " . $row['specialites'] . "</option>";
            }
    ?>
     </select>
                </div>
                <div class="form-group">
                    <label for="date">Date souhaitée</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="heure">Heure souhaitée</label>
                    <select id="heure" name="heure" required>
                        <option value="">Sélectionnez une heure</option>
                        <option value="09:00">09:00</option>
                        <option value="10:00">10:00</option>
                        <option value="11:00">11:00</option>
                        <option value="14:00">14:00</option>
                        <option value="15:00">15:00</option>
                        <option value="16:00">16:00</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="motif">Motif de la consultation</label>
                    <textarea id="motif" name="motif" rows="4" required></textarea>
                </div>
                <button type="submit" name="reserve" class="btn-primary">Réserver</button>
            </form>

            <?php
            if (isset($_POST['reserve'])) {
                // Récupérer les données du formulaire
                $id_client = 1; // À remplacer par l'ID du client connecté
                $id_avocat = intval($_POST['avocat']);
                $date_consultation = $_POST['date'];
                $heure = $_POST['heure'];
                $motif = $_POST['motif'];

                // Insérer dans la table Consultation
                $insert_query = "INSERT INTO Consultation (id_client, id_avocat, date_consultation, statut)
                                 VALUES ('$id_client', '$id_avocat', '$date_consultation', 'en_attente')";

                if (mysqli_query($conn, $insert_query)) {
                    echo "<p class='success'>Votre réservation a été enregistrée avec succès !</p>";
                } else {
                    echo "<p class='error'>Erreur lors de l'enregistrement : " . mysqli_error($conn) . "</p>";
                }
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