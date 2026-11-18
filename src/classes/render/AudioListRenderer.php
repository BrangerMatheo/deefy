<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;

class AudioListRenderer implements Renderer
{
    private AudioList $audioList;

    public function __construct(AudioList $audioList)
    {
        $this->audioList = $audioList;
    }

    public function render(int $selector): string
    {
        $tracksHtml = '';
        foreach ($this->audioList->pistes as $track) {
            $renderer = RendererFactory::getRenderer($track);
            $tracksHtml .= '<li>' . $renderer->render(Renderer::compact) . '</li>';
        }
        if ($tracksHtml === '') {
            $tracksHtml = '<li class="empty">Cette playlist est vide. Ajoutez une première piste.</li>';
        }

        $nom = AudioTrackRenderer::esc($this->audioList->nom);
        $nb = (int)$this->audioList->nombre2pistes;
        $duree = AudioTrackRenderer::formatDuree((int)$this->audioList->dureeTotale);
        $pluriel = $nb > 1 ? 'pistes' : 'piste';

        // couleur de la pochette dérivée du nom de la playlist
        $h = crc32((string)$this->audioList->nom) % 360;
        $h2 = ($h + 50) % 360;
        $cover = "background:linear-gradient(135deg,hsl($h,65%,55%),hsl($h2,65%,32%))";

        return <<<HTML
<section class="playlist">
    <div class="pl-head">
        <div class="cover" style="{$cover}">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3z"/></svg>
        </div>
        <div class="meta">
            <div class="kind">Playlist</div>
            <h1>{$nom}</h1>
            <div class="stats">{$nb} {$pluriel}, {$duree}</div>
        </div>
    </div>
    <ul class="tracks">{$tracksHtml}</ul>
</section>
HTML;
    }
}
