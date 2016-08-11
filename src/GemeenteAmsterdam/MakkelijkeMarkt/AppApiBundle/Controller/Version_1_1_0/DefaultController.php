<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Controller\Version_1_1_0;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 * @Route("1.1.0")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function defaultAction(Request $request)
    {
        return new JsonResponse(['msg' => 'Hallo!'], Response::HTTP_OK);
    }
}
