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
use App\Model\KoopmanModel;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class KoopmanMapper
{
    /**
     * @var string[]
     */
    public static $statussen = [
        Koopman::STATUS_ACTIEF => 'Actief',
        Koopman::STATUS_ONBEKEND => 'Onbekend',
        Koopman::STATUS_VERWIJDERD => 'Verwijderd',
        Koopman::STATUS_WACHTER => 'Wachter',
        Koopman::STATUS_VERVANGER => 'Vervanger',
    ];

    /**
     * @var SimpleSollicitatieMapper
     */
    private $mapperSimpleSolliciatie;

    /**
     * @var VervangerMapper
     */
    private $mapperVervanger;

    /**
     * @var CacheManager
     */
    private $imagineCacheManager;

    /**
     * @param SimpleSollicitatieMapper $mapperSimpleSolliciatie
     * @param VervangerMapper $mapperVervanger
     * @param CacheManager $imagineCacheManager
     */
    public function __construct(SimpleSollicitatieMapper $mapperSimpleSolliciatie, VervangerMapper $mapperVervanger, CacheManager $imagineCacheManager)
    {
        $this->mapperSimpleSolliciatie = $mapperSimpleSolliciatie;
        $this->mapperVervanger = $mapperVervanger;
        $this->imagineCacheManager = $imagineCacheManager;
    }

    /**
     * @param Koopman $e
     * @return \App\Model\KoopmanModel
     */
    public function singleEntityToModel(Koopman $e)
    {
        $object = new KoopmanModel();
        $object->voorletters = $e->getVoorletters();
        $object->tussenvoegsels = $e->getTussenvoegsels();
        $object->achternaam = $e->getAchternaam();
        $object->telefoon = $e->getTelefoon();
        $object->email = $e->getEmail();
        $object->id = $e->getId();
        $object->erkenningsnummer = $e->getErkenningsnummer();
        $object->weging = $e->calculateWeging();

        if ($e->getHandhavingsVerzoek() !== null) {
            $object->handhavingsVerzoek = $e->getHandhavingsVerzoek()->format('Y-m-d');
        }

        $object->perfectViewNummer = $e->getPerfectViewNummer();
        if ($e->getFoto() !== '' && $e->getFoto() !== null) {
            $object->fotoUrl = $this->imagineCacheManager->getBrowserPath('koopman-fotos/' . $e->getFoto(), 'koopman_rect_small');
        }

        if ($e->getFoto() !== '' && $e->getFoto() !== null) {
            $object->fotoMediumUrl = $this->imagineCacheManager->getBrowserPath('koopman-fotos/' . $e->getFoto(), 'koopman_rect_medium');
        }

        $object->status = self::$statussen[$e->getStatus()];
        $object->sollicitaties = $this->mapperSimpleSolliciatie->multipleEntityToModel($e->getSollicitaties());
        $object->pasUid = $e->getPasUid();
        $object->vervangers = $this->mapperVervanger->multipleEntityToModel($e->getVervangersVan());
        return $object;
    }

    /**
     * @param \App\Entity\Koopman $list
     * @return \App\Model\KoopmanModel[]
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
