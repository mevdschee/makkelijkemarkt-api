<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Controller\Version_1_1_0;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Enum\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning;
use Symfony\Component\Validator\Constraints\DateTime;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Token;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 * @Route("1.1.0")
 */
class LoginController extends Controller
{
    /**
     * Genereert een nieuw token op accountId + password
     *
     * @Method("POST")
     * @Route("/login/basicId/")
     * @ApiDoc(
     *  section="Login",
     *  parameters={
     *      {"name"="accountId", "dataType"="integer", "required"=true, "description"="Account ID"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password"},
     *      {"name"="deviceUuid", "dataType"="string", "required"=false, "description"="UUID van het gebruikte device"},
     *      {"name"="clientApp", "dataType"="string", "required"=false, "description"="Appliciatie type"},
     *      {"name"="clientVersion", "dataType"="string", "required"=false, "description"="Versie van de client"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function basicIdAction(Request $request)
    {
        $message = json_decode($request->getContent(false), true);
        if ($message === null)
            return new JsonResponse(['error' => json_last_error_msg()]);

        $message = array_merge(['accountId'=> null, 'password' => null], $message);

        if ($message['accountId'] === null)
            throw $this->createAccessDeniedException('Account unknown');

        $token = new Token();
        $token->setClientApp(isset($message['clientApp']) === true ? $message['clientApp'] : null);
        $token->setClientVersion(isset($message['clientVersion']) === true ? $message['clientVersion'] : null);
        $token->setDeviceUuid(isset($message['deviceUuid']) === true ? $message['deviceUuid'] : null);
        $token->setLifeTime(60 * 60 * 8 * 1);

        /* @var $accountRepo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\AccountRepository */
        $accountRepo = $this->get('appapi.repository.account');
        $account = $accountRepo->getById($message['accountId']);
        if ($account === null)
            throw $this->createAccessDeniedException('Account unknown');

        if ($account->getLocked()) {
            return new JsonResponse('Account is locked', Response::HTTP_LOCKED, []);
        }

        $token->setAccount($account);

        $account->setLastAttempt(new \DateTime());
        $em = $this->get('doctrine.orm.entity_manager');
        $em->flush();

        $encoder = $this->container->get('security.password_encoder');
        if ($encoder->isPasswordValid($account, $message['password']) === false) {
            $attempts = $account->getAttempts();
            $attempts++;
            $account->setAttempts($attempts++);
            if ($attempts >= 9) {
                $account->setLocked(true);
            }
            $em = $this->get('doctrine.orm.entity_manager');
            $em->flush();
            throw $this->createAccessDeniedException('Password invalid');
        }

        $account->setAttempts(0);
        $em->flush();

        $this->getDoctrine()->getManager()->persist($token);
        $this->getDoctrine()->getManager()->flush();

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\TokenMapper */
        $mapper = $this->get('appapi.mapper.token');
        $response = $mapper->singleEntityToModel($token);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * Genereert een nieuw token op username + password
     *
     * @Method("POST")
     * @Route("/login/basicUsername/")
     * @ApiDoc(
     *  section="Login",
     *  parameters={
     *      {"name"="username", "dataType"="string", "required"=true, "description"="Username"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password"},
     *      {"name"="deviceUuid", "dataType"="string", "required"=false, "description"="UUID van het gebruikte device"},
     *      {"name"="clientApp", "dataType"="string", "required"=false, "description"="Appliciatie type"},
     *      {"name"="clientVersion", "dataType"="string", "required"=false, "description"="Versie van de client"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function basicUsernameAction(Request $request)
    {
        $message = json_decode($request->getContent(false), true);
        if ($message === null)
            return new JsonResponse(['error' => json_last_error_msg()]);

        $message = array_merge(['username' => null, 'password' => null], $message);

        if ($message['username'] === null)
            throw $this->createAccessDeniedException('Account unknown');

        $token = new Token();
        $token->setClientApp(isset($message['clientApp']) === true ? $message['clientApp'] : null);
        $token->setClientVersion(isset($message['clientVersion']) === true ? $message['clientVersion'] : null);
        $token->setDeviceUuid(isset($message['deviceUuid']) === true ? $message['deviceUuid'] : null);
        $token->setLifeTime(60 * 60 * 8 * 1);

        /* @var $accountRepo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\AccountRepository */
        $accountRepo = $this->get('appapi.repository.account');
        $account = $accountRepo->getByUsername($message['username']);
        if ($account === null)
            throw $this->createAccessDeniedException('Account unknown');


        if ($account->getLocked()) {
            return new JsonResponse('Account is locked', Response::HTTP_LOCKED, []);
        }

        $token->setAccount($account);

        $account->setLastAttempt(new \DateTime());
        $em = $this->get('doctrine.orm.entity_manager');
        $em->flush();

        $encoder = $this->container->get('security.password_encoder');
        if ($encoder->isPasswordValid($account, $message['password']) === false) {
            $attempts = $account->getAttempts();
            $attempts++;
            $account->setAttempts($attempts++);
            if ($attempts >= 9) {
                $account->setLocked(true);
            }
            $em = $this->get('doctrine.orm.entity_manager');
            $em->flush();
            throw $this->createAccessDeniedException('Password invalid');
        }

        $account->setAttempts(0);
        $em->flush();

        $this->getDoctrine()->getManager()->persist($token);
        $this->getDoctrine()->getManager()->flush();

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\TokenMapper */
        $mapper = $this->get('appapi.mapper.token');
        $response = $mapper->singleEntityToModel($token);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * @Method("GET")
     * @Route("/login/whoami/")
     * @ApiDoc(
     *  section="Login",
     *  parameters={},
     *  views = { "default", "1.1.0" }
     * )
     * \@Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function whoamiAction(Request $request)
    {
        $account = $this->getUser();
        if ($account === null)
            return new JsonResponse(['account' => null, 'authorization-header' => $request->headers->get('Authorization')], Response::HTTP_NOT_FOUND, []);

        $result = $this->get('appapi.mapper.account')->singleEntityToModel($account);
        return new JsonResponse(['account' => $result, 'authorization-header' => $request->headers->get('Authorization')], Response::HTTP_OK, []);
    }

    /**
     * @Method("GET")
     * @Route("/logout/")
     * @ApiDoc(
     *  section="Login",
     *  parameters={},
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function logoutAction(Request $request)
    {
        $request->headers->get('Authorization');

        $authorizationHeader = $request->headers->get('Authorization');
        if ($authorizationHeader === null)
            return new JsonResponse(['msg' => 'No Authorization header found'], JsonResponse::HTTP_BAD_REQUEST);

        $header = explode(' ', $authorizationHeader);
        if ($header[0] !== 'Bearer')
            return new JsonResponse(['msg' => 'Authorization header not containing Bearer'], JsonResponse::HTTP_BAD_REQUEST);

        if (isset($header[1]) === false)
            return new JsonResponse(['msg' => 'Authorization header not containing ID'], JsonResponse::HTTP_BAD_REQUEST);

        $token = $this->get('appapi.repository.token')->getByUuid($header[1]);
        $token->setLifeTime(0);

        $this->getDoctrine()->getManager()->flush();

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\TokenMapper */
        $mapper = $this->get('appapi.mapper.token');
        $response = $mapper->singleEntityToModel($token);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * @Method("GET")
     * @Route("/login/roles")
     * @ApiDoc(
     *  section="Login",
     *  parameters={},
     *  views = { "default", "1.1.0" }
     * )
     */
    public function rolesListAction()
    {
        return new JsonResponse(Roles::all(), Response::HTTP_OK, []);
    }
}
