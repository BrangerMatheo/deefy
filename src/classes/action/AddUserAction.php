<?php

namespace iutnc\deefy\action;

class AddUserAction extends Action{

    public function execute() : string{
        if ($this->http_method === 'GET') {
            return $this->formulaire();
        }
        return $this->afficherInscription();
    }

    private function formulaire() : string{
        return <<<HTML
            <h2>Inscription</h2>
            <form method="post" action="?action=add-user">
                <p><label>Nom : <input type="text" name="nom" required></label></p>
                <p><label>Email : <input type="email" name="email" required></label></p>
                <p><label>Âge : <input type="number" name="age" min="0" max="150" required></label></p>
                <button type="submit">Connexion</button>
            </form>
        HTML;
    }

    private function afficherInscription() : string{
        $nom = filter_var($_POST['nom'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $age = filter_var($_POST['age'] ?? '', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => 150]
        ]);

        if ($nom === '' || $email === false || $age === false) {
            return "<p>Données invalides.</p>" . $this->formulaire();
        }

        $email = htmlspecialchars($email);

        return "<p>Nom : <b>$nom</b>, Email : <b>$email</b>, Age : <b>$age</b> ans</p>";
    }
}