<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace PDP\Integration\Model;

class PdpDesignJsonForm{
    private $designId;
    private $sideThumb;
    
    public function getDesignId() {
        return $this->designId;
    }

    public function getSideThumb() {
        return $this->sideThumb;
    }

    public function setDesignId($designId) {
        $this->designId = $designId;
    }

    public function setSideThumb($sideThumb) {
        $this->sideThumb = $sideThumb;
    }

}
