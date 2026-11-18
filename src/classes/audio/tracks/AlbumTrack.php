<?php

namespace iutnc\deefy\audio\tracks;

class AlbumTrack extends AudioTrack
{
    protected string $artiste;
    protected string $album;
    protected int $annee;
    protected int $numero;

    public function __construct(string $titre, string $fichier, string $album, int $numero)
    {
        parent::__construct($titre, $fichier);
        $this->album = $album;
        $this->numero = $numero;
    }

    public function setArtiste(string $artiste): void
    {
        $this->artiste = $artiste;
    }

    public function setAnnee(int $annee): void
    {
        $this->annee = $annee;
    }

    public function __toString(): string
    {
        return json_encode(get_object_vars($this));
    }
}