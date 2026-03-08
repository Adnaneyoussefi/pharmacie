<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Proprietaire;
use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        // ── 1. USER ──────────────────────────────────────────────────────────
        $user = new User();
        $user->setEmail('pharmacie.soleil@example.com');
        $user->setNom('Bennani');
        $user->setPrenom('Youssef');
        $user->setRoles(['ROLE_PROPRIETAIRE']);
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, 'password123')
        );
        $user->setRegistredAt(new \DateTime());
        $user->setIsActive(true);
        $user->setStatut('actif');
        $manager->persist($user);

        // ── 2. PROPRIETAIRE ──────────────────────────────────────────────────
        $proprietaire = new Proprietaire();
        $proprietaire->setNomPharmacie('Pharmacie du Soleil');
        $proprietaire->setVille('Casablanca');
        $proprietaire->setAdresse('12 Rue Hassan II, Maarif');
        $proprietaire->setTel('0661234567');
        $proprietaire->setRegion('Casablanca-Settat');
        $proprietaire->setUser($user);
        $manager->persist($proprietaire);

        // ── 3. CATEGORIES ────────────────────────────────────────────────────
        $categoriesData = [
            'Soins du visage',
            'Soins du corps',
            'Compléments alimentaires',
            'Hygiène & Beauté',
        ];

        $categories = [];
        foreach ($categoriesData as $nom) {
            $cat = new Categorie();
            $cat->setNom($nom);
            $manager->persist($cat);
            $categories[$nom] = $cat;
        }

        // ── 4. PRODUITS PARAPHARMACIE ────────────────────────────────────────
        $produits = [
            [
                'nom'             => 'Crème Hydratante Visage SPF 30',
                'description'     => 'Crème de jour légère offrant hydratation intense et protection solaire SPF 30. Convient aux peaux mixtes à normales.',
                'prix_ht'         => 89.90,
                'prix_tva'        => 99.00,
                'quantite'        => 50,
                'date_expiration' => new \DateTime('2027-06-01'),
                'categorie'       => 'Soins du visage',
                'image'           => 'creme_visage.png',
            ],
            [
                'nom'             => 'Sérum Anti-Âge à la Vitamine C',
                'description'     => 'Sérum concentré à 15 % de vitamine C pure pour illuminer le teint et réduire les rides. Usage matin et soir.',
                'prix_ht'         => 149.00,
                'prix_tva'        => 165.00,
                'quantite'        => 30,
                'date_expiration' => new \DateTime('2026-12-01'),
                'categorie'       => 'Soins du visage',
                'image'           => 'serum_vitaminec.jpg',
            ],
            [
                'nom'             => 'Lait Corps Karité & Aloe Vera',
                'description'     => 'Lait corps nourrissant enrichi en beurre de karité et gel d\'aloe vera. Peau douce et veloutée en 24 h.',
                'prix_ht'         => 59.50,
                'prix_tva'        => 65.00,
                'quantite'        => 80,
                'date_expiration' => new \DateTime('2027-03-15'),
                'categorie'       => 'Soins du corps',
                'image'           => 'lait_corps.jpg',
            ],
            [
                'nom'             => 'Huile Sèche Corps & Cheveux Argan',
                'description'     => 'Huile sèche multi-usage à base d\'huile d\'argan du Maroc. Nourrit, protège et fait briller la peau et les cheveux.',
                'prix_ht'         => 79.00,
                'prix_tva'        => 87.00,
                'quantite'        => 40,
                'date_expiration' => new \DateTime('2026-09-30'),
                'categorie'       => 'Soins du corps',
                'image'           => 'huile_argan.jpg',
            ],
            [
                'nom'             => 'Magnésium Marin 300 mg – 60 comprimés',
                'description'     => 'Complément alimentaire à base de magnésium marin hautement biodisponible. Réduit la fatigue et soutient le système nerveux.',
                'prix_ht'         => 69.00,
                'prix_tva'        => 76.00,
                'quantite'        => 100,
                'date_expiration' => new \DateTime('2026-07-01'),
                'categorie'       => 'Compléments alimentaires',
                'image'           => 'magnesium.jpg',
            ],
            [
                'nom'             => 'Vitamine D3 1000 UI – 90 gélules',
                'description'     => 'Vitamine D3 d\'origine naturelle pour soutenir l\'immunité, la solidité osseuse et l\'humeur.',
                'prix_ht'         => 55.00,
                'prix_tva'        => 60.00,
                'quantite'        => 120,
                'date_expiration' => new \DateTime('2027-01-01'),
                'categorie'       => 'Compléments alimentaires',
                'image'           => 'vitamine_d3.jpg',
            ],
            [
                'nom'             => 'Gel Douche Surgras Avoine & Miel',
                'description'     => 'Gel douche doux et surgras formulé pour les peaux sensibles et sèches. Sans savon ni paraben.',
                'prix_ht'         => 35.00,
                'prix_tva'        => 38.50,
                'quantite'        => 150,
                'date_expiration' => new \DateTime('2027-08-01'),
                'categorie'       => 'Hygiène & Beauté',
                'image'           => 'gel_douche.jpg',
            ],
            [
                'nom'             => 'Dentifrice Blancheur Charbon Actif',
                'description'     => 'Dentifrice au charbon végétal activé pour un blanchissement naturel des dents sans abrasion excessive.',
                'prix_ht'         => 42.00,
                'prix_tva'        => 46.00,
                'quantite'        => 90,
                'date_expiration' => new \DateTime('2026-11-01'),
                'categorie'       => 'Hygiène & Beauté',
                'image'           => 'dentifrice_charbon.jpg',
            ],
        ];

        $now = new \DateTime();

        foreach ($produits as $data) {
            $produit = new Produit();
            $produit->setNom($data['nom']);
            $produit->setDescription($data['description']);
            $produit->setPrixHt($data['prix_ht']);
            $produit->setPrixTva($data['prix_tva']);
            $produit->setQuantite($data['quantite']);
            $produit->setDateExpiration($data['date_expiration']);
            $produit->setCategorie($categories[$data['categorie']]);
            $produit->setProprietaire($proprietaire);
            $produit->setImage($data['image']);
            $produit->setCreatedAt($now);
            $produit->setUpdatedAt($now);
            $manager->persist($produit);
        }

        $manager->flush();
    }
}
