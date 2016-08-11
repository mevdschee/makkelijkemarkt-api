<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Controller\Version_1_1_0;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\BtwTarief;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Exception\FactuurServiceException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * @Route("1.1.0")
 */
class BtwController extends Controller
{

    /**
     * Maake of update en btw tarief
     *
     * @Method("POST")
     * @Route("/btw/")
     * @ApiDoc(
     *  section="Btw",
     *  parameters={
     *      {"name"="jaar", "dataType"="integer", "required"=true, "description"="Jaar van het BTW tarief"},
     *      {"name"="hoog", "dataType"="float", "required"=true, "description"="Btw tarief hoog"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function createOrUpdateAction(Request $request)
    {
        $message = json_decode($request->getContent(false), true);

        // check inputs
        if ($message === null)
            return new JsonResponse(['error' => json_last_error_msg()]);
        if (isset($message['jaar']) === false)
            return new JsonResponse(['error' => 'Required field jaar is missing']);
        if (isset($message['hoog']) === false)
            return new JsonResponse(['error' => 'Required field hoog is missing']);

        $em = $this->getDoctrine()->getManager();
        $btwTariefRepo =  $repo = $em->getRepository('AppApiBundle:BtwTarief');

        $btwTarief = $btwTariefRepo->findOneBy(array('jaar'=>$message['jaar']));

        if (null === $btwTarief) {
            $btwTarief = new BtwTarief();
            $btwTarief->setJaar($message['jaar']);
            $em->persist($btwTarief);
        }

        $btwTarief->setHoog($message['hoog']);

        $em->flush();

        $mapper = $this->get('appapi.mapper.btwtarief');
        $result = $mapper->singleEntityToModel($btwTarief);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Zoek door alle markten
     *
     * @Method("GET")
     * @Route("/btw/")
     * @ApiDoc(
     *  section="Btw",
     *  filters={
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppApiBundle:BtwTarief');

        $results = $repo->findAll();

        $mapper = $this->get('appapi.mapper.btwtarief');
        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

}
