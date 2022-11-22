<?php
    require_once "inc/init.php";
    // debug($_SESSION);

    if (isset($_POST['connexion'])) {
        // debug($_POST);

        // Etape de sécurisation des données
            foreach ($_POST as $key => $value) {
                $_POST[$key] = htmlspecialchars($value, ENT_QUOTES);
            }
        // Fin

        // Etape de vérification des données
            if (empty($_POST['username']) || empty($_POST['password'])) {
                $errorMessage = "Les identifiants sont obligatoires";
            }else{      // Si les champs sont remplis, je peux essayer de récupérer un internaute.

                // Etape de connexion
                    // Récupération d'un memebre via son pseudo.
                    $requete = $bdd->prepare("SELECT * FROM membre WHERE username = :username");
                    $requete->execute([':username' => $_POST['username']]);

                    // debug($requete->rowCount());    // rowCount(); permet de compter le nombre de résultats récupérés depuis la BDD
                    // Si j'ai un résultat, alors c'est que le pseudo est correct
                    if($requete->rowCount() == 1){
                        $user = $requete->fetch();
                        // debug($user);
                        // Maintenant que j'ai un utilisateur, je peux vérifier son mot de passe.
                        // Le mot de passe dans la BDD est hashé donc pour vérifier le mdp reçu dans le formulaire, je dois utiliser la fonction password_verify();
                        // Cette fonction attend en premier pramaètre le mdp 'normal' et en deuxième paramètre le mdp hashé. La fonction renverra TRUE ou FALSE en fonction du résultat.
                        if (password_verify($_POST['password'], $user['password'])) {

                            // Une fois le mdp vérifié, je peux stocker dans la session les informations de cet utilisateur.
                            // A partir de ce moment, il sera connecté au site web.
                            $_SESSION['membre'] = $user;

                            /*                                
                                Exercice : faire une redirection vers la page profil.php quand on est connecté.
                                Afficher un message de succès sur la page profil.php.
                                "Bonjour pseudo, bienvenu sur votre compte".
                                Dans la page profil.php, j'affiche les informations de l'utilisateur.
                            */

                            $_SESSION['successMessage'] = "Bonjour $user[username], bienvenu sur votre compte";
                            header("location:profil.php");
                            exit;

                        }else{
                            $errorMessage ="Mot de passe ou identifiant incorrects";
                        }

                    }else{
                        $errorMessage = "Mot de passe ou identifiant incorrects";
                    }
                // Fin
            }
        // Fin

    }
// debug($_SESSION);
    require_once "inc/header.php";
?>

<h1 class="text-center">Connexion</h1>

<?php if (!empty($successMessage)) { ?>
    <div class="alert alert-success col-md-6 text-center mx-auto">
        <?php echo $successMessage ?>
    </div>
<?php } ?>

<?php if (!empty($_SESSION['successMessage'])) { ?>
    <div class="alert alert-success col-md-6 text-center mx-auto">
        <?php echo $_SESSION['successMessage'] ?>
    </div>
    <?php unset($_SESSION['successMessage']); ?>
<?php } 
    // debug($_SESSION);
?>

<?php if (!empty($errorMessage)) { ?>
    <div class="alert alert-danger col-md-6 text-center mx-auto">
        <?php echo $errorMessage ?>
    </div>
<?php } ?>

<form action="" method="post" class="col-md-6 mx-auto">

<label for="username" class="form-label" >Pseudo</label>
<input type="text" placeholder="Votre Pseudo" name="username" id="username" class="form-control">

<label for="password" class="form-label">Mot de Passe</label>
<input type="password" placeholder="Votre Mot de Passe" name="password" id="password" class="form-control">

<button class="d-block mx-auto btn btn-primary mt-3" name="connexion">Connexion</button>

</form>

<?php
    require_once "inc/footer.php";
?>