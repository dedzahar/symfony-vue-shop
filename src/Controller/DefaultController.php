<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request): Response
    {
        $productList = $this->managerRegistry->getRepository(Product::class)->findAll();

        return $this->render('main/default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/product-edit/{id}", methods="GET|POST", name="product-edit", requirements={"id"="\d+"})
     * @Route("/product-add", methods="GET|POST", name="product-add")
     */
    public function productEdit(Request $request, int $id = null): Response
    {
        if($id) {
            $product = $this->managerRegistry->getRepository(Product::class)->find($id);
        } else {
            $product = new Product();
        }
        $form = $this->createForm(ProductFormType::class, $product);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $manager = $this->managerRegistry->getManager();
            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('product-edit', [
                'id' => $product->getId()
            ]);
        }
        return $this->render('main/default/edit_product.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
