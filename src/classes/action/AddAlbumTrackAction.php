<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;

class AddAlbumTrackAction extends Action{

    public function execute() : string{
        $playlist = $this->playlistCourante();
        if ($playlist === null) {
            return '<p>Aucune playlist courante. <a href="?action=add-playlist">Créer une playlist</a> ou <a href="?action=playlist">en choisir une</a></p>';
        }

        if ($this->http_method === 'GET') {
            return $this->formulaire();
        }
        return $this->ajouterPiste($playlist);
    }

    private function playlistCourante() : ?Playlist{
        $id = $_SESSION['courante'] ?? null;
        if (!is_int($id)) {
            return null;
        }
        return DeefyRepository::getInstance()->findPlaylistById($id);
    }

    private function formulaire() : string{
        $options = '';
        for ($i = 0; $i <= AudioTrack::TRAP; $i++) {
            $nomGenre = AudioTrack::getGenreAsString($i);
            $options .= "<option value=\"$i\">$nomGenre</option>";
        }

        return <<<HTML
            <h2>Ajouter une piste</h2>
            <form method="post" action="?action=add-track" enctype="multipart/form-data">
                <p><label>Titre : <input type="text" name="titre" required></label></p>
                <p><label>Artiste : <input type="text" name="artiste" required></label></p>
                <p><label>Album : <input type="text" name="album" required></label></p>
                <p><label>Année : <input type="number" name="annee" min="0" required></label></p>
                <p><label>Numéro de piste : <input type="number" name="numero" min="1" required></label></p>
                <p><label>Genre : <select name="genre">{$options}</select></label></p>
                <p><label>Durée (secondes) : <input type="number" name="duree" min="0" required></label></p>
                <p><label>Fichier audio (.mp3) : <input type="file" name="userfile" accept=".mp3,audio/mpeg" required></label></p>
                <button type="submit">Ajouter</button>
            </form>
        HTML;
    }

    private function ajouterPiste(Playlist $playlist) : string{
        $titre = filter_var($_POST['titre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $artiste = filter_var($_POST['artiste'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $album = filter_var($_POST['album'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $annee = filter_var($_POST['annee'] ?? '', FILTER_VALIDATE_INT);
        $numero = filter_var($_POST['numero'] ?? '', FILTER_VALIDATE_INT);
        $duree = filter_var($_POST['duree'] ?? '', FILTER_VALIDATE_INT);
        $genre = filter_var($_POST['genre'] ?? '', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => AudioTrack::TRAP]
        ]);

        if ($titre === '' || $artiste === '' || $album === ''
            || $annee === false || $numero === false
            || $duree === false || $duree < 0 || $genre === false) {
            return "<p>Données invalides.</p>" . $this->formulaire();
        }

        $fichier = $this->sauvegarderFichier();
        if ($fichier === null) {
            return "<p>Fichier audio invalide ou absent : seul un fichier .mp3 est accepté.</p>" . $this->formulaire();
        }

        $piste = new AlbumTrack($titre, $fichier, $album, $numero);
        $piste->setArtiste($artiste);
        $piste->setAnnee($annee);
        $piste->setDuree($duree);
        $piste->setGenre($genre);

        // sauvegarde de la piste puis rattachement à la playlist courante
        $repo = DeefyRepository::getInstance();
        $repo->saveTrack($piste);
        $repo->addTrackToPlaylist($playlist->id, $piste->id);
        $playlist->ajouterPiste($piste);

        $renderer = new AudioListRenderer($playlist);
        return $renderer->render(Renderer::compact) . '<a href="?action=add-track">Ajouter encore une piste</a>';
    }

    /**
     * Enregistre le fichier audio choisi dans le formulaire (champ "userfile").
     * Retourne le nom du fichier stocké dans audio/, ou null si le fichier est refusé.
     */
    private function sauvegarderFichier() : ?string{
        if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $nom = $_FILES['userfile']['name'];
        $tmp = $_FILES['userfile']['tmp_name'];

        // extension .mp3 obligatoire
        if (strtolower(pathinfo($nom, PATHINFO_EXTENSION)) !== 'mp3') {
            return null;
        }

        // le type MIME envoyé par le navigateur est falsifiable : on analyse le fichier lui-même
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp);
        if (!in_array($mime, ['audio/mpeg', 'audio/mp3', 'application/octet-stream'], true)) {
            return null;
        }

        $nomFichier = bin2hex(random_bytes(8)) . '.mp3';
        $dossier = __DIR__ . '/../../../audio/';

        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }

        if (move_uploaded_file($tmp, $dossier . $nomFichier)) {
            return $nomFichier;
        }
        return null;
    }
}
