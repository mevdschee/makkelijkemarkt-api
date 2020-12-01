<?php

namespace App\Mapper;

use App\Entity\Koopman;
use App\Entity\Vervanger;
use App\Model\KoopmanModel;
use App\Model\VervangerModel;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class VervangerMapper
{
    /**
     * @var CacheManager
     */
    private $imagineCacheManager;

    /**
     * @param CacheManager $imagineCacheManager
     */
    public function __construct(CacheManager $imagineCacheManager)
    {
        $this->imagineCacheManager = $imagineCacheManager;
    }

    /**
     * @param Vervanger $e
     * @return \App\Model\KoopmanModel
     */
    public function singleEntityToModel(Vervanger $e)
    {
        $object = new VervangerModel();

        $object->pas_uid = $e->getPasUid();
        $object->vervanger_id = $e->getVervanger()->getId();
        $object->achternaam = $e->getVervanger()->getAchternaam();
        $object->email = $e->getVervanger()->getEmail();
        $object->erkenningsnummer = $e->getVervanger()->getErkenningsnummer();
        if ($e->getVervanger()->getFoto() !== '' && $e->getVervanger()->getFoto() !== null) {
            $object->fotoUrl = $this->imagineCacheManager->getBrowserPath('koopman-fotos/' . $e->getVervanger()->getFoto(), 'koopman_rect_small');
        }

        if ($e->getVervanger()->getFoto() !== '' && $e->getVervanger()->getFoto() !== null) {
            $object->fotoMediumUrl = $this->imagineCacheManager->getBrowserPath('koopman-fotos/' . $e->getVervanger()->getFoto(), 'koopman_rect_medium');
        }

        $object->perfectViewNummer = $e->getVervanger()->getPerfectViewNummer();
        $object->status = KoopmanMapper::$statussen[$e->getVervanger()->getStatus()];
        $object->telefoon = $e->getVervanger()->getTelefoon();
        $object->voorletters = $e->getVervanger()->getVoorletters();
        $object->tussenvoegsels = $e->getVervanger()->getTussenvoegsels();

        return $object;
    }

    /**
     * @param \App\Entity\Vervanger $list
     * @return \App\Model\VervangerModel[]
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
