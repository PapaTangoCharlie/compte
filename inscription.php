<?php

// ########## ETAPE 1 - Inclusion de init.php ##########
require_once "inc/init.php";

// ########## ETAPE 2 - Traitement des données du formulaire ##########
// Je vérifie si le formulaire a été validé.
// S'il a été validé, je peux traiter les données.
// ATTENTION : je ne peux pas traiter le formulaire s'il n'a pas été envoyé.

if (!empty($_POST)) {
    debug($_POST);

    // Etape de vérification des données
        if (empty($_POST['username'])) {
            $errorMessage = "Remplissez le champ pseudo ! <br>";
        }

        // strlen() permet de récupérer la longueur d'une chaîne d ecaractères.
        // Attention, les caractères spéciaux comptent pour 2 espaces.
        // Exemple, "éé" comptera pour 4 caractères.
        // iconv_strlen() permet de résoudre ce problème.
        // Chaque caque caractère comptera comme 1 caractère.
        // Exemple : "éé" ne comptera que pour 2 caractères.
        if (iconv_strlen(trim($_POST['username'])) < 3 || iconv_strlen(trim($_POST['username'])) > 20) {
            $errorMessage .= "Le pseudo doit contenir entre 3 et 20 caractères.<br>";
        }

        if (empty($_POST['password']) || iconv_strlen(trim($_POST['password'])) < 8) {
            $errorMessage .= "Merci d'indiquer un mot de passe minimum de 8 caractères.<br>";
        }

        if (empty($_POST['lastname']) || iconv_strlen(trim($_POST['lastname'])) > 70) {
            $errorMessage .= "Merci d'indiquer un nom maximum de 70 caractères.<br>";
        }

        if (empty($_POST['firstname']) || iconv_strlen(trim($_POST['firstname'])) > 70) {
            $errorMessage .= "Merci d'indiquer un prénom maximum de 70 caractères.<br>";
        }

        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errorMessage .= "L'e-mail n'est pas valide.<br>";
        }
    // Fin

    // Etape de sécurisation des données
        // $_POST['username'] = htmlspecialchars($_POST['username']);
        // $_POST['lastname'] = htmlspecialchars($_POST['lastname']);
        // $_POST['firstname'] = htmlspecialchars($_POST['firstname']);
        // htmlspecialchars() permet de transformer les chevrons <> et les guillemets (simples ou doubles) en leurs entitées html.
        // Cette étape est obligatoire lorsque vous récupérez des données de l'utilisateur.
        foreach ($_POST as $key => $value) {
            $_POST[$key] = htmlspecialchars($value, ENT_QUOTES);
        }
    // Fin

    // Etape envoi des données
        // Si $errorMessage est vide alors les données envoyées par l'utilisateur sont correctes, je peux donc les envoyer.
        if (empty($errorMessage)) {
            $requete = $bdd->prepare("INSERT INTO membre VALUES (NULL, :username, :password, :lastname, :firstname, :email, :status)");
            $success = $requete->execute([
                ":username"     => $_POST['username'],
                // password_hash() permet de hasher un mot de passe.
                // On doit lui indiquer en paramètre le type d'algorythme que l'on souhaite utiliser.
                // Ici, on prend l'algorithme par défault.
                ":password"     => password_hash($_POST['password'], PASSWORD_DEFAULT),
                ":lastname"     => $_POST['lastname'],
                ":firstname"    => $_POST['firstname'],
                ":email"        => $_POST['email'],
                ":status"       => "user"
            ]);

            if ($success) {
                $successMessage = "Inscription réussie";

                /*
                    Introduction à la session :
                    La session est un fichier temporaire appelé sess_(id) stocké sur le serveur.
                    Ce fichier a un identifiant unique.
                    Chaque fichier de session sera lié à un seul internaute.

                    On déclare la session avec session_start();
                        session_start(); créé la session OU récupère la session en fonction de l'ID passé dans le cookie (PHPSESSID)

                    Le fichier de session peut contenir toutes sortes d'informations, y compris des informations sensibles (mdp, carte bancaire, données de santé, etc.). Ce fichier n'est pas accessible par l'utilisateur.

                    Une fois que session_start();, on a ccès à la superglobale $_SESSION.
                    C'est dans cette superglobale que l'on va récupérer les informations de la session.
                    C'ets via celle-ci également que l'on pourra ajouter des informations à la session.
                    Pour ajouter une information, on appelle $_SESSION suivi d'un indice (ici 'successMessage') et de sa valeur.
                    Si l'indice appelé existe déjà, sa valeur sera remplacée, s'il n'existe pas, l'indice sera créé.

                    Les informations présentes dans la session ne sont pas supprimées automatiquement. Pour supprimer une information, on doit utiliser la fonction 'unset();'
                */

                $_SESSION["successMessage"] = "Inscription réussie";
                // Si ma requête a fonctionnée, je suis dirigée vers la page de connexion.
                header("location:connexion.php");
                exit;   // Après la fonction header(), on place très régulièrement une exécution 'exit;'.
                        // Le temps que le header soit exécuté et que la redirection soit faite, il peut se passer un peu de temps. Pendant ce temps là, le code est lu et exécuté. Pour éviter que ce code soit exécuté, j'ai besoin de l'exécution 'exit'. C'est important car pendant ce laps de temps très court, du code peut être injecté par un individu malveillant.
            } else {
                $errorMessage = "Erreur lors de l'enregistrement";
            }
        }
    // Fin

}

require_once "inc/header.php";

?>

<h1 class="text-center">Inscription</h1>

<?php if (!empty($successMessage)) { ?>
    <div class="alert alert-success col-md-6 text-center mx-auto">
        <?php echo $successMessage ?>
    </div>
<?php } ?>

<?php if (!empty($errorMessage)) { ?>
    <div class="alert alert-danger col-md-6 text-center mx-auto">
        <?php echo $errorMessage ?>
    </div>
<?php } ?>

<form action="" method="post" class="col-md-6 mx-auto">

    <label for="username" class="form-label">Pseudo</label>
    <input 
        type="text" 
        name="username" 
        id="username" 
        class="form-control"
        value="<?= $_POST['username'] ?? "" ?>"
    >
    <!-- Si $_POST['username]  existe alors j'affiche sa valeur SINON j'affiche uen chaîne de caractères vide.-->
    <!-- On utilise ici l'opérateur NULL COALESCENT -->
    <div class="invalid-feedback"></div>

    <label for="password" class="form-label">Mot de Passe</label>
    <input 
        type="password" 
        name="password" 
        id="password" 
        class="form-control"
        value="<?= $_POST['password'] ?? "" ?>"
    >
    <div class="invalid-feedback"></div>

    <label for="lastname" class="form-label">Nom</label>
    <input 
        type="text" 
        name="lastname" 
        id="lastname" 
        class="form-control"
        value="<?= $_POST['lastname'] ?? "" ?>"
    >
    <div class="invalid-feedback"></div>

    <label for="firstname" class="form-label">Prénom</label>
    <input 
        type="text" 
        name="firstname" 
        id="firstname" 
        class="form-control"
        value="<?= $_POST['firstname'] ?? "" ?>"
    >
    <div class="invalid-feedback"></div>

    <label for="email" class="form-label">Email</label>
    <input 
        type="email" 
        name="email" 
        id="email" 
        class="form-control"
        value="<?= $_POST['email'] ?? "" ?>"
    >
    <div class="invalid-feedback"></div>

    <button class="btn btn-success d-block mx-auto mt-3">S'inscrire</button>

</form>

<?php

require_once "inc/footer.php";

?>