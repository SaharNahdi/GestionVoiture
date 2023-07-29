<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ProductRepository;
use App\Entity\Product;

class ProductMobile extends AbstractController
 /**
     * @Route("/mobile/product", name="mobile_product")
     */
{
    /**
     * @Route("/", name="mobile_product")
     */
    public function index(): Response
    {
        return $this->render('mobile_product/index.html.twig', [
            'controller_name' => 'MobileProductController',
        ]);
    }

    /**
     * @Route("/all", name="getAllProducts")
     */
    public function allProducts()
    {
        $products = $this->getDoctrine()->getManager()->getRepository(  Product::class)->findALL();
        $serializer = new Serializer ([new ObjectNormalizer()]);
        $formatted = $serializer->normalize ($products);
        return new JsonResponse ($formatted);
    }
      /**
     * @Route("/ajout", name="add_new")
     */

    public function ajouterReclamationAction (Request $request){ 
   $product = new Product();
   $name = $request->query->get("name");
   $description = $request->query->get("description");
   $price = $request->query->get("price");
   $brand = $request->query->get("brand");
   $quantity = $request->query->get("quantity");
   $picture = $request->query->get("picture");
   
   $em = $this->getDoctrine()->getManager ();
    $product->setName ($name);
   $product->setDescription($description);
   $product->setPrice($price);
   $product->setBrand( $brand);
   $product->setQuantity( $quantity);
   $product->setPicture($picture);
  
    $em->persist ($product);
   $em->flush();
   $serializer = new Serializer([new ObjectNormalizer()]);
   $formatted = $serializer->normalize($product);
   return new JsonResponse ($formatted);

}


 /**
     * @Route("/delete", name="delete_one")
     */

public function deleteProductAction (Request $request) {
    $id = $request->get("id");
    $em = $this->getDoctrine()->getManager ();
    $product = $em->getRepository(  Product::class)->find($id);
    if($product !=null ) {
       $em->remove ($product);
        $em->flush();
       $serializer =new Serializer ([new ObjectNormalizer ()]);
       $formatted = $serializer->normalize( "Produit a ete supprimée avec success.");
        return new JsonResponse ($formatted);
    }
    return new JsonResponse("id produit invalide.");


}
 /**
     * @Route("/update", name="modifier_une")
     * Method("PUT")
     */

public function modifierProductAction(Request $request) {
    $em = $this->getDoctrine()->getManager();
    $product = $this->getDoctrine()->getManager ()
                  ->getRepository(  Product::class)
                  ->find($request->get("id"));
    $product->setName($request->get("name"));
    $product->setDescription($request->get("description"));
    $product->setPrice($request->get("price"));
    $em->persist ($product);
    $em->flush();
    $serializer = new Serializer ([new ObjectNormalizer()]);
    $formatted = $serializer->normalize($product);
    return new JsonResponse("Produit a ete modifiee avec success.");
}

/**
     * @Route("/detail", name="detail_one")
     */


public function detailProductAction(Request $request) {   
   $id = $request->get("id");
   $em = $this->getDoctrine()->getManager();
   $product = $this->getDoctrine()->getManager ()->getRepository(  Product::class)->find($id);
   $encoder = new JsonEncoder ();
   $normalizer = new ObjectNormalizer();
   $normalizer->setCircularReferenceHandler (function ($object) {
       return $object->getDescription();
  });
   $serializer = new Serializer([$normalizer], [$encoder]);
   $formatted = $serializer->normalize($product);
   return new JsonResponse ($formatted);
}
}
