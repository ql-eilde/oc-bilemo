<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

class UserController extends Controller
{
    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     */
    public function getUsersAction(){
        $users = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findAll();

        return $users;
    }

    /**
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
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     */
    public function postUsersAction(Request $request){
        $manip = $this->get('fos_user.util.user_manipulator');

        if(!empty($request->get('username')) && !empty($request->get('email')) && !empty($request->get('password'))){
            $manip->create($request->get('username'), $request->get('email'), $request->get('password'), 1, null);
            return View::create(['message' => 'User created']);
        } else {
            return View::create(['message' => 'Something went wrong, please try again'], Response::HTTP_BAD_REQUEST);
        }
    }
}