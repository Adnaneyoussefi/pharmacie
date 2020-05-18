<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UserClientType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ClientController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        $form = $this->createForm(UserClientType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $role[] = 'ROLE_USER';
            $user->setRoles($role);
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('client/inscription.html.twig',[
            'pagetitle'=>'Inscription',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('client/index.html.twig');
    }

    /**
     * @Route("/shop/{page}",defaults={"page"=1}, name="shop")
     */
    public function shop($page)
        {   $totalPages=$this->getDoctrine()->getRepository(Produit::class)->totalPages();
            $maxPerPage=3;
            $offset=$page*$maxPerPage;
            $products = $this->getDoctrine()
            ->getRepository(Produit::class)
            ->findByPage($offset,$maxPerPage);
        return $this->render('client/shop.html.twig',[
            'pagetitle' => 'Store',
            'products'=>$products,
            'totalPages'=>ceil($totalPages/$maxPerPage),
            'current'=>$page
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('client/about.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render('client/contact.html.twig');
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function cart()
    {
        return $this->render('client/cart.html.twig');
    }
    

    


}
