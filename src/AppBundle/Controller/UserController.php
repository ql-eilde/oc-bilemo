<?php

namespace AppBundle\Controller;

use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UserController extends Controller
{
    /**
     * @ApiDoc(
     *     description="Récupère la liste des utilisateurs",
     *     section="User",
     *     output={
     *         "class"="AppBundle\Entity\User",
     *         "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *     },
     *     statusCodes={
     *         200="Returned when successful"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_OK)
     */
    public function getUsersAction(){
        $users = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findAll();

        return $users;
    }

    /**
     * @ApiDoc(
     *     description="Récupère le détail d'un utilisateur",
     *     requirements={
     *          {
     *              "name"="id",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Identifiant unique de l'utilisateur"
     *          }
     *     },
     *     section="User",
     *     output={
     *         "class"="AppBundle\Entity\User",
     *         "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *     },
     *     statusCodes={
     *         200="Returned when the user is found",
     *         404="Returned when the user is not found"
     *     },
     * )
     * @Rest\View(statusCode=Response::HTTP_OK)
     */
    public function getUserAction($id){
        $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find($id);

        if(empty($user)){
            return View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    /**
     * @ApiDoc(
     *     description="Création d'un utilisateur",
     *     section="User",
     *     input={
     *         "class"="FOS\UserBundle\Form\Type\RegistrationFormType",
     *         "name"=""
     *     },
     *     statusCodes={
     *         201="Returned when the user is created",
     *         400="Returned when error in the payload"
     *     },
     * )
     * @Rest\View()
     */
    public function postUsersAction(Request $request)
    {
        //TODO : Put this in a service

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();

        $form->setData($user);
        $form->submit($request->request->all());

        if (!$form->isValid()) {

            $event = new FormEvent($form, $request);

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }

            return $form;
        }

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $userManager->updateUser($user);

        $response = new JsonResponse('User created', JsonResponse::HTTP_CREATED);

        $dispatcher->dispatch(
            FOSUserEvents::REGISTRATION_COMPLETED,
            new FilterUserResponseEvent($user, $request, $response)
        );

        return $response;
    }
}