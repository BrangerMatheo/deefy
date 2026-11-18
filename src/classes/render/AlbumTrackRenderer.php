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

    protected function subtitle(): string
    {
        return $this->albumTrack->artiste . ' - ' . $this->albumTrack->album . ' (' . $this->albumTrack->annee . ')';
    }
}
