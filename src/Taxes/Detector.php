<?php

namespace App\Taxes;

class Detector 
{
    protected $seuil;

    public function __construct(float $seuil) {
        $this->seuil = $seuil;
    }

    public function detect(float $nombre) :bool {
       if($nombre > $this->seuil) {
        return true;
       }else {
        return false;
       }
    }
}