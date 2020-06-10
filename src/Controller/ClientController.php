<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Visite;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\Reclamation;
use App\Entity\Proprietaire;
use App\Form\UserClientType;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


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
      $doctrine = $this->getDoctrine();
      $repository = $doctrine->getRepository(Produit::class);
      $produits=$repository->getLastProduct();
      $repository = $doctrine->getRepository(Categorie::class);
      $categories=$repository->findAll();
      $repository=$doctrine->getRepository(Proprietaire::class);
      $pharmacie=$repository->getLastPharmacie();
        return $this->render('client/index.html.twig',[
           'nb_visite'=>$nb,
           'produits'=>$produits,
           'categories'=>$categories,
            'pharmacie'=>$pharmacie
        ]);
    }

    /**
     * @Route("/shop", name="shop")
     */
    public function shop(Request $request,PaginatorInterface $paginator)
        {   /*$totalPages=$this->getDoctrine()->getRepository(Produit::class)->totalPages();
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
        ]);*/
        $produits=$this->getDoctrine()->getRepository(Produit::class)->findAll();
        $page = $paginator->paginate(
            $produits,
            $request->query->getInt('page', 1),
            8
        );
        return $this->render('client/shop.html.twig',[
            'pagetitle'=>'shop',
            'products' => $produits,
            'page' => $page
            ]);
    
    }
   
    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('client/about.html.twig',[
            'pagetitle'=>'About us'
           
        ]);
    }

    /**
     * @Route("/contact", name="contactclient")
     */
    public function contact(Request $request)
    {   
  
        $reclamation = new Reclamation();
        $user= $this->getUser();
        $formcontact = $this->createForm(ReclamationType::class, $reclamation);
        $formcontact->handleRequest($request);

        if($formcontact->isSubmitted() && $formcontact->isValid()){
        $reclamation->setEmmeteur('client');
        $reclamation->setUser($user);
        $em = $this->getDoctrine()->getManager();

        $em->persist($reclamation);
        $em->flush();
        $this->addFlash('success', 'Votre réclamation a été envoyé !');

        return $this->redirectToRoute('contactclient');
        }
        return $this->render('client/contact.html.twig',[
            'pagetitle'=>'Contact',
            'formcontact'=>$formcontact->createView()

           
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
        $form=$this->createFormBuilder(null)
        ->add('crit',TextType::class, array('label' => false,'attr' => array(
            'placeholder' => 'search for a product','style'=>'width:350px'
        )))
        ->add('ville', ChoiceType::class,[
            'choices'  => [
                'toutes les villes'=>'all',
                'Casablanca' => 'Casablanca',
                'Fès' => 'Fès',
                'Salé' => 'Salé',
                'Tanger' => 'Tanger',
                'Marrakech' => 'Marrakech',
                'Meknès' => 'Meknès',
                'Rabat' => 'Rabat',
                'Oujda' => 'Oujda',
                'Kénitra' => 'Kénitra',
                'Agadir' => 'Agadir',
                'Tétouan' => 'Tétouan',
                'Témara' => 'Témara',
                'Safi' => 'Safi',
                'Mohammédia' => 'Mohammédia',
                'Khouribga' => 'Khouribga',
                'El Jadida' => 'El Jadida',
                'Béni Mellal' => 'Béni Mellal',
                'Nador' => 'Nador',
                'Taza' => 'Taza',
                'Khémisset' => 'Khémisset',
                'Autre...' => 'Autre',
            ],'label'=>false
        ])
        ->add('Chercher', SubmitType::class, array(
            'attr'=>array(
                'class'=>'btn btn-primary bnt-block','style'=>'margin-left:5px'
            )
            ))
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
    
    public function HandleSearch(Request $request,PaginatorInterface $paginator)
    {    $frm=$request->request->get('form');
        
        
        $produits=$this->getDoctrine()->getRepository(Produit::class)->search($frm['crit'],$frm['ville']);
       
        $page = $paginator->paginate(
            $produits,
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('client/shop.html.twig',[
            'pagetitle'=>'shop',
            'products' => $produits,
            'page' => $page
            ]);
     

      
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
     * @Route("/categories", name="categories")
     */
    
    public function categories()
    {
        $doctrine = $this->getDoctrine();
        $repository = $doctrine->getRepository(Categorie::class);
        $cat=$repository->findAll();
         return $this->json(array_map(function($x){return $x->getNom();}, $cat),200);
        
    }
    /**
     * @Route("/profilepharma", name="profile")
     */
    public function profile() {

        return $this->render('client/detailspharmacie.html.twig',[
            'pagetitle'=>'profile'
            ]);
    }

    /**
     * @Route("/pharmacies", name="toutpharmacies")
     */
    public function listpharma(PaginatorInterface $paginator, Request $request){
        $pharmacies = $this->getDoctrine()->getRepository(Proprietaire::class)->findAll();
        $page = $paginator->paginate(
            $pharmacies,
            $request->query->getInt('page', 1),
            9
        );
        return $this->render('client/list-pharmacie.html.twig',[
            'pagetitle'=>'pharmacies',
            'pharmacies' => $pharmacies,
            'page' => $page
            ]);
    }
    
    /**
     * @Route("/cat/{name}", name="cat")
     */
    public function cat(PaginatorInterface $paginator, Request $request,$name){
        $result=$this->getDoctrine()->getRepository(Produit::class)->getProductByCategorie($name);
        $page = $paginator->paginate(
            $result,
            $request->query->getInt('page', 1),
            9
        );
        return $this->render('client/shop.html.twig',[
            'pagetitle'=>'shop',
            'products' => $result,
            'page' => $page
            ]);

       
    }
}
