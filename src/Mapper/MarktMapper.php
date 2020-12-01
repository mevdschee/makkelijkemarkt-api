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

use App\Entity\Markt;
use App\Model\MarktModel;

class MarktMapper
{
    /**
     * @param Markt $e
     * @return \App\Model\MarktModel
     */
    public function singleEntityToModel(Markt $e)
    {
        $object = new MarktModel();
        $object->id = $e->getId();
        $object->naam = $e->getNaam();
        $object->afkorting = $e->getAfkorting();
        $object->soort = $e->getSoort();
        $object->marktDagen = $e->getMarktDagen();
        $object->geoArea = $e->getGeoArea();
        $object->standaardKraamAfmeting = $e->getStandaardKraamAfmeting();
        $object->extraMetersMogelijk = $e->getExtraMetersMogelijk();
        $object->aanwezigeOpties = [];
        foreach ($e->getAanwezigeOpties() as $key) {
            $object->aanwezigeOpties[$key] = true;
        }
        $object->perfectViewNummer = $e->getPerfectViewNummer();
        $object->aantalKramen = $e->getAantalKramen();
        $object->aantalMeter = $e->getAantalMeter();
        $object->auditMax = $e->getAuditMax();
        $object->kiesJeKraamActief = $e->getKiesJeKraamActief();
        $object->kiesJeKraamFase = $e->getKiesJeKraamFase();
        $object->kiesJeKraamMededelingActief = $e->getKiesJeKraamMededelingActief();
        $object->kiesJeKraamMededelingTekst = $e->getKiesJeKraamMededelingTekst();
        $object->kiesJeKraamMededelingTitel = $e->getKiesJeKraamMededelingTitel();
        $object->kiesJeKraamGeblokkeerdePlaatsen = $e->getKiesJeKraamGeblokkeerdePlaatsen();
        $object->kiesJeKraamEmailKramenzetter = $e->getKiesJeKraamEmailKramenzetter();
        $object->kiesJeKraamGeblokkeerdePlaatsen = $e->getKiesJeKraamGeblokkeerdePlaatsen();
        $object->makkelijkeMarktActief = $e->getMakkelijkeMarktActief();
        $object->indelingsTijdstipTekst = $e->getIndelingsTijdstipTekst();
        $object->marktDagenTekst = $e->getMarktDagenTekst();
        $object->telefoonNummerContact = $e->getTelefoonNummerContact();
        $object->kiesJeKraamGeblokkeerdeData = $e->getKiesJeKraamGeblokkeerdeData();
        $object->indelingstype = $e->getIndelingstype();
        $object->isABlijstIndeling = $e->getIndelingstype() === 'A/B-lijst';
        return $object;
    }

    /**
     * @param \App\Entity\Markt $list
     * @return \App\Model\MarktModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e Markt */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }

}
