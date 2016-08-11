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
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning;
use Symfony\Component\Validator\Constraints\DateTime;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Account;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 * @Route("1.1.0")
 */
class VersionController extends Controller
{
    /**
     * Geeft versie nummer
     *
     * @Method("GET")
     * @Route("/version/")
     * @ApiDoc(
     *  section="Version",
     *  views = { "default", "1.1.0" }
     * )
     */
    public function getAction(Request $request)
    {
        /* @var $kernel \AppKernel */
        $kernel = $this->get('kernel');

        return new JsonResponse(
            [
                'apiVersion'     => $kernel->getVersion(),
                'androidVersion' => $this->getParameter('android_version'),
                'androidBuild'   => $this->getParameter('android_build')
            ], Response::HTTP_OK);
    }
}
