<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

abstract class AudioTrack
{
    public const BLUES = 0;
    public const CLASSIC_ROCK = 1;
    public const COUNTRY = 2;
    public const DANCE = 3;
    public const DISCO = 4;
    public const FUNK = 5;
    public const GRUNGE = 6;
    public const HIP_HOP = 7;
    public const JAZZ = 8;
    public const METAL = 9;
    public const NEW_AGE = 10;
    public const POP = 11;
    public const REGGAE = 12;
    public const ROCK = 13;
    public const TECHNO = 14;
    public const INDUSTRIAL = 15;
    public const ALTERNATIVE = 16;
    public const PSYCHEDELIC = 17;
    public const HEAVY_METAL = 18;
    public const FOLK = 19;
    public const POLKA = 20;
    public const TRAP = 21;

    protected ?int $id = null;
    protected string $titre;
    protected int $genre;
    protected int $duree; // en secondes
    protected string $fichier;

    public function __construct(string $titre, string $fichier)
    {
        $this->titre = $titre;
        $this->fichier = $fichier;
    }

    public static function getGenreAsString(int $genre): string {
        return match($genre){
            self::BLUES => 'Blues',
            self::CLASSIC_ROCK => 'Classic Rock',
            self::COUNTRY => 'Country',
            self::DANCE => 'Dance',
            self::DISCO => 'Disco',
            self::FUNK => 'Funk',
            self::GRUNGE => 'Grunge',
            self::HIP_HOP => 'Hip-Hop',
            self::JAZZ => 'Jazz',
            self::METAL => 'Metal',
            self::NEW_AGE => 'New Age',
            self::POP => 'Pop',
            self::REGGAE => 'Reggae',
            self::ROCK => 'Rock',
            self::TECHNO => 'Techno',
            self::INDUSTRIAL => 'Industrial',
            self::ALTERNATIVE => 'Alternative',
            self::PSYCHEDELIC => 'Psychedelic',
            self::HEAVY_METAL => 'Heavy Metal',
            self::FOLK => 'Folk',
            self::POLKA => 'Polka',
            self::TRAP => 'Trap',
            default => 'Inconnu',
        };
    }

    public function __get(string $name)
    {
        if (!property_exists($this, $name)) {
            throw new InvalidPropertyNameException("invalid property : $name");
        }
        return $this->$name;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setGenre(int $genre): void
    {
        $this->genre = $genre;
    }

    public function setDuree(int $duree): void
    {
        if ($duree < 0) {
            throw new InvalidPropertyValueException("duree must be >= 0, got $duree");
        }
        $this->duree = $duree;
    }
}