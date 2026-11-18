<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\tracks\AudioTrack;


class RendererFactory {

    public static function getRenderer(AudioTrack $audioTrack){
        $type = get_class($audioTrack);

        return match (true) {
            $audioTrack instanceof PodcastTrack => new PodcastTrackRenderer($audioTrack),
            $audioTrack instanceof AlbumTrack   => new AlbumTrackRenderer($audioTrack),
            default => new AudioTrackRenderer($audioTrack),
            };
    }

}

?>