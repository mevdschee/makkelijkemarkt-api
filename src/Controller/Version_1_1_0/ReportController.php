<?php
/*
 *  Copyright (C) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace App\Controller\Version_1_1_0;

use App\Entity\Koopman;
use App\Mapper\DagvergunningMapper;
use App\Mapper\KoopmanMapper;
use App\Mapper\SollicitatieMapper;
use App\Model\AbstractRapportModel;
use App\Repository\DagvergunningRepository;
use App\Repository\KoopmanRepository;
use App\Repository\MarktRepository;
use App\Repository\SollicitatieRepository;
use App\Repository\VergunningControleRepository;
use Doctrine\ORM\Query;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("api/1.1.0")
 * @OA\Tag(name="Rapport")
 */
class ReportController extends AbstractController
{
    /**
     * @Route("/rapport/dubbelstaan/{dag}", methods={"GET"})
     * @OA\Parameter(name="dag", in="path", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @IsGranted("ROLE_SENIOR")
     */
    public function dubbelstaanRaportAction(
        DagvergunningMapper $dagvergunningMapper,
        KoopmanMapper $koopmanMapper,
        DagvergunningRepository $dagvergunningRepository,
        KoopmanRepository $koopmanRepository,
        $dag
    ) {

        // eerst alle erkenningsnummers selecteren die meerdere ACTIEVE vergunningen hebben voor een bepaalde dag
        $qb = $dagvergunningRepository->createQueryBuilder('dagvergunning');
        $qb->select('dagvergunning.erkenningsnummerInvoerWaarde AS erkenningsnummer')
            ->addSelect('COUNT(dagvergunning.erkenningsnummerInvoerWaarde) AS aantal')
            ->andWhere('dagvergunning.doorgehaald = :doorgehaald')
            ->setParameter('doorgehaald', false)
            ->andWhere('dagvergunning.dag = :dag')
            ->setParameter('dag', $dag)
            ->addGroupBy('dagvergunning.erkenningsnummerInvoerWaarde')
            ->andHaving('COUNT(dagvergunning.erkenningsnummerInvoerWaarde) > 1')
        ;
        $selector = $qb->getQuery()->execute([], Query::HYDRATE_ARRAY);

        // vervolgens de achterliggende vergunningen selecteren per erkenningsnummer
        $detailData = [];
        foreach ($selector as $record) {
            $qb = $dagvergunningRepository->createQueryBuilder('dagvergunning');
            $qb->select('dagvergunning')
                ->addSelect('markt')
                ->addSelect('koopman')
                ->join('dagvergunning.markt', 'markt')
                ->leftJoin('dagvergunning.koopman', 'koopman')
                ->andWhere('dagvergunning.doorgehaald = :doorgehaald')
                ->setParameter('doorgehaald', false)
                ->andWhere('dagvergunning.erkenningsnummerInvoerWaarde = :erkenningsnummer')
                ->setParameter('erkenningsnummer', $record['erkenningsnummer'])
                ->andWhere('dagvergunning.dag = :dag')
                ->setParameter('dag', $dag)
                ->addOrderBy('dagvergunning.registratieDatumtijd');
            $detailData[$record['erkenningsnummer']] = $qb->getQuery()->execute();
        }

        // vervolgens de bijbehorende koopman selecteren
        $koopmanData = [];
        foreach ($selector as $record) {
            $qb = $koopmanRepository->createQueryBuilder('koopman');
            $qb->select('koopman')
                ->andWhere('koopman.erkenningsnummer = :erkenningsnummer')
                ->setParameter('erkenningsnummer', $record['erkenningsnummer']);
            $koopmanData[$record['erkenningsnummer']] = $qb->getQuery()->getOneOrNullResult();
        }

        // bouw abstract rapport model
        $model = new AbstractRapportModel();
        $model->type = 'dubbelstaan';
        $model->generationDate = date('Y-m-d H:i:s');
        $model->input = ['dag' => $dag];
        $model->output = [];

        // add data
        foreach ($selector as $record) {
            $koopman = null;
            if (isset($koopmanData[$record['erkenningsnummer']])) {
                $koopman = $koopmanMapper->singleEntityToSimpleModel($koopmanData[$record['erkenningsnummer']]);
            }

            $model->output[] = [
                'erkenningsnummer' => $record['erkenningsnummer'],
                'aantalDagvergunningenUitgegeven' => $record['aantal'],
                'koopman' => $koopman,
                'dagvergunningen' => $dagvergunningMapper->multipleEntityToModel($detailData[$record['erkenningsnummer']]),
            ];
        }

        return new JsonResponse($model, Response::HTTP_OK, ['X-Api-ListSize' => count($selector)]);
    }

