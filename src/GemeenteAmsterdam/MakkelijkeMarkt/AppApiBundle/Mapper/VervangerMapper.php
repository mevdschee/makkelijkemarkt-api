<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\KoopmanModel;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\VervangerModel;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;


class VervangerMapper
{
    /**
     * @var CacheManager
     */
    protected $imagineCacheManager;

    /**
     * @param Vervanger $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\KoopmanModel
     */
    public function singleEntityToModel(Vervanger $e)
    {
        $object = new VervangerModel();

        $object->pas_uid = $e->getPasUid();
        $object->vervanger_id = $e->getVervanger()->getId();
        $object->achternaam = $e->getVervanger()->getAchternaam();
        $object->email = $e->getVervanger()->getEmail();
        $object->erkenningsnummer = $e->getVervanger()->getErkenningsnummer();
        if ($e->getVervanger()->getFoto() !== '' && $e->getVervanger()->getFoto() !== null)
            $object->fotoUrl = $this->imagineCacheManager->getBrowserPath('koopman-fotos/' . $e->getVervanger()->getFoto(), 'koopman_rect_small');
        if ($e->getVervanger()->getFoto() !== '' && $e->getVervanger()->getFoto() !== null)
            $object->fotoMediumUrl = $this->imagineCacheManager->getBrowserPath('koopman-fotos/' . $e->getVervanger()->getFoto(), 'koopman_rect_medium');
        $object->perfectViewNummer = $e->getVervanger()->getPerfectViewNummer();
        $object->status = KoopmanMapper::$statussen[$e->getVervanger()->getStatus()];
        $object->telefoon = $e->getVervanger()->getTelefoon();
        $object->voorletters = $e->getVervanger()->getVoorletters();
        $object->tussenvoegsels = $e->getVervanger()->getTussenvoegsels();

        return $object;
    }

    /**
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\VervangerModel
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

    /**
     * @param CacheManager $imagineCacheManager
     */
    public function setImagineCacheManager(CacheManager $imagineCacheManager)
    {
        $this->imagineCacheManager = $imagineCacheManager;
    }
}