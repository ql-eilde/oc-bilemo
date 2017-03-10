<?php

namespace AppBundle\Controller;

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
     *     output="AppBundle\Entity\User"
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
     *     output="AppBundle\Entity\User"
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
     *     parameters={
     *          {
     *              "name"="username",
     *              "dataType"="string",
     *              "required"="true",
     *              "format"="{not blank}, {length: min: 2, max: 180}",
     *              "description"="Saisir un nom d'utilisateur"
     *          },
     *          {
     *              "name"="email",
     *              "dataType"="string",
     *              "required"="true",
     *              "format"="{not blank}, {length: min: 2, max: 180}, {email address}",
     *              "description"="Saisir un email"
     *          },
     *          {
     *              "name"="password",
     *              "dataType"="",
     *              "required"="true",
     *              "format"="{not blank}, {length: min: 2, max: 4096}",
     *              "description"="Saisir un mot de passe"
     *          }
     *     },
     * )
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