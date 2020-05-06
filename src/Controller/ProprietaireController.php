<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Proprietaire;
use App\Form\RegistrationProType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/proprietaire")
 */

class ProprietaireController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $role[] = 'ROLE_PROP';
            $user->setRoles($role);
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('proprietaire/inscription.html.twig',[
            'pagetitle'=>'Inscription',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/", name="home_proprietaire")
     */
    public function home()
    {
        return $this->render('proprietaire/acceuil.html.twig',[
            'pagetitle'=>'Home',
            'path'=>'home_proprietaire',
        ]);
    }

     /**
     * @Route("/compte", name="compte_proprietaire")
     */
    public function compte(/*AuthenticationUtils $authenticationUtils*/)
    {
        //$repos = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $authenticationUtils->getLastUsername()]);
        return $this->render('proprietaire/compte.html.twig',[
            'pagetitle'=>'Compte',
            'path'=>'compte_proprietaire',
            //'prenom'=>$repos->getPrenom()
        ]);
    }

     /**
     * @Route("/stock", name="stock_proprietaire")
     */
    public function stock()
    {
        return $this->render('proprietaire/stock.html.twig',[
            'pagetitle'=>'Stock',
            'path'=>'stock_proprietaire',
        ]);
    }
}
