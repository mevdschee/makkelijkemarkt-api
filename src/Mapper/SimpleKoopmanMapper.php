<?php
/*
 *  Copyright (c) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace App\Mapper;

use App\Entity\Koopman;
use App\Model\SimpleKoopmanModel;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class SimpleKoopmanMapper
{
    /**
     * @var VervangerMapper
     */
    private $mapperVervanger;

    /**
     * @var CacheManager
     */
    private $imagineCacheManager;

    /**
     * @param VervangerMapper $mapperVervanger
     * @param CacheManager $imagineCacheManager
     */
    public function __construct(VervangerMapper $mapperVervanger, CacheManager $imagineCacheManager)
    {
        $this->mapperVervanger = $mapperVervanger;
        $this->imagineCacheManager = $imagineCacheManager;
    }

    /**
     * @param Koopman $e
     * @return \App\Model\SimpleKoopmanModel
     */
    public function singleEntityToModel(Koopman $e)
    {
        $object = new SimpleKoopmanModel();
        $object->voorletters = $e->getVoorletters();
        $object->tussenvoegsels = $e->getTussenvoegsels();
        $object->achternaam = $e->getAchternaam();
        $object->id = $e->getId();
        $object->erkenningsnummer = $e->getErkenningsnummer();
        if ($e->getFoto() !== '' && $e->getFoto() !== null) {
            $object->fotoUrl = $this->imagineCacheManager->getBrowserPath('koopman-fotos/' . $e->getFoto(), 'koopman_rect_small');
        }

        if ($e->getFoto() !== '' && $e->getFoto() !== null) {
            $object->fotoMediumUrl = $this->imagineCacheManager->getBrowserPath('koopman-fotos/' . $e->getFoto(), 'koopman_rect_medium');
        }

        $object->status = KoopmanMapper::$statussen[$e->getStatus()];
        $object->telefoon = $e->getTelefoon();
        $object->email = $e->getEmail();
        $object->pasUid = $e->getPasUid();
        $object->vervangers = $this->mapperVervanger->multipleEntityToModel($e->getVervangersVan());
        if ($e->getHandhavingsVerzoek() !== null) {
            $object->handhavingsVerzoek = $e->getHandhavingsVerzoek()->format('Y-m-d');
        }
        return $object;
    }

    /**
     * @param \App\Entity\Koopman[] $list
     * @return \App\Model\SimpleKoopmanModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e Koopman */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }

}
