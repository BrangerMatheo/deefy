<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPlaylistAction extends Action{

    public function execute() : string{
        if ($this->http_method === 'GET') {
            return $this->formulaire();
        }
        return $this->creerPlaylist();
    }

    private function formulaire() : string{
        return <<<HTML
            <h2>Créer une playlist</h2>
            <form method="post" action="?action=add-playlist">
                <label for="nom">Nom de la playlist :</label>
                <input type="text" id="nom" name="nom" required>
                <button type="submit">Créer</button>
            </form>
        HTML;
    }

    private function creerPlaylist() : string{
        $nom = filter_var($_POST['nom'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($nom === false || trim($nom) === '') {
            return "<p>Nom invalide.</p>" . $this->formulaire();
        }

        $playlist = new Playlist($nom);
        DeefyRepository::getInstance()->savePlaylist($playlist);

        // la playlist courante est mémorisée en session via son id
        $_SESSION['courante'] = $playlist->id;

        $renderer = new AudioListRenderer($playlist);
        return $renderer->render(Renderer::compact) . '<div class="pl-tools"><a class="btn" href="?action=add-track">Ajouter une piste</a></div>';
    }
}