    /**
     * @Route("/rapport/staanverplichting/{dagStart}/{dagEind}/{vergunningType}", methods={"GET"})
     * @OA\Parameter(name="dagStart", in="path", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @OA\Parameter(name="dagEind", in="path", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @OA\Parameter(name="vergunningType", in="path", required="true", @OA\Schema(type="string"), description="alle|soll|vkk|vpl|lot")
     * @OA\Parameter(name="marktId[]", in="query", required="true", @OA\Schema(type="integer"), description="ID van markt")
     * @IsGranted("ROLE_SENIOR")
     */
    public function staanverplichtingRapportAction(
        KoopmanMapper $koopmanMapper,
        SollicitatieMapper $sollicitatieMapper,
        SollicitatieRepository $sollicitatieRepository,
        DagvergunningRepository $dagvergunningRepository,
        VergunningControleRepository $vergunningControleRepository,
        Request $request,
        $dagStart,
        $dagEind,
        $vergunningType
    ) {
        // get the right markt
        $marktIds = $request->query->get('marktId', []);
        if (is_array($marktIds) === false) {
            $marktIds = explode(',', $marktIds);
        }
        $marktIds = array_values($marktIds);

        $qb = $sollicitatieRepository->createQueryBuilder('s');
        $qb->select('s.id AS sollicitatie_id');
        $qb->innerJoin('s.koopman', 'k');
        $qb->innerJoin('s.markt', 'markt');
        if (count($marktIds) > 0) {
            $qb->andWhere($qb->expr()->in('markt.id', ':marktIds'));
            $qb->setParameter('marktIds', $marktIds);
        }
        $qb->andWhere('s.doorgehaald = :sdoorgehaald');
        $qb->setParameter('sdoorgehaald', false);
        $qb->andWhere('k.status <> :kstatus');
        $qb->setParameter('kstatus', Koopman::STATUS_VERWIJDERD);
        if ('alle' !== $vergunningType) {
            $qb->andWhere('s.status = :status');
            $qb->setParameter('status', $vergunningType);
        }
        $qb->addSelect('(SELECT COUNT(d1.id) FROM AppApiBundle:Dagvergunning AS d1 WHERE d1.sollicitatie = s AND d1.dag BETWEEN :dagStart1 AND :dagEind1 AND d1.doorgehaald = false) AS aantalActieveDagvergunningen');
        $qb->setParameter('dagStart1', new \DateTime($dagStart));
        $qb->setParameter('dagEind1', new \DateTime($dagEind));
        $qb->addSelect('(SELECT COUNT(d2.id) FROM AppApiBundle:Dagvergunning AS d2 WHERE d2.sollicitatie = s AND d2.dag BETWEEN :dagStart2 AND :dagEind2 AND d2.doorgehaald = false AND LOWER(d2.aanwezig) = \'zelf\') AS aantalActieveDagvergunningenZelfAanwezig');
        $qb->setParameter('dagStart2', new \DateTime($dagStart));
        $qb->setParameter('dagEind2', new \DateTime($dagEind));
        $qb->addOrderBy('k.erkenningsnummer');
        $qb->addGroupBy('s.id');
        $qb->addGroupBy('k.erkenningsnummer');

        $selector = $qb->getQuery()->execute([], Query::HYDRATE_ARRAY);

        // bouw abstract rapport model
        $model = new AbstractRapportModel();
        $model->type = 'staanverplichting';
        $model->generationDate = date('Y-m-d H:i:s');
        $model->input = ['marktId' => $marktIds, 'dagStart' => $dagStart, 'dagEind' => $dagEind];
        $model->output = [];

        // make a indexed quick lookup array of koopmannen
        $sollicitaties = [];
        $qb = $sollicitatieRepository->createQueryBuilder('s');
        $qb->select('s');
        $qb->innerJoin('s.markt', 'markt');
        $qb->join('s.koopman', 'k');
        $qb->leftJoin('k.vervangersVan', 'vervanger');
        $qb->addSelect('vervanger');
        $qb->leftJoin('vervanger.vervanger', 'vervangerKoopman');
        $qb->addSelect('vervangerKoopman');
        $qb->addSelect('k');
        if (count($marktIds) > 0) {
            $qb->andWhere($qb->expr()->in('markt.id', ':marktIds'));
            $qb->setParameter('marktIds', $marktIds);
        }
        $unindexedSollicitaties = $qb->getQuery()->execute();
        foreach ($unindexedSollicitaties as $sollicitatie) {
            $sollicitaties[$sollicitatie->getId()] = $sollicitatie;
        }

        // create output
        foreach ($selector as $record) {
            $formattedRecord = $record;
            $formattedRecord['aantalActieveDagvergunningenNietZelfAanwezig'] = $record['aantalActieveDagvergunningen'] - $record['aantalActieveDagvergunningenZelfAanwezig'];
            $formattedRecord['percentageAanwezig'] = $record['aantalActieveDagvergunningen'] > 0 ? (round($record['aantalActieveDagvergunningenZelfAanwezig'] / $record['aantalActieveDagvergunningen'], 2)) : 0;
            $formattedRecord['koopman'] = $koopmanMapper->singleEntityToSimpleModel($sollicitaties[$record['sollicitatie_id']]->getKoopman());
            $formattedRecord['sollicitatie'] = $sollicitatieMapper->singleEntityToSimpleModel($sollicitaties[$record['sollicitatie_id']]);

            $controle_rondes = [];

            // per sollicitatie
            $qb2 = $dagvergunningRepository->createQueryBuilder('d');
            $qb2->select('d.dag');
            $qb2->addSelect('d.aanwezig');
            $qb2->join('d.sollicitatie', 's');
            $qb2->andWhere('d.sollicitatie = :sollicitatie');
            $qb2->setParameter('sollicitatie', $sollicitaties[$record['sollicitatie_id']]);
            $qb2->andWhere('d.dag BETWEEN :dagStart3 AND :dagEind3');
            $qb2->setParameter('dagStart3', new \DateTime($dagStart));
            $qb2->setParameter('dagEind3', new \DateTime($dagEind));
            $qb2->andWhere('s.doorgehaald = :sdoorgehaald');
            $qb2->setParameter('sdoorgehaald', false);
            $qb2->andWhere('d.doorgehaald = :ddoorgehaald');
            $qb2->setParameter('ddoorgehaald', false);
            $dagvergunning_records = $qb2->getQuery()->execute();
            foreach ($dagvergunning_records as $row) {
                $row['dag'] = $row['dag']->format('Y-m-d');
                if (isset($controle_rondes[$row['dag']]) === false) {
                    $controle_rondes[$row['dag']] = ['zelf' => 0, 'andere' => 0];
                }
                if ($row['aanwezig'] === 'zelf') {
                    $controle_rondes[$row['dag']]['zelf']++;
                } else {
                    $controle_rondes[$row['dag']]['andere']++;
                }
            }

            $qb3 = $vergunningControleRepository->createQueryBuilder('vc');
            $qb3->select('d.dag');
            $qb3->addSelect('vc.aanwezig');
            $qb3->join('vc.dagvergunning', 'd');
            $qb3->join('d.sollicitatie', 's');
            $qb3->andWhere('d.sollicitatie = :sollicitatie');
            $qb3->andWhere('d.dag BETWEEN :dagStart3 AND :dagEind3');
            $qb3->setParameter('sollicitatie', $sollicitaties[$record['sollicitatie_id']]);
            $qb3->setParameter('dagStart3', new \DateTime($dagStart));
            $qb3->setParameter('dagEind3', new \DateTime($dagEind));
            $qb3->andWhere('s.doorgehaald = :sdoorgehaald');
            $qb3->setParameter('sdoorgehaald', false);
            $qb3->andWhere('d.doorgehaald = :ddoorgehaald');
            $qb3->setParameter('ddoorgehaald', false);
            $controle_rondes_temp = $qb3->getQuery()->execute();

            foreach ($controle_rondes_temp as $row) {
                $row['dag'] = $row['dag']->format('Y-m-d');
                if (isset($controle_rondes[$row['dag']]) === false) {
                    $controle_rondes[$row['dag']] = ['zelf' => 0, 'andere' => 0];
                }
                if ($row['aanwezig'] === 'zelf') {
                    $controle_rondes[$row['dag']]['zelf']++;
                } else {
                    $controle_rondes[$row['dag']]['andere']++;
                }
            }

            $formattedRecord['aantalActieveDagvergunningenNietZelfAanwezigNaControle'] = 0;
            $formattedRecord['aantalActieveDagvergunningenZelfAanwezigNaControle'] = 0;
            foreach ($controle_rondes as $dag => $stats) {
                if ($stats['zelf'] >= $stats['andere']) {
                    $formattedRecord['aantalActieveDagvergunningenZelfAanwezigNaControle']++;
                } else {
                    $formattedRecord['aantalActieveDagvergunningenNietZelfAanwezigNaControle']++;
                }
            }

            $formattedRecord['percentageAanwezigNaControle'] = $record['aantalActieveDagvergunningen'] > 0 ? (round($formattedRecord['aantalActieveDagvergunningenZelfAanwezigNaControle'] / $record['aantalActieveDagvergunningen'], 2)) : 0;

            $model->output[] = $formattedRecord;
        }

        return new JsonResponse($model, Response::HTTP_OK, ['X-Api-ListSize' => count($selector)]);
    }

    /**
     * @Route("/rapport/frequentie/{marktId}/{type}/{dagStart}/{dagEind}", methods={"GET"})
     * @OA\Parameter(name="marktId", in="path", required="true", @OA\Schema(type="integer"), description="ID van markt")
     * @OA\Parameter(name="type", in="path", required="true", @OA\Schema(type="string"), description="dag|week|soll")
     * @OA\Parameter(name="dagStart", in="path", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @OA\Parameter(name="dagEind", in="path", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @IsGranted("ROLE_SENIOR")
     */
    public function frequentieRapportAction(
        DagvergunningRepository $repo,
        $marktId,
        $type,
        $dagStart,
        $dagEind
    ) {
        $response = array();
        if (in_array($type, array('dag', 'week'))) {
            $response = $repo->getMarktFrequentieDag($marktId, $dagStart, $dagEind);
        } elseif ('soll' === $type) {
            $response = $repo->getMarktFrequentieSollicitanten($marktId, $dagStart, $dagEind);
        }

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($response)]);
    }

    /**
     * @Route("/rapport/aanwezigheid/{marktId}/{dagStart}/{dagEind}", methods={"GET"})
     * @OA\Parameter(name="marktId", in="path", required="true", @OA\Schema(type="integer"), description="ID van markt")
     * @OA\Parameter(name="dagStart", in="path", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @OA\Parameter(name="dagEind", in="path", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @IsGranted("ROLE_SENIOR")
     */
    public function persoonlijkeAanwezigheidRapportAction(
        DagvergunningRepository $repo,
        $marktId,
        $dagStart,
        $dagEind
    ) {
        $response = $repo->getMarktPersoonlijkeAanwezigheid($marktId, $dagStart, $dagEind);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($response)]);
    }

    /**
     * @Route("/rapport/invoer/{marktId}/{dagStart}/{dagEind}", methods={"GET"})
     * @OA\Parameter(name="marktId", in="path", required="true", @OA\Schema(type="integer"), description="ID van markt")
     * @OA\Parameter(name="dagStart", in="path", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @OA\Parameter(name="dagEind", in="path", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @IsGranted("ROLE_SENIOR")
     */
    public function invoerRapportAction(
        DagvergunningRepository $repo,
        $marktId,
        $dagStart,
        $dagEind
    ) {
        $response = $repo->getInvoer($marktId, $dagStart, $dagEind);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($response)]);
    }

    /**
     * @Route("/rapport/detailfactuur", methods={"GET"})
     * @OA\Parameter(name="marktId", in="query", required="true", @OA\Schema(type="integer"), description="ID van markt")
     * @OA\Parameter(name="dagStart", in="query", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @OA\Parameter(name="dagEind", in="query", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @IsGranted("ROLE_SENIOR")
     */
    public function detailFactuurRapportAction(Request $request)
    {
        $marktIds = $request->query->get('marktIds', '');
        if (is_array($marktIds) === false) {
            $marktIds = explode(',', $marktIds);
        }
        $dateStart = $request->query->get('dagStart');
        $dateEnd = $request->query->get('dagEind');

        /* @var $stmt \Doctrine\DBAL\Driver\Statement */
        $sql = '
            SELECT
                COUNT(p.id) AS voorkomens,
                p.naam AS product_naam, p.bedrag,
                p.aantal,
                (p.bedrag * p.aantal) AS som,
                ((p.bedrag * p.aantal) * count(p.id)) AS totaal,
                m.naam AS markt_naam,
                d.dag
            FROM product p
            JOIN factuur f ON p.factuur_id = f.id
            JOIN dagvergunning d ON f.dagvergunning_id = d.id
            JOIN markt m ON d.markt_id = m.id
            WHERE p.bedrag > 0
            AND d.doorgehaald = false
            AND d.dag >= :dateStart
            AND d.dag <= :dateEnd
            AND m.id IN (:marktIds)
            GROUP BY
                p.naam,
                p.bedrag,
                p.aantal,
                m.naam,
                d.dag
            ORDER BY
                m.naam ASC,
                d.dag ASC,
                totaal DESC
        ;';
        $stmt = $this->getDoctrine()->getConnection()->executeQuery(
            $sql,
            ['dateStart' => $dateStart, 'dateEnd' => $dateEnd, 'marktIds' => $marktIds],
            ['dateStart' => \PDO::PARAM_STR, 'dateEnd' => \PDO::PARAM_STR, 'marktIds' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
        );

        // bouw abstract rapport model
        $model = new AbstractRapportModel();
        $model->type = 'factuurdetail';
        $model->generationDate = date('Y-m-d H:i:s');
        $model->input = ['marktIds' => $marktIds, 'dagStart' => $dateStart, 'dagEind' => $dateEnd];
        $model->output = $stmt->fetchAll();

        return new JsonResponse($model, Response::HTTP_OK, ['X-Api-ListSize' => count($model->output)]);
    }

    /**
     * @Route("/rapport/marktcapaciteit", methods={"GET"})
     * @OA\Parameter(name="marktId", in="query", required="true", @OA\Schema(type="integer"), description="ID van markt")
     * @OA\Parameter(name="dagStart", in="query", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @OA\Parameter(name="dagEind", in="query", required="true", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @IsGranted("ROLE_SENIOR")
     */
    public function marktCapaciteitRapportAction(MarktRepository $repo, Request $request)
    {
        $marktIds = $request->query->get('marktId', '');
        if (is_array($marktIds) === false) {
            $marktIds = explode(',', $marktIds);
        }
        $dateStart = $request->query->get('dagStart');
        $dateEnd = $request->query->get('dagEind');

        /* @var $stmt \Doctrine\DBAL\Driver\Statement */
        $sql = '
            SELECT
                to_char(d.dag, \'dy\') AS dag,
                to_char(d.dag, \'IW\') AS week,
                to_char(d.dag, \'mon\') AS maand,
                to_char(d.dag, \'YYYY\') AS jaar,
            	d.dag AS datum,
            	d.markt_id,
            	d.status_solliciatie,
            	COUNT(d.id) AS aantal_dagvergunningen,
            	SUM(d.aantal3meter_kramen) AS aantal_3_meter_kramen,
            	SUM(d.aantal4meter_kramen) AS aantal_4_meter_kramen,
            	SUM(d.extra_meters) AS aantal_extra_meters,
            	((SUM(d.aantal3meter_kramen) * 3) + (SUM(d.aantal4meter_kramen) * 4) + SUM(d.extra_meters)) AS totaal_aantal_meters
            FROM dagvergunning AS d
            WHERE d.doorgehaald = false
            AND d.dag BETWEEN :dateStart AND :dateEnd
            AND d.markt_id IN (:marktIds)
            GROUP BY
                d.dag,
                d.markt_id,
                d.status_solliciatie
            ORDER BY
            	d.dag DESC,
            	d.markt_id ASC,
                d.status_solliciatie ASC
        ;';

        $stmt = $this->getDoctrine()->getConnection()->executeQuery(
            $sql,
            ['dateStart' => $dateStart, 'dateEnd' => $dateEnd, 'marktIds' => $marktIds],
            ['dateStart' => \PDO::PARAM_STR, 'dateEnd' => \PDO::PARAM_STR, 'marktIds' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
        );

        $results = $repo->findAll();
        $markten = [];
        foreach ($results as $markt) {
            $markten[$markt->getId()] = $markt;
        }

        $rapport = [];
        foreach ($stmt->fetchAll() as $row) {
            $key = $row['datum'] . '_' . $row['markt_id'];
            if (isset($rapport[$key]) === false) {
                $rapport[$key] = [
                    'marktId' => $row['markt_id'],
                    'marktNaam' => $markten[$row['markt_id']]->getNaam(),
                    'datum' => $row['datum'],
                    'week' => $row['week'],
                    'maand' => $row['maand'],
                    'jaar' => $row['jaar'],
                    'dag' => $row['dag'],
                    'capaciteitKramen' => $markten[$row['markt_id']]->getAantalKramen(),
                    'capaciteitMeter' => $markten[$row['markt_id']]->getAantalMeter(),
                    'aantalDagvergunningen' => 0,
                    'totaalAantalKramen' => 0,
                    'totaalAantalKramen%' => 0.0,
                    'totaalAantalMeter' => 0,
                    'totaalAantalMeter%' => 0.0,

                    'vplAantalDagvergunningen' => 0,
                    'vplAantalDagvergunningen%' => 0.0,
                    'vplAantalKramen' => 0,
                    'vplAantalKramen%' => 0.0,
                    'vplAantalMeter' => 0,
                    'vplAantalMeter%' => 0.0,

                    'vkkAantalDagvergunningen' => 0,
                    'vkkAantalDagvergunningen%' => 0.0,
                    'vkkAantalKramen' => 0,
                    'vkkAantalKramen%' => 0.0,
                    'vkkAantalMeter' => 0,
                    'vkkAantalMeter%' => 0.0,

                    'tvplAantalDagvergunningen' => 0,
                    'tvplAantalDagvergunningen%' => 0.0,
                    'tvplAantalKramen' => 0,
                    'tvplAantalKramen%' => 0.0,
                    'tvplAantalMeter' => 0,
                    'tvplAantalMeter%' => 0.0,

                    'tvplzAantalDagvergunningen' => 0,
                    'tvplzAantalDagvergunningen%' => 0.0,
                    'tvplzAantalKramen' => 0,
                    'tvplzAantalKramen%' => 0.0,
                    'tvplzAantalMeter' => 0,
                    'tvplzAantalMeter%' => 0.0,

                    'expAantalDagvergunningen' => 0,
                    'expAantalDagvergunningen%' => 0.0,
                    'expAantalKramen' => 0,
                    'expAantalKramen%' => 0.0,
                    'expAantalMeter' => 0,
                    'expAantalMeter%' => 0.0,

                    'expfAantalDagvergunningen' => 0,
                    'expfAantalDagvergunningen%' => 0.0,
                    'expfAantalKramen' => 0,
                    'expfAantalKramen%' => 0.0,
                    'expfAantalMeter' => 0,
                    'expfAantalMeter%' => 0.0,

                    'sollAantalDagvergunningen' => 0,
                    'sollAantalDagvergunningen%' => 0.0,
                    'sollAantalKramen' => 0,
                    'sollAantalKramen%' => 0.0,
                    'sollAantalMeter' => 0,
                    'sollAantalMeter%' => 0.0,

                    'lotAantalDagvergunningen' => 0,
                    'lotAantalDagvergunningen%' => 0.0,
                    'lotAantalKramen' => 0,
                    'lotAantalKramen%' => 0.0,
                    'lotAantalMeter' => 0,
                    'lotAantalMeter%' => 0.0,
                ];
            }

            $rapport[$key]['aantalDagvergunningen'] = $rapport[$key]['aantalDagvergunningen'] + $row['aantal_dagvergunningen'];
            $rapport[$key][$row['status_solliciatie'] . 'AantalDagvergunningen'] = $row['aantal_dagvergunningen'];
            $rapport[$key][$row['status_solliciatie'] . 'AantalKramen'] = $row['aantal_3_meter_kramen'] + $row['aantal_4_meter_kramen'];
            $rapport[$key][$row['status_solliciatie'] . 'AantalMeter'] = $row['totaal_aantal_meters'];
        }

        foreach ($rapport as $key => $row) {
            $rapport[$key]['vplAantalDagvergunningen%'] = $rapport[$key]['aantalDagvergunningen'] > 0 ? $rapport[$key]['vplAantalDagvergunningen'] / $rapport[$key]['aantalDagvergunningen'] : 0;
            $rapport[$key]['vkkAantalDagvergunningen%'] = $rapport[$key]['aantalDagvergunningen'] > 0 ? $rapport[$key]['vkkAantalDagvergunningen'] / $rapport[$key]['aantalDagvergunningen'] : 0;
            $rapport[$key]['tvplAantalDagvergunningen%'] = $rapport[$key]['aantalDagvergunningen'] > 0 ? $rapport[$key]['tvplAantalDagvergunningen'] / $rapport[$key]['aantalDagvergunningen'] : 0;
            $rapport[$key]['tvplzAantalDagvergunningen%'] = $rapport[$key]['aantalDagvergunningen'] > 0 ? $rapport[$key]['tvplzAantalDagvergunningen'] / $rapport[$key]['aantalDagvergunningen'] : 0;
            $rapport[$key]['expAantalDagvergunningen%'] = $rapport[$key]['aantalDagvergunningen'] > 0 ? $rapport[$key]['expAantalDagvergunningen'] / $rapport[$key]['aantalDagvergunningen'] : 0;
            $rapport[$key]['expfAantalDagvergunningen%'] = $rapport[$key]['aantalDagvergunningen'] > 0 ? $rapport[$key]['expfAantalDagvergunningen'] / $rapport[$key]['aantalDagvergunningen'] : 0;
            $rapport[$key]['sollAantalDagvergunningen%'] = $rapport[$key]['aantalDagvergunningen'] > 0 ? $rapport[$key]['sollAantalDagvergunningen'] / $rapport[$key]['aantalDagvergunningen'] : 0;
            $rapport[$key]['lotAantalDagvergunningen%'] = $rapport[$key]['aantalDagvergunningen'] > 0 ? $rapport[$key]['lotAantalDagvergunningen'] / $rapport[$key]['aantalDagvergunningen'] : 0;

            $rapport[$key]['vplAantalKramen%'] = $rapport[$key]['capaciteitKramen'] > 0 ? (($rapport[$key]['vplAantalKramen'] / $rapport[$key]['capaciteitKramen'])) : 0;
            $rapport[$key]['vkkAantalKramen%'] = $rapport[$key]['capaciteitKramen'] > 0 ? (($rapport[$key]['vkkAantalKramen'] / $rapport[$key]['capaciteitKramen'])) : 0;
            $rapport[$key]['tvplAantalKramen%'] = $rapport[$key]['capaciteitKramen'] > 0 ? (($rapport[$key]['tvplAantalKramen'] / $rapport[$key]['capaciteitKramen'])) : 0;
            $rapport[$key]['tvplzAantalKramen%'] = $rapport[$key]['capaciteitKramen'] > 0 ? (($rapport[$key]['tvplzAantalKramen'] / $rapport[$key]['capaciteitKramen'])) : 0;
            $rapport[$key]['expAantalKramen%'] = $rapport[$key]['capaciteitKramen'] > 0 ? (($rapport[$key]['expAantalKramen'] / $rapport[$key]['capaciteitKramen'])) : 0;
            $rapport[$key]['expfAantalKramen%'] = $rapport[$key]['capaciteitKramen'] > 0 ? (($rapport[$key]['expfAantalKramen'] / $rapport[$key]['capaciteitKramen'])) : 0;
            $rapport[$key]['sollAantalKramen%'] = $rapport[$key]['capaciteitKramen'] > 0 ? (($rapport[$key]['sollAantalKramen'] / $rapport[$key]['capaciteitKramen'])) : 0;
            $rapport[$key]['lotAantalKramen%'] = $rapport[$key]['capaciteitKramen'] > 0 ? (($rapport[$key]['lotAantalKramen'] / $rapport[$key]['capaciteitKramen'])) : 0;

            $rapport[$key]['vplAantalMeter%'] = $rapport[$key]['capaciteitMeter'] > 0 ? (($rapport[$key]['vplAantalMeter'] / $rapport[$key]['capaciteitMeter'])) : 0;
            $rapport[$key]['vkkAantalMeter%'] = $rapport[$key]['capaciteitMeter'] > 0 ? (($rapport[$key]['vkkAantalMeter'] / $rapport[$key]['capaciteitMeter'])) : 0;
            $rapport[$key]['tvplAantalMeter%'] = $rapport[$key]['capaciteitMeter'] > 0 ? (($rapport[$key]['tvplAantalMeter'] / $rapport[$key]['capaciteitMeter'])) : 0;
            $rapport[$key]['tvplzAantalMeter%'] = $rapport[$key]['capaciteitMeter'] > 0 ? (($rapport[$key]['tvplzAantalMeter'] / $rapport[$key]['capaciteitMeter'])) : 0;
            $rapport[$key]['expAantalMeter%'] = $rapport[$key]['capaciteitMeter'] > 0 ? (($rapport[$key]['expAantalMeter'] / $rapport[$key]['capaciteitMeter'])) : 0;
            $rapport[$key]['expfAantalMeter%'] = $rapport[$key]['capaciteitMeter'] > 0 ? (($rapport[$key]['expfAantalMeter'] / $rapport[$key]['capaciteitMeter'])) : 0;
            $rapport[$key]['sollAantalMeter%'] = $rapport[$key]['capaciteitMeter'] > 0 ? (($rapport[$key]['sollAantalMeter'] / $rapport[$key]['capaciteitMeter'])) : 0;
            $rapport[$key]['lotAantalMeter%'] = $rapport[$key]['capaciteitMeter'] > 0 ? (($rapport[$key]['lotAantalMeter'] / $rapport[$key]['capaciteitMeter'])) : 0;

            $rapport[$key]['totaalAantalKramen'] = $rapport[$key]['vplAantalKramen'] + $rapport[$key]['vkkAantalKramen'] + $rapport[$key]['tvplAantalKramen'] + $rapport[$key]['tvplzAantalKramen'] + $rapport[$key]['expAantalKramen'] + $rapport[$key]['expfAantalKramen'] + $rapport[$key]['sollAantalKramen'] + $rapport[$key]['lotAantalKramen'];
            $rapport[$key]['totaalAantalMeter'] = $rapport[$key]['vplAantalMeter'] + $rapport[$key]['vkkAantalMeter'] + $rapport[$key]['tvplAantalMeter'] + $rapport[$key]['tvplzAantalMeter'] + $rapport[$key]['expAantalMeter'] + $rapport[$key]['expfAantalMeter'] + $rapport[$key]['sollAantalMeter'] + $rapport[$key]['lotAantalMeter'];

            $rapport[$key]['totaalAantalKramen%'] = $rapport[$key]['capaciteitKramen'] > 0 ? (($rapport[$key]['totaalAantalKramen'] / $rapport[$key]['capaciteitKramen'])) : 0;
            $rapport[$key]['totaalAantalMeter%'] = $rapport[$key]['capaciteitMeter'] > 0 ? (($rapport[$key]['totaalAantalMeter'] / $rapport[$key]['capaciteitMeter'])) : 0;
        }

        // bouw abstract rapport model
        $model = new AbstractRapportModel();
        $model->type = 'marktcapaciteit';
        $model->generationDate = date('Y-m-d H:i:s');
        $model->input = ['marktIds' => $marktIds, 'dagStart' => $dateStart, 'dagEind' => $dateEnd];
        $model->output = array_values($rapport);

        return new JsonResponse($model, Response::HTTP_OK, ['X-Api-ListSize' => count($model->output)]);
    }
}
