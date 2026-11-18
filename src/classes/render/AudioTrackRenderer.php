<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AudioTrack;


class AudioTrackRenderer implements Renderer
{
    protected AudioTrack $audioTrack;

    public function __construct(AudioTrack $audioTrack)
    {
        $this->audioTrack = $audioTrack;
    }

    public function render(int $selector): string
    {
        $html = "ERR";
        switch ($selector) {
            case Renderer::compact:
                $html = "<h1>{$this->audioTrack->titre}
                    <audio controls src='audio/{$this->audioTrack->fichier}'></audio></h1>";
                break;
            case Renderer::long:
                $html = "<h1>{$this->audioTrack->titre} genre {$this->audioTrack->getGenreAsString($this->audioTrack->genre)}
                    de durée {$this->audioTrack->duree} secondes
                    <audio controls src='audio/{$this->audioTrack->fichier}'></audio></h1>";
                break;
        }
        return $html;
    }
}