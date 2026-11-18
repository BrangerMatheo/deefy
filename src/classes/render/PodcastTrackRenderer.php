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

    public function render(int $selector): string
    {
        $html = parent::render($selector);
        $html = $html . "<h2>auteur {$this->podcastTrack->auteur}, sorti le {$this->podcastTrack->date}</h2>";
        return $html;
    }
}