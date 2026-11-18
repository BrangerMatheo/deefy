<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\PodcastTrack;

class PodcastTrackRenderer extends AudioTrackRenderer
{
    private PodcastTrack $podcastTrack;

    public function __construct(PodcastTrack $podcastTrack)
    {
        parent::__construct($podcastTrack);
        $this->podcastTrack = $podcastTrack;
    }

    protected function subtitle(): string
    {
        return $this->podcastTrack->auteur . ' - ' . $this->podcastTrack->date;
    }
}
