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
            $tracksHtml = $tracksHtml . '<li>' . $renderer->render(Renderer::compact) . '</li>';
        }

        return "<h1>{$this->audioList->nom}</h1>"
            . "<ul>{$tracksHtml}</ul>"
            . "<div>"
            . "<p>Nombre de pistes : {$this->audioList->nombre2pistes}</p>"
            . "<p>Durée totale : {$this->audioList->dureeTotale} secondes</p>"
            . "</div>";
    }
}