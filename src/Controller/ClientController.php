<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Visite;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\Categorie;
use App\Entity\Reclamation;
use App\Entity\Proprietaire;
use App\Form\UserClientType;
use App\Form\ReclamationType;
use App\Entity\DetailsCommande;
use Symfony\Component\Form\FormError;
use App\Form\ClientChangePasswordType;
use App\Form\ClientChangeInfoPersoType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;




class ClientController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        $user->setRegistredAt(new \DateTime('now'));
        $user->setIsActive(true);
        $user->setStatut('Activé');
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
      $doctrine = $this->getDoctrine();
      $repository = $doctrine->getRepository(Categorie::class);
      $totalCategories=$repository->totalCategories();
      $categories_footer=$repository->getLimitedCategories(4);
      //$categories_footer[]= ['haveMore'=>($totalCategories == count($categories_footer))?false:true];
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
            'pharmacie'=>$pharmacie,

        ]);
    }
    /**
     * @Route("/categories/{limit}", name="categories")
     */
    
    public function categories($limit)
    {   //change max results in dropdown here
        $maxResult=4;
        $doctrine = $this->getDoctrine();
        $repository = $doctrine->getRepository(Categorie::class);
        $totalCategories=$repository->totalCategories();
        $cat=$repository->getLimitedCategories(($limit)?$maxResult:false);
        $t=($totalCategories == count($cat))?false:true;
         if($limit)
         return $this->json(['c'=>array_map(function($x){return $x->getNom();}, $cat),'haveMore'=>$t],200);
        else 
         return $this->redirectToRoute('Allcategories'); 
        
    }

    /**
     * @Route("/shop", name="shop")
     */
    public function shop(Request $request,PaginatorInterface $paginator,SessionInterface $session)
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

        $session->set("tempCategorie",null);
        $produits=$this->getDoctrine()->getRepository(Produit::class)->getProducts();
        $page = $paginator->paginate(
            $produits,
            $request->query->getInt('page', 1),
            8
        );
        return $this->render('client/shop.html.twig',[
            'pagetitle'=>'shop',
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
     * @Route("/thankyou", name="thankyou")
     */
    public function thankyou()
    {
        return $this->render('client/thankyou.html.twig',['pagetitle'=>'Merci!']);
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
        //return $this->render('client/chekout.html.twig',['villes'=>$villes]);
    }


    public function Checkout()
    {
     $form=$this->createFormBuilder(null, array('label' => false))
                 ->add('items',TextareaType::class, array('label' => false))
                 ->getForm()
                 ;
         return $this->render('client/CheckoutForm.html.twig',['form'=>$form->createView()]);        
    }
    public function search($categorie=null)
    {
    
        // class="form-control my-0 py-1 amber-border" type="text" placeholder="Search" aria-label="Search"
        $form=$this->createFormBuilder(null)
        ->setAction($this->generateUrl('HandleSearch'))
        ->add('crit',TextType::class, array('label' => false,'required'=>false,'attr' => array(
            'placeholder' => 'search for a product','style'=>'width:350px'
        )))
        ->add('ville', ChoiceType::class,[
            'choices'  => [
                'toutes les villes'=> false,
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
        ->add('min', NumberType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Min'
            ]
        ])
        ->add('max', NumberType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Max'
            ]
        ])
        ->add('Chercher', SubmitType::class, array(
            'attr'=>array(
                'class'=>'btn btn-primary mt-2'
            )
            ))
        ->add('categorie', HiddenType::class,['data'=>$categorie])   
        ->getForm();
        return $this->render('client/SearchForm.html.twig',['form'=>$form->createView()]);

    }

    public function searchprop()
    {
        $form=$this->createFormBuilder(null)
        ->setAction($this->generateUrl('HandleSearchProp'))
        ->add('nom',TextType::class, array('label' => false,'required'=>false,'attr' => array(
            'placeholder' => 'Entrer le nom du Parapharmacie','style'=>'width:350px'
        )))
        ->add('ville', ChoiceType::class,[
            'choices'  => [
                'toutes les villes'=> false,
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
                'class'=>'btn btn-primary mt-2'
            )
            ))
        ->getForm();
        return $this->render('client/SearchFormProp.html.twig',['form'=>$form->createView()]);

    }
    /**
     * @Route("/HandleCheckout", name="HandleCheckout")
     */
    
    public function HandleCheckout(Request $request)
    {   //date commande
        //$DateCommande = new DetailsCommande();
        //$DateCommande->setDateCommande(new \DateTime('now'));
        //------------------------//
        
        $frm=$request->request->get('form');
        $villes=[
            'Casablanca', 'Fès', 'Salé', 'Tanger',   'Meknès', 'Rabat', 'Oujda',
            'Kénitra','Agadir', 'Tétouan', 'Témara', 'Safi', 'Mohammédia', 'Khouribga',
             'El Jadida', 'Béni Mellal',  'Nador',   'Taza', 'Khémisset',];
      //json_decode($frm['items']);
      //die();
      return $this->render('client/chekout.html.twig',['villes'=>$villes,'items'=>json_decode($frm['items'])]); 

      
    }
       /**
     * @Route("/HandleSearch", name="HandleSearch")
     */
    
    public function HandleSearch(Request $request,PaginatorInterface $paginator,SessionInterface $session)
    {    
           $frm=$request->request->get('form'); 
           if(!$frm)
           {
            $produits=$session->get('Storetemp',[]);
            
           }
           else{
           $produits=$this->getDoctrine()->getRepository(Produit::class)->search($frm['crit'], $frm['ville'], $frm['min'], $frm['max'],$frm['categorie']);
           $session->set('Storetemp',$produits);
           if($frm['categorie'])
                $session->set("tempCategorie",$frm['categorie']);
           }
         $page = $paginator->paginate(
            $produits,
            $request->query->getInt('page', 1),
            8
        );
       
        $tmp=($session->get('tempCategorie'))?$session->get('tempCategorie'):null;
        
        return $this->render('client/shop.html.twig',[
            'pagetitle'=>'shop',
            'page' => $page,
            "categorie"=>$tmp,
            'products' => $produits,
            ]);      
    }

      /**
     * @Route("/pharmacie", name="HandleSearchProp")
     */
    public function HandleSearchProp(Request $request,PaginatorInterface $paginator, SessionInterface $session)
    {       
        $frm=$request->request->get('form');
        
        if(!$frm)
           {
            $pharmacies=$session->get('temp',[]);
           }
           else{
        $pharmacies=$this->getDoctrine()->getRepository(Proprietaire::class)->search($frm['nom'], $frm['ville']);
        $session->set('temp',$pharmacies);
    }
        $page = $paginator->paginate(
            $pharmacies,
            $request->query->getInt('page', 1),
            9
        );
        return $this->render('client/list-pharmacie.html.twig',[
            'pagetitle'=>'pharmacie',
            'pharmacies' => $pharmacies,
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
        return $this->render('client/detailsprod.html.twig', ['pagetitle'=>'details produit','produit'=>$produits[0]]);
    
    }
    
   
     /**
     * @Route("/infosprod/{id}", name="infosprod")
     */
    public function infosprod($id)
    {
        /*$doctrine = $this->getDoctrine();
        $repository = $doctrine->getRepository(Produit::class);
        $produit=$repository->findBy( ['id'=>$id])[0];
        $data=array('nom'=>$produit->getNom(),'image'=>$produit->getImage(),'prix'=>$produit->getPrixTva(),'prop_id'=>$produit->getProprietaire()->getId(),'stock'=>$produit->getQuantite());
        *
        
        return $this->json($data,200);*/
        //return $this->render('client/detailsprod.html.twig', ['pagetitle'=>'details produit','produit'=>$produits[0]]);
    
    }
     
      /**
     * @Route("/Allcategories", name="Allcategories")
     */
    
    public function Allcategories()
    { 
        $repository = $this->getDoctrine()->getRepository(Categorie::class);
        $categories=$repository->findAll();
        return $this->render('client/allCategories.html.twig',['categories'=>$categories,'pagetitle'=>'Les categories']); 
        
    }
    /**
     * @Route("profilepharma/{id}", name="profile")
     */
    public function profile($id, Request $request, PaginatorInterface $paginator) {
        $proprietaire = $this->getDoctrine()->getRepository(Proprietaire::class)->findBy(['id'=>$id]);
        $produits= $this->getDoctrine()->getRepository(Produit::class)->createQueryBuilder('p')
        ->select('p')
        ->where('p.proprietaire = :prop')
        ->setParameter('prop', $id)
        ->getQuery()
        ->getResult();
        dump($produits);
        $page = $paginator->paginate(
            $produits,
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('client/detailspharmacie.html.twig',[
            'pagetitle'=>'profile',
            'proprietaire'=>$proprietaire[0],
            'produits' =>$produits, 
            'page'=>$page         
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
            8
        );
        return $this->render('client/shop.html.twig',[
            'pagetitle'=>'shop',
            'categorie'=>$name,
            'page' => $page
            ]);

       
    }
      /**
     * @Route("/validateOrder", name="validateOrder")
     */
    public function validateOrder(Request $request){
        //////
        $response=array();
        $client=$this->getUser();
        $resp=json_decode($request->getContent());
        $commande=$resp->infos[0]->commande;
        $details_commande=$resp->infos[1]->details;
        $newcommande=new commande();
        for($i=0;$i<count($details_commande)-1;$i++)
        {   $product=$this->getDoctrine()->getManager()->getRepository(Produit::class)->findOneBy(['id' =>$details_commande[$i]->prod_id ]);
            if($product->getQuantite()==0){
                $response[]=$product->getNom();
                continue;}
            $details=new DetailsCommande();
            //$details->setDateCommande(new \DateTime('now'));
            $details->setQuantite(min($details_commande[$i]->qt,$product->getQuantite()));
            $details->setProduit($product);
            $newcommande->addProduit($details);
        
        }
        $newcommande->setVille($commande[3]->ville);
        $newcommande->setPayment('livraison');
        $newcommande->setCodePostal('75000');
        $newcommande->setAdresseLivraison($commande[2]->address);
        $newcommande->setClient($client->getClient());
        $newcommande->setDate(new \DateTime('now'));
        $newcommande->setNom($commande[1]->lname);
        $newcommande->setPrenom($commande[0]->fname);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($newcommande);
        $manager->flush();
        return $this->json(1);


       // die();


        
    }
      /**
     * @Route("/ClientAccount", name="ClientAccount")
     */
    public function compte(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {       
        $em = $this->getDoctrine()->getManager();
        $client = $this->getUser();
       
        $formInfoPerso = $this->createForm(ClientChangeInfoPersoType::class);
        $formInfoPerso->handleRequest($request);
        $formPassword = $this->createForm(ClientChangePasswordType::class);
        $formPassword->handleRequest($request);
        $active_tab1 = "active show";
        $active_tab2 = "";
        if($formPassword->isSubmitted() && $formPassword->isValid()){
             $oldpassword = $request->request->get('client_change_password')['oldpassword'];
            $newpassword = $request->request->get('client_change_password')['confirmpassword']['first'];
            

            $active_tab = $_POST['active_tab'];
            $active_tab1 = ($active_tab ==="tab_1") ? "active show" : "";
            $active_tab2 = ($active_tab ==="tab_2") ? "active show" : "";
            $active_tab3 = ($active_tab ==="tab_3") ? "active show" : "";
            // Si l'ancien mot de passe est bon
            if($passwordEncoder->isPasswordValid($client, $oldpassword)){
                if(!$passwordEncoder->isPasswordValid($client, $newpassword)){
                  
                $newEncodedPassword = $passwordEncoder->encodePassword($client, $newpassword);
                $client->setPassword($newEncodedPassword);
                $em->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été changé !');
                }
                else{
                    $formPassword->get('confirmpassword')['first']->addError(new FormError('Nouveau mot de passe me peux pas etre le meme que l`ancien'));
                }
            }
            else {
                $formPassword->get('oldpassword')->addError(new FormError('Ancien mot de passe incorrect'));
            }
        }
        elseif ($formPassword->isSubmitted()) {
            $active_tab = $_POST['active_tab'];
            $active_tab1 = ($active_tab ==="tab_1") ? "active show" : "";
            $active_tab2 = ($active_tab ==="tab_2") ? "active show" : "";
            $active_tab3 = ($active_tab ==="tab_3") ? "active show" : "";
        }
       
        if($formInfoPerso->isSubmitted() && $formInfoPerso->isValid()){
            $newTel= $request->request->get('client_change_info_perso')['telephone']; 
            if(!preg_match("/(0[5|6|7])[0-9]{8}/",$newTel) || strlen(strval($newTel))!=10)
            {    
              $formInfoPerso->get('telephone')->addError(new FormError('Numero invalid'));
             }
             else{
            $newnom = $request->request->get('client_change_info_perso')['nom'];
            $newprenom = $request->request->get('client_change_info_perso')['prenom'];
            $newVille= $request->request->get('client_change_info_perso')['ville'];
            $newRegion= $request->request->get('client_change_info_perso')['region'];
            $client->setNom($newnom);
            $client->setPrenom($newprenom);
            $client=$client->getClient();
            $client->setTel($newTel);
            $client->setRegion($newRegion);
            $client->setVille($newVille);
            $em->flush();
            $this->addFlash('success', 'Vos infos sont bien modifiés!');
            return $this->redirectToRoute('ClientAccount'); 
             }     
        }
    
        return $this->render('client/Compte.html.twig',[
            'pagetitle'=>'Compte',
            'path'=>'ClientAccount',
            'formPassword'=>$formPassword->createView(),
            'formInfoPerso'=>$formInfoPerso->createView(),
            'active_tab1' => $active_tab1,
            'active_tab2' => $active_tab2,
            //'prenom'=>$repos->getPrenom()
        ]);
    }

    /**
     * @Route("/commande", name="client_commande")
     */
    public function commande(PaginatorInterface $paginator, Request $request)
    {
        $commandes = $this->getDoctrine()->getRepository(Commande::class)->findCmdClient($this->getuser());
        $i=1;
        foreach($commandes as $k=>$commande)
        {
            $commandes[$k]->nb=$i++;
        }

        $prixTotal = $this->getDoctrine()->getRepository(DetailsCommande::class)->findPrixTotalClient($this->getuser());
        
        $page = $paginator->paginate(
            $commandes,
            $request->query->getInt('page', 1),
            3
        );
        return $this->render('client/commande.html.twig',[
            'page'=> $page,
            'pagetitle'=>'Commande',
            'path'=>'home_proprietaire',   
            'prixTotal' => $prixTotal,      
        ]);
    }
}

