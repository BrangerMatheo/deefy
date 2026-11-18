<?php

namespace iutnc\deefy\audio\lists;

class Album extends AudioList{

    private string $artiste;
    private string $date2sortie;

    public function __construct(string $nom, array $pistes){
        parent::__construct($nom, $pistes);
    }

    public function setArtiste(string $artiste) : void {
        $this->artiste = $artiste;
    }
    public function setDate2sortie(string $date2sortie) : void {
        $this->date2sortie = $date2sortie;
    }
}

?>