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
            return '<div class="hero"><h1>Aucune playlist</h1><p>Créez votre première playlist pour commencer.</p><div class="actions"><a class="btn" href="?action=add-playlist">Créer une playlist</a></div></div>';
        }

        $courante = $_SESSION['courante'] ?? null;
        $html = '';
        foreach ($playlists as $liste) {
            // on recharge la playlist avec ses pistes pour l'afficher
            $playlist = $repo->findPlaylistById($liste->id);
            $renderer = new AudioListRenderer($playlist);
            $html .= $renderer->render(Renderer::compact);
            if ($playlist->id === $courante) {
                $html .= '<div class="pl-tools"><span>Playlist courante</span><a class="btn ghost" href="?action=add-track">Ajouter une piste</a></div>';
            } else {
                $html .= '<div class="pl-tools"><a class="btn ghost" href="?action=playlist&select=' . $playlist->id . '">Choisir cette playlist</a></div>';
            }
        }
        return $html;
    }
}
