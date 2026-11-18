<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\exception\InvalidPropertyValueException;

class Playlist extends AudioList {

    public function __construct(string $nom, array $pistes = []) {
        parent::__construct($nom, $pistes);
    }

    public function ajouterPiste(AudioTrack $pisteAudio) : void {
        $this->pistes[] = $pisteAudio;
        $this->majDonne();
    }

    public function supprimerPiste(int $indicePiste) : void {
        if (array_key_exists($indicePiste, $this->pistes)) {
            unset($this->pistes[$indicePiste]);
            $this->pistes = array_values($this->pistes);
            $this->majDonne();
        } else {
            throw new InvalidPropertyValueException("L'indice de la piste n'existe pas : " . $indicePiste);
        }
    }

    public function ajouterListePistes(array $pistes) : void {
        foreach ($pistes as $piste) {
            $doublon = false;
                        foreach ($this->pistes as $existante) {
                if ($this->estPareille($existante, $piste)) {
                    $doublon = true;
                    break;
                }
            }

            if (!$doublon) {
                $this->ajouterPiste($piste);
            }
        }
    }

    public function estPareille(AudioTrack $audioTrack1, AudioTrack $audioTrack2) : bool {
        return $audioTrack1->fichier === $audioTrack2->fichier;
    }

}
?>