<?php

namespace iutnc\deefy\audio\lists;

use Iterator;
use iutnc\deefy\exception\InvalidPropertyNameException;

class AudioList implements Iterator{

    protected ?int $id = null;
    protected string $nom;
    protected int $dureeTotale;
    protected int $nombre2pistes;
    protected array $pistes;
    protected int $indicePosition;

    public function __construct(string $nom, array $pistes = []){
        $this->nom = $nom;
        $this->pistes = $pistes;
        $this->nombre2pistes = count($pistes);

        $this->dureeTotale = 0;
        foreach($pistes as $piste){
            $this->dureeTotale = $this->dureeTotale + $piste->duree;
        }

    }

    public function majDonne() : void {
        $this->nombre2pistes = count($this->pistes);
        $this->dureeTotale = 0;
        foreach($this->pistes as $piste){
            $this->dureeTotale = $this->dureeTotale + $piste->duree;
        }
    }

    public function setId(int $id) : void {
        $this->id = $id;
    }

    public function rewind(): void {
        $this->indicePosition = 0;
    }

    #[\ReturnTypeWillChange]
    public function current(): mixed {
        return $this->pistes[$this->indicePosition];
    }

    #[\ReturnTypeWillChange]
    public function key() : mixed {
        return $this->indicePosition;
    }

    public function next(): void {
        ++$this->indicePosition;
    }

    public function valid(): bool {
        return isset($this->pistes[$this->indicePosition]);
    }

    public function __get(string $name)
    {
        if (!property_exists($this, $name)) {
            throw new InvalidPropertyNameException("invalid property : $name");
        }
        return $this->$name;
    }

}

?>