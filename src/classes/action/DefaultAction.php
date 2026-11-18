<?php

namespace iutnc\deefy\action;

class DefaultAction extends Action{

    public function execute() : string{
        return <<<HTML
<div class="hero">
    <h1>Bienvenue sur Deefy</h1>
    <p>Créez vos playlists, ajoutez vos pistes et écoutez-les directement depuis le lecteur en bas de page.</p>
    <div class="actions">
        <a class="btn" href="?action=playlist">Mes playlists</a>
        <a class="btn ghost" href="?action=add-playlist">Créer une playlist</a>
    </div>
</div>
HTML;
    }

}
