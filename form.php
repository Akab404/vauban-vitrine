<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Page : contact.php
$votre_adresse_mail = $_ENV['MAIL_ADDRESSES'];
// si le bouton "Envoyer" est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $params = [
        'prenom' => $_POST['prenom'],
        'nom' => $_POST['nom'],
        'email' => $_POST['email'],
        'telephone' => $_POST['telephone'],
        'code_postal' => $_POST['code_postal'],
        'ville' => $_POST['ville'],
        'marque' => $_POST['marque'],
        'cgu' => $_POST['cgu']
    ];

    // Utilisez http_build_query pour créer la chaîne de requête
    $query_string = http_build_query($params);

// Ajouter le '?' au début
    $query_string = 'Location: /contact.html?' . $query_string;
    $isValid = true;
    //vérification des champs
    if (empty($_POST['nom'])) {
        $query_string = $query_string . "&error=name";
        header($query_string);
        $isValid = false;
    }
    if (empty($_POST['prenom'])) {
        $query_string = $query_string . "&error=firstname";
        header($query_string);
        $isValid = false;
    }
    if (empty($_POST['telephone'])) {
        $query_string .=  $query_string . "&error=telephone";
        header($query_string);
        $isValid = false;
    }
    if (empty($_POST['code_postal'])) {
        $query_string .=  $query_string . "&error=zipcode";
        header($query_string);
        $isValid = false;
    }
    if (empty($_POST['ville'])) {
        $query_string .=  $query_string . "&error=city";
        header($query_string);
        $isValid = false;
    }
    if (empty($_POST['marque'])) {
        $query_string .=  $query_string . "&error=brand";
        header($query_string);
        $isValid = false;
    }
    if (empty($_POST['cgu']) || $_POST['cgu'] !== 'on') {
        $query_string .=  $query_string . "&error=cgu";
        header($query_string);
        $isValid = false;
    }

    $motorisations = "";
    foreach (['hybriderechargeable', 'electrique', 'hybride', 'essence', 'diesel'] as $motorisation) {
        if (!empty($_POST[$motorisation])) {
            $motorisations .= ucfirst($motorisation) . " : " . $_POST[$motorisation] . "\n";
        }
    }

    // Si tous les champs sont valides, envoyer l'email
    if ($isValid) {
        $mail_denvoi = $_ENV['MAIL_ESTORIK'];
        $mail_de_lutilisateur = $_POST['email'];
        $telephone_de_lutilisateur = $_POST['telephone'];
        $code_postal_de_lutilisateur = $_POST['code_postal'];
        $ville_de_lutilisateur = $_POST['ville'];
        $nom_de_lutilisateur = $_POST['nom'];
        $prenom_de_lutilisateur = $_POST['prenom'];
        $marque_de_lutilisateur = $_POST['marque'];

        // On renseigne les entêtes du mail
        $entetes_du_mail = [];
        $entetes_du_mail[] = 'MIME-Version: 1.0';
        $entetes_du_mail[] = 'Content-type: text/html; charset=UTF-8';
        $entetes_du_mail[] = 'From: Groupe Vauban <' . $mail_denvoi . '>';
        $entetes_du_mail[] = 'Reply-To: Groupe Vauban <' . $mail_de_lutilisateur . '>';

    //ajoute des sauts de ligne entre chaque headers
    $entetes_du_mail = implode("\r\n", $entetes_du_mail);

    //base64_encode() est fait pour permettre aux informations binaires d'être manipulées par les systèmes qui ne gèrent pas correctement les 8 bits (=?UTF-8?B? est une norme afin de transmettre correctement les caractères de la chaine)
    $sujet = '[ëstorik] Nouveau lead Essai véhicule';

        // Contenu du message
        $resultat = '';
        $resultat .= 'Email : ' . $mail_de_lutilisateur . "\n";
        $resultat .= 'Téléphone : ' . $telephone_de_lutilisateur . "\n";
        $resultat .= 'Code postal : ' . $code_postal_de_lutilisateur . "\n";
        $resultat .= 'Ville : ' . $ville_de_lutilisateur . "\n";
        $resultat .= 'Nom : ' . $nom_de_lutilisateur . "\n";
        $resultat .= 'Prénom : ' . $prenom_de_lutilisateur . "\n";
        $resultat .= 'Marque : ' . $marque_de_lutilisateur . "\n";
        $resultat .= "Motorisations : \n$motorisations";

        $message = htmlentities($resultat, ENT_QUOTES, 'UTF-8');
        $message = nl2br($message); // Sauts de ligne HTML

        // Configuration de PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = function ($str) {
                file_put_contents(__DIR__ . '/var/log/mail.log', $str, FILE_APPEND);
            };
            $mail->isSMTP();
            $mail->CharSet = "UTF-8";
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "tls";
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->Username = $_ENV['MAIL_USER'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->Port = $_ENV['MAIL_PORT'];

            $mail->setFrom($mail_denvoi, 'Groupe Vauban');

            // Ajout des destinataires en copie cachée (BCC)
            $mail->addBCC("Nawess@estorik.com");
            $mail->addBCC("David@estorik.com");
            $mail->addAddress("src@vauban-groupe.fr");
            $mail->addAddress("jccadon@vauban-groupe.fr");
            $mail->addAddress("oheinrich@vauban-groupe.fr");

            $mail->Subject = $sujet;
            $mail->isHTML();
            $mail->Body = $message;

            $mail->send();
            header("Location: /contact.html?success=true");
            exit();
        } catch (Exception $e) {
            header($query_string . "&success=false");
            exit();
        }
    } else {
        header($query_string . "&success=false");
        exit();
    }

} else {
    header("Location: /contact.html");
    exit();
}
