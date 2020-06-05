<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Visite;
use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UserClientType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ClientController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        $user->setRegistredAt(new \DateTime('now'));
        $user->setIsActive(true);
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
    public function index(){
      //nombre de visite du site
      $nb_visite = $this->getDoctrine()->getManager(); 
      $nb = $nb_visite->getRepository(Visite::class)->findOneBy(['id' => '1']);
     if($nb){
         $nb->setNbVisite($nb->getNbVisite()+1);
       $nb_visite->persist($nb);
       $nb_visite->flush();
      } 
      else{
        $user = new Visite();
        $user->setNbVisite('1');
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();
      }
      dump($nb);
        return $this->render('client/index.html.twig',[
           'nb_visite'=>$nb
        ]);
    }

    /**
     * @Route("/shop/{page}",defaults={"page"=1}, name="shop")
     */
    public function shop($page)
        {   $totalPages=$this->getDoctrine()->getRepository(Produit::class)->totalPages();
            $maxPerPage=6;
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
    /*public function search()
    {  

    }*/

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('client/about.html.twig',[
            'pagetitle'=>'About us',
           
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render('client/contact.html.twig',[
            'pagetitle'=>'Contact',
           
        ]);
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function cart()
    {
        return $this->render('client/cart.html.twig');
    }
    /**
     * @Route("/chekout", name="c")
     */
    public function check()
    {
        return $this->render('client/chekout.html.twig');
    }


    public function Checkout()
    {
     $form=$this->createFormBuilder(null, array('label' => false))
                 ->add('items',TextareaType::class, array('label' => false))
                 ->getForm()
                 ;
         return $this->render('client/CheckoutForm.html.twig',['form'=>$form->createView()]);        
    }
    public function search()
    {

        // class="form-control my-0 py-1 amber-border" type="text" placeholder="Search" aria-label="Search"
        $form=$this->createFormBuilder(null, array('label' => false))
        ->add('crit',TextType::class, array('label' => false,'attr' => array(
            'placeholder' => 'search for a product','style'=>'width:350px;margin:auto','class'=>"form-control my-0 py-1 amber-border",'aria-label'=>"Search"
        )))
        ->getForm();
        return $this->render('client/SearchForm.html.twig',['form'=>$form->createView()]);

    }
    /**
     * @Route("/HandleCheckout", name="HandleCheckout")
     */
    
    public function HandleCheckout(Request $request)
    {$frm=$request->request->get('form');
      //json_decode($frm['items']);
      //die();
      return $this->render('client/chekout.html.twig',['items'=>json_decode($frm['items'])]); 

      
    }
       /**
     * @Route("/HandleSearch", name="HandleSearch")
     */
    
    public function HandleSearch(Request $request)
    {$frm=$request->request->get('form');
        $maxPerPage=3;
        $result=$this->getDoctrine()->getRepository(Produit::class)->search($frm['crit']);
        return $this->render('client/shop.html.twig',[
            'pagetitle' => 'Store',
            'products'=>$result,
            'current'=>1,
            'totalPages'=>1
        ]);
     

      
    }
    /**
     * @Route("/listtri", name="listtri")
     */
    public function listtri()
    {
        $doctrine = $this->getDoctrine();
        $repository = $doctrine->getRepository(Produit::class);
        $produits=$repository->findAll();
        $doctrine = $this->getDoctrine();
        $repository = $doctrine->getRepository(Categorie::class);
        $categories=$repository->findAll();
        return $this->render('client/index.html.twig', ['produits'=>$produits,'categories'=>$categories]);
    }

     /**
     * @Route("/detailspro/{id}", name="details")
     */
    public function detailprod($id)
    {
        $doctrine = $this->getDoctrine();
        $repository = $doctrine->getRepository(Produit::class);
        $produits=$repository->findBy( ['id'=>$id]);
        return $this->render('client/detailsprod.html.twig', ['produit'=>$produits[0]]);
    
    }

    /**
     * @Route("/", name="combocat")
     */
    
    public function combocatg()
    {
        $doctrine = $this->getDoctrine();
        $repository = $doctrine->getRepository(Categorie::class);
        $categories=$repository->findAll();
        return $this->render('client/base.html.twig', ['categories'=>$categories]);
        
    }
  // public function combocatg(CategorieRepository $CategorieRepository)
    // {
    //     return $this->render('client/base.html.twig' ,
    //     array('categorie'=> $CategorieRepository->findall()
    //     ));
    // }
}
