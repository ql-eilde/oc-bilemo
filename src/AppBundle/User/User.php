<?php

namespace AppBundle\User;

use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Form\Factory\FormFactory;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;

class User
{
    private $formFactory;
    private $userManager;
    private $dispatcher;

    public function __construct(FormFactory $formFactory, UserManager $userManager, TraceableEventDispatcher $dispatcher)
    {
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->dispatcher = $dispatcher;
    }

    public function postUser(Request $request)
    {
        $user = $this->userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createForm();

        $form->setData($user);
        $form->submit($request->request->all());

        if (!$form->isValid()) {

            $event = new FormEvent($form, $request);

            $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }

            return $form;
        }

        $event = new FormEvent($form, $request);
        $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $this->userManager->updateUser($user);

        $response = new JsonResponse('User created', JsonResponse::HTTP_CREATED);

        $this->dispatcher->dispatch(
            FOSUserEvents::REGISTRATION_COMPLETED,
            new FilterUserResponseEvent($user, $request, $response)
        );

        return $response;
    }
}
