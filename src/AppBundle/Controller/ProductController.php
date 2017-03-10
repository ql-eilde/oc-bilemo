<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ProductController extends Controller
{
    /**
     * @ApiDoc(
     *     description="Récupère la liste des produits",
     *     output="AppBundle\Entity\Product"
     * )
     * @Rest\View(statusCode=Response::HTTP_OK)
     */
    public function getProductsAction(){
        $products = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findAll();

        return $products;
    }

    /**
     * @ApiDoc(
     *     description="Récupère les détails d'un produit",
     *     requirements={
     *          {
     *              "name"="id",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="Identifiant unique du produit"
     *          }
     *     },
     *     output="AppBundle\Entity\Product"
     * )
     * @Rest\View(statusCode=Response::HTTP_OK)
     */
    public function getProductAction($id){
        $product = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->find($id);

        if(empty($product)){
            return View::create(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $product;
    }
}