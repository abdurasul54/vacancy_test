<?php

namespace App\Controller;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
class ProductController extends AbstractController 
{
   #[Route("/product", name:"product_index", methods:["GET"])]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
    
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products,
        ]);
    }
    
    

    #[Route("/product/{id}", name:"product_show", methods:["GET"])]
    public function show(ManagerRegistry $doctrine, $id): Response
    {
        
        $products = $doctrine->getRepository(Product::class)->find($id);

        $data = [];
        if(!is_null($products)){
        
            $data['data'] = [
                'id' => $products->getId(),
                'name' => $products->getName(),
                'price' => $product->getPrice(),
                'description' => $products->getDescription(),
            ];
        
            return $this->json($data);
        }else
            return $this->json(['status'=>'None product with id = '.$id]);
    }

}
