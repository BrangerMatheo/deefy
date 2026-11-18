<?php

namespace iutnc\deefy\audio\tracks;

class PodcastTrack extends AudioTrack
{
    protected string $auteur;
    protected string $date;

    public function __construct(string $titre, string $fichier)
    {
        parent::__construct($titre, $fichier);
    }

    public function setAuteur(string $auteur): void
    {
        $this->auteur = $auteur;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function __toString(): string
    {
        return json_encode(get_object_vars($this));
    }
}