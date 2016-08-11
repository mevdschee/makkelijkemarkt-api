<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Controller\Version_1_1_0;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\AbstractRapportModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 * @Route("1.1.0")
 */
class ReportController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/rapport/dubbelstaan/{dag}")
     * @ApiDoc(
     *  section="Rapport",
     *  requirements={
     *      {"name"="dag", "required"="true", "dataType"="string", "description"="date as yyyy-mm-dd"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function dubbelstaanRaportAction($dag)
    {
        /* @var $dagvergunningMapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\DagvergunningMapper */
        $dagvergunningMapper = $this->get('appapi.mapper.dagvergunning');
        /* @var $koopmanMapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\KoopmanMapper */
        $koopmanMapper = $this->get('appapi.mapper.koopman');

        // eerst alle erkenningsnummers selecteren die meerdere ACTIEVE vergunningen hebben voor een bepaalde dag
        $qb = $this->getDoctrine()->getRepository('AppApiBundle:Dagvergunning')->createQueryBuilder('dagvergunning');
        $qb ->select('dagvergunning.erkenningsnummerInvoerWaarde AS erkenningsnummer')
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
            $qb = $this->getDoctrine()->getRepository('AppApiBundle:Dagvergunning')->createQueryBuilder('dagvergunning');
            $qb ->select('dagvergunning')
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
            $qb = $this->getDoctrine()->getRepository('AppApiBundle:Koopman')->createQueryBuilder('koopman');
            $qb ->select('koopman')
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
            if (isset($koopmanData[$record['erkenningsnummer']]))
                $koopman = $koopmanMapper->singleEntityToSimpleModel($koopmanData[$record['erkenningsnummer']]);
            $model->output[] = [
                'erkenningsnummer' => $record['erkenningsnummer'],
                'aantalDagvergunningenUitgegeven' => $record['aantal'],
                'koopman' => $koopman,
                'dagvergunningen' => $dagvergunningMapper->multipleEntityToModel($detailData[$record['erkenningsnummer']])
            ];
        }

        return new JsonResponse($model, Response::HTTP_OK, ['X-Api-ListSize' => count($selector)]);
    }

    /**
     * @Method("GET")
     * @Route("/rapport/staanverplichting/{marktId}/{dagStart}/{dagEind}/{vergunningType}")
     * @ApiDoc(
     *  section="Rapport",
     *  requirements={
     *      {"name"="marktId", "required"="true", "dataType"="integer", "description"="ID van markt"},
     *      {"name"="dagStart", "required"="true", "dataType"="string", "description"="date as yyyy-mm-dd"},
     *      {"name"="dagEind", "required"="true", "dataType"="string", "description"="date as yyyy-mm-dd"},
     *      {"name"="vergunningType", "required"="true", "dataType"="string", "description"="alle|soll|vkk|vpl|lot"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function staanverplichtingRapportAction($marktId, $dagStart, $dagEind, $vergunningType)
    {
        /* @var $koopmanMapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\KoopmanMapper */
        $koopmanMapper = $this->get('appapi.mapper.koopman');

        // get the right markt
        $markt = $this->getDoctrine()->getRepository('AppApiBundle:Markt')->find($marktId);
        if ($markt === null)
            throw $this->createNotFoundException('Markt unknown');

        $qb = $this->getDoctrine()->getRepository('AppApiBundle:Dagvergunning')->createQueryBuilder('dagvergunning');
        $qb->select('dagvergunning.erkenningsnummerInvoerWaarde AS erkenningsnummer');
        $qb->addSelect('dagvergunning.statusSolliciatie AS status');
        $qb->addSelect('COUNT(dagvergunning.id) AS aantal');
        $qb->andWhere('dagvergunning.dag BETWEEN :startDate AND :endDate');
        $qb->setParameter('startDate', $dagStart);
        $qb->setParameter('endDate', $dagEind);
        $qb->andWhere('dagvergunning.markt = :markt');
        $qb->setParameter('markt', $markt);
        $qb->andWhere('dagvergunning.doorgehaald = :doorgehaald');
        if ('alle' !== $vergunningType) {
            $qb->andWhere('dagvergunning.statusSolliciatie = :statusSolliciatie');
            $qb->setParameter('statusSolliciatie', $vergunningType);
        }
        $qb->setParameter('doorgehaald', false);
        $qb->addGroupBy('dagvergunning.erkenningsnummerInvoerWaarde');
        $qb->addGroupBy('dagvergunning.statusSolliciatie');
        $qb->addOrderBy('aantal', 'DESC');
        $selector = $qb->getQuery()->execute([], Query::HYDRATE_ARRAY);

        // bouw abstract rapport model
        $model = new AbstractRapportModel();
        $model->type = 'staanverplichting';
        $model->generationDate = date('Y-m-d H:i:s');
        $model->input = ['marktId' => $marktId, 'dagStart' => $dagStart, 'dagEind' =>  $dagEind];
        $model->output = [];

        // make a indexed quick lookup array of koopmannen
        $koopmannen = [];
        $qb = $this->getDoctrine()->getRepository('AppApiBundle:Koopman')->createQueryBuilder('koopman');
        $qb->select('koopman');
        $qb->join('koopman.sollicitaties', 'sollicitatie');
        $qb->andWhere('sollicitatie.markt = :markt');
        $qb->setParameter('markt', $markt);
        $unindexedKoopmannen = $qb->getQuery()->execute();
        foreach ($unindexedKoopmannen as $koopman) {
            $koopmannen[$koopman->getErkenningsnummer()] = $koopman;
        }

        // create output
        foreach ($selector as $record) {
            $model->output[] = [
                'erkenningsnummer' => $record['erkenningsnummer'],
                'aantalDagvergunningenUitgegeven' => $record['aantal'],
                'koopman' => isset($koopmannen[$record['erkenningsnummer']]) ? $koopmanMapper->singleEntityToSimpleModel($koopmannen[$record['erkenningsnummer']]) : null,
                'status' => $record['status']
            ];
        }

        return new JsonResponse($model, Response::HTTP_OK, ['X-Api-ListSize' => count($selector)]);
    }

    /**
     * @Method("GET")
     * @Route("/rapport/frequentie/{marktId}/{type}/{dagStart}/{dagEind}")
     * @ApiDoc(
     *  section="Rapport",
     *  requirements={
     *      {"name"="marktId", "required"="true", "dataType"="integer", "description"="ID van markt"},
     *      {"name"="type", "required"="true", "dataType"="string", "description"="dag|week|soll"},
     *      {"name"="dagStart", "required"="true", "dataType"="string", "description"="date as yyyy-mm-dd"},
     *      {"name"="dagEind", "required"="true", "dataType"="string", "description"="date as yyyy-mm-dd"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function frequentieRapportAction($marktId, $type, $dagStart, $dagEind)
    {
        $repo = $this->getDoctrine()->getRepository('AppApiBundle:Dagvergunning');

        $response = array();
        if (in_array($type,array('dag', 'week'))) {
            $response = $repo->getMarktFrequentieDag($marktId, $dagStart, $dagEind);
        } elseif ('soll' === $type) {
            $response = $repo->getMarktFrequentieSollicitanten($marktId, $dagStart, $dagEind);
        }

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($response)]);
    }

    /**
     * @Method("GET")
     * @Route("/rapport/aanwezigheid/{marktId}/{dagStart}/{dagEind}")
     * @ApiDoc(
     *  section="Rapport",
     *  requirements={
     *      {"name"="marktId", "required"="true", "dataType"="integer", "description"="ID van markt"},
     *      {"name"="dagStart", "required"="true", "dataType"="string", "description"="date as yyyy-mm-dd"},
     *      {"name"="dagEind", "required"="true", "dataType"="string", "description"="date as yyyy-mm-dd"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function persoonlijkeAanwezigheidRapportAction($marktId, $dagStart, $dagEind)
    {
        $repo = $this->getDoctrine()->getRepository('AppApiBundle:Dagvergunning');

        $response = $repo->getMarktPersoonlijkeAanwezigheid($marktId, $dagStart, $dagEind);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($response)]);
    }

    /**
     * @Method("GET")
     * @Route("/rapport/invoer/{marktId}/{dagStart}/{dagEind}")
     * @ApiDoc(
     *  section="Rapport",
     *  requirements={
     *      {"name"="marktId", "required"="true", "dataType"="integer", "description"="ID van markt"},
     *      {"name"="dagStart", "required"="true", "dataType"="string", "description"="date as yyyy-mm-dd"},
     *      {"name"="dagEind", "required"="true", "dataType"="string", "description"="date as yyyy-mm-dd"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function invoerRapportAction($marktId, $dagStart, $dagEind)
    {
        $repo = $this->getDoctrine()->getRepository('AppApiBundle:Dagvergunning');

        $response = $repo->getInvoer($marktId, $dagStart, $dagEind);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($response)]);
    }
}