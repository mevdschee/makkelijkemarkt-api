<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Controller\Version_1_1_0;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 * @Route("1.1.0")
 */
class MarktController extends Controller
{
    /**
     * Zoek door alle markten
     *
     * @Method("GET")
     * @Route("/markt/")
     * @ApiDoc(
     *  section="Markt",
     *  filters={
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function listAction(Request $request)
    {
        /* @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\MarktRepository */
        $repo = $this->get('appapi.repository.markt');

        $results = $repo->findAll();

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\MarktMapper */
        $mapper = $this->get('appapi.mapper.markt');
        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Vraag een markt op
     *
     * @Method("GET")
     * @Route("/markt/{id}")
     * @ApiDoc(
     *  section="Markt",
     *  filters={
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function getAction($id)
    {
        /* @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\MarktRepository */
        $repo = $this->get('appapi.repository.markt');

        $result = $repo->findOneById($id);

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\MarktMapper */
        $mapper = $this->get('appapi.mapper.markt');
        $response = $mapper->singleEntityToModel($result);

        return new JsonResponse($response, Response::HTTP_OK);
    }

}
