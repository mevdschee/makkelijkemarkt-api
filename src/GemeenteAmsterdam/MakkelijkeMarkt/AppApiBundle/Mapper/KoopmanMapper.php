<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\KoopmanModel;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SimpleKoopmanModel;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class KoopmanMapper
{
    /**
     * @var multitype:string
     */
    protected $statussen = [
        Koopman::STATUS_ACTIEF => 'Actief',
        Koopman::STATUS_ONBEKEND => 'Onbekend',
        Koopman::STATUS_VERWIJDERD => 'Verwijderd',
        Koopman::STATUS_WACHTER => 'Wachter',
        Koopman::STATUS_VERVANGER => 'Vervanger'
    ];

    /**
     * @var SollicitatieMapper
     */
    protected $mapperSollicitatie;

    /**
     * @var VervangerMapper
     */
    protected $mapperVervanger;

    /**
     * @var CacheManager
     */
    protected $imagineCacheManager;

    /**
     * @param SollicitatieMapper $mapperSolliciatie
     */
    public function setSollicitatieMapper(SollicitatieMapper $mapperSolliciatie)
    {
        $this->mapperSollicitatie = $mapperSolliciatie;
    }

    /**
     * @param VervangerMapper $mapperSolliciatie
     */
    public function setVervangerMapper(VervangerMapper $mapperVervanger)
    {
        $this->mapperVervanger = $mapperVervanger;
    }

    /**
     * @param CacheManager $imagineCacheManager
     */
    public function setImagineCacheManager(CacheManager $imagineCacheManager)
    {
        $this->imagineCacheManager = $imagineCacheManager;
    }

    /**
     * @param Koopman $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\KoopmanModel
     */
    public function singleEntityToModel(Koopman $e)
    {
        $object = new KoopmanModel();
        $object->voorletters = $e->getVoorletters();
        $object->achternaam = $e->getAchternaam();
        $object->telefoon = $e->getTelefoon();
        $object->email = $e->getEmail();
        $object->id = $e->getId();
        $object->erkenningsnummer = $e->getErkenningsnummer();
        $object->perfectViewNummer = $e->getPerfectViewNummer();
        if ($e->getFoto() !== '' && $e->getFoto() !== null)
            $object->fotoUrl = $this->imagineCacheManager->getBrowserPath('koopman-fotos/' . $e->getFoto(), 'koopman_rect_small');
        if ($e->getFoto() !== '' && $e->getFoto() !== null)
            $object->fotoMediumUrl = $this->imagineCacheManager->getBrowserPath('koopman-fotos/' . $e->getFoto(), 'koopman_rect_medium');
        $object->status = $this->statussen[$e->getStatus()];
        $object->sollicitaties = $this->mapperSollicitatie->multipleEntityToSimpleModel($e->getSollicitaties());
        $object->pasUid = $e->getPasUid();
        $object->status = $this->statussen[$e->getStatus()];
        $object->vervangers = $this->mapperVervanger->multipleEntityToModel($e->getVervangersVan());
        return $object;
    }

    /**
     * @param Koopman $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SimpleKoopmanModel
     */
    public function singleEntityToSimpleModel(Koopman $e)
    {
        $object = new SimpleKoopmanModel();
        $object->voorletters = $e->getVoorletters();
        $object->achternaam = $e->getAchternaam();
        $object->id = $e->getId();
        $object->erkenningsnummer = $e->getErkenningsnummer();
        if ($e->getFoto() !== '' && $e->getFoto() !== null)
            $object->fotoUrl = $this->imagineCacheManager->getBrowserPath('koopman-fotos/' . $e->getFoto(), 'koopman_rect_small');
        if ($e->getFoto() !== '' && $e->getFoto() !== null)
            $object->fotoMediumUrl = $this->imagineCacheManager->getBrowserPath('koopman-fotos/' . $e->getFoto(), 'koopman_rect_medium');
        $object->status = $this->statussen[$e->getStatus()];
        $object->telefoon = $e->getTelefoon();
        $object->email = $e->getEmail();
        $object->pasUid = $e->getPasUid();
        $object->vervangers = $this->mapperVervanger->multipleEntityToModel($e->getVervangersVan());
        return $object;
    }

    /**
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\KoopmanModel
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Koopman */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }

    /**
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman[] $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SimpleKoopmanModel
     */
    public function multipleEntityToSimpleModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Koopman */
            $result[] = $this->singleEntityToSimpleModel($e);
        }
        return $result;
    }

}