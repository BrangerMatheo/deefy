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

    /** Sous-titre affiché sous le titre (artiste, auteur...). Surchargé par les sous-classes. */
    protected function subtitle(): string
    {
        return '';
    }

    public static function esc(string $s): string
    {
        // double_encode = false : les valeurs ont déjà pu être encodées à la saisie
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8', false);
    }

    public static function formatDuree(int $secondes): string
    {
        return intdiv($secondes, 60) . ':' . str_pad((string)($secondes % 60), 2, '0', STR_PAD_LEFT);
    }

    public function render(int $selector): string
    {
        $titre = self::esc($this->audioTrack->titre);
        $src = self::esc('audio/' . $this->audioTrack->fichier);
        $sous = $this->subtitle();
        if ($selector === Renderer::long) {
            $genre = self::esc($this->audioTrack->getGenreAsString($this->audioTrack->genre));
            $sous = trim($sous . ($sous !== '' ? ' - ' : '') . $genre);
        }
        $duree = self::formatDuree((int)$this->audioTrack->duree);
        $sousEsc = self::esc($sous);

        return <<<HTML
<div class="track" data-src="{$src}" data-title="{$titre}" data-artist="{$sousEsc}">
    <button type="button" class="play" aria-label="Lire {$titre}">
        <svg class="ico-play" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
        <svg class="ico-pause" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>
    </button>
    <div class="info"><span class="t">{$titre}</span><span class="a">{$sousEsc}</span></div>
    <span class="d">{$duree}</span>
</div>
HTML;
    }
}
