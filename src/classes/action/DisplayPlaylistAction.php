<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action{

    public function execute() : string{
        $repo = DeefyRepository::getInstance();

        // sélection de la playlist courante
        if (isset($_GET['select'])) {
            $id = filter_var($_GET['select'], FILTER_VALIDATE_INT);
            if ($id !== false && $repo->findPlaylistById($id) !== null) {
                $_SESSION['courante'] = $id;
            }
        }

        $playlists = $repo->findAllPlaylists();
        if (empty($playlists)) {
            return '<p>Aucune playlist. <a href="?action=add-playlist">Créer une playlist</a></p>';
        }

        $courante = $_SESSION['courante'] ?? null;
        $html = '';
        foreach ($playlists as $liste) {
            // on recharge la playlist avec ses pistes pour l'afficher
            $playlist = $repo->findPlaylistById($liste->id);
            $renderer = new AudioListRenderer($playlist);
            $html .= $renderer->render(Renderer::compact);
            if ($playlist->id === $courante) {
                $html .= '<p><b>Playlist courante</b> : <a href="?action=add-track">Ajouter une piste</a></p>';
            } else {
                $html .= '<p><a href="?action=playlist&select=' . $playlist->id . '">Choisir cette playlist</a></p>';
            }
            $html .= '<hr>';
        }
        return $html;
    }
}
