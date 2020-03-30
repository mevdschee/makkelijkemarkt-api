<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\VergunningControle;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\VergunningControleModel;


class VergunningControleMapper
{
    /**
     * @var KoopmanMapper
     */
    protected $mapperKoopman;

    /**
     * @var SollicitatieMapper
     */
    protected $mapperSollicitatie;

    /**
     * @var AccountMapper
     */
    protected $mapperAccount;


    public function __construct(
        KoopmanMapper $koopmanMapper,
        SollicitatieMapper $sollicitatieMapper,
        AccountMapper $accountMapper
    ) {
        $this->mapperKoopman = $koopmanMapper;
        $this->mapperSollicitatie = $sollicitatieMapper;
        $this->mapperAccount = $accountMapper;
    }

    /**
     * @param VergunningControle $e
     * @return VergunningControleModel
     */
    public function singleEntityToModel(VergunningControle $e)
    {
        $object = new VergunningControleModel();
        $object->id = $e->getId();
        $object->erkenningsnummer = $e->getErkenningsnummerInvoerWaarde();
        $object->erkenningsnummerInvoerMethode = $e->getErkenningsnummerInvoerMethode();
        $object->registratieGeolocatie = $e->getRegistratieGeolocatie();
        if ($e->getVervanger() !== null) {
            $object->vervanger = $this->mapperKoopman->singleEntityToSimpleModel($e->getVervanger());
        }
        $object->aantal3MeterKramen = $e->getAantal3MeterKramen();
        $object->aantal4MeterKramen = $e->getAantal4MeterKramen();
        $object->afvaleiland = $e->getAfvaleiland();
        $object->eenmaligElektra = $e->getEenmaligElektra();
        $object->extraMeters = $e->getExtraMeters();
        $object->aantalElektra = $e->getAantalElektra();
        $object->krachtstroom = boolval($e->getKrachtstroom());
        $object->reiniging = boolval($e->getReiniging());
        $object->aanwezig = $e->getAanwezig();
        $object->registratieDatumtijd = $e->getRegistratieDatumtijd()->format('Y-m-d H:i:s');
        $object->notitie = $e->getNotitie();
        $object->aantal3meterKramenVast = $e->getAantal3meterKramenVast();
        $object->aantal4meterKramenVast = $e->getAantal4meterKramenVast();
        $object->aantalExtraMetersVast = $e->getAantalExtraMetersVast();
        $object->aantalElektraVast = $e->getAantalElektraVast();
        $object->krachtstroomVast = $e->getKrachtstroomVast();
        $object->afvaleilandVast = $e->getAfvaleilandVast();
        $object->status = $e->getStatusSolliciatie();
        $object->ronde = $e->getRonde();
        if ($e->getSollicitatie() !== null) {
            $object->sollicitatie = $this->mapperSollicitatie->singleEntityToSimpleModel($e->getSollicitatie());
        }
        $object->totaleLengte = ($e->getAantal3MeterKramen() * 3) + ($e->getAantal4MeterKramen() * 4) + $e->getExtraMeters();
        $object->totaleLengteVast = ($e->getAantal3meterKramenVast() * 3) + ($e->getAantal4meterKramenVast() * 4) + $e->getExtraMeters();
        if ($e->getRegistratieAccount() !== null) {
            $object->registratieAccount = $this->mapperAccount->singleEntityToModel($e->getRegistratieAccount());
        }

        return $object;
    }

    /**
     * @param VergunningControle[] $list
     * @return VergunningControleModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e VergunningControle */
            $result[] = $this->singleEntityToModel($e);
        }

        return $result;
    }
}