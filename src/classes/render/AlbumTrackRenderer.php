<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;

class AlbumTrackRenderer extends AudioTrackRenderer
{
    private AlbumTrack $albumTrack;

    public function __construct(AlbumTrack $albumTrack)
    {
        parent::__construct($albumTrack);
        $this->albumTrack = $albumTrack;
    }

    public function render(int $selector): string
    {
        $html = parent::render($selector);
        $html .= "<h2>artiste {$this->albumTrack->artiste}, de l'album {$this->albumTrack->album}
            sorti en {$this->albumTrack->annee}, piste n°{$this->albumTrack->numero}</h2>";
        return $html;
    }
}