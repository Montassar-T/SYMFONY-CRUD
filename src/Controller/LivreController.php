<?php
namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted; // Import this class

class LivreController extends AbstractController
{
    /* Fonction de récupération de tous les livres */
    #[Route("/", name: "app_livres")]
    #[IsGranted('IS_AUTHENTICATED_FULLY')] // Protect this route
    public function livres(LivreRepository $livreRepository): Response
    {
        $livres = $livreRepository->findAll();
        return $this->render('livre/listesLivres.html.twig', array(
            'livres' => $livres,
        ));
    }

    /* Fonction d'ajout d'un livre */
    #[Route('/ajouter', name: 'app_ajouter_livre')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')] // Protect this route
    public function ajouterLivre(Request $request, EntityManagerInterface $em): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($livre);
            $em->flush();
            return $this->redirectToRoute('app_livres');
        }

        return $this->render('livre/ajouterLivre.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /* Fonction de modification d'un livre */
    #[Route('/modifier/{id}', name: 'app_modifier_livre')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')] // Protect this route
    public function modifierLivre(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $livre = $em->getRepository(Livre::class)->find($id);

        if (!$livre) {
            throw $this->createNotFoundException('Livre not found');
        }

        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_livres');
        }

        return $this->render('livre/modifierLivre.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/supprimer/{id<\d+>}", name: "app_supprimer_livre", methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')] // Protect this route
    public function supprimerLivre(int $id, EntityManagerInterface $em): Response
    {
        $livre = $em->getRepository(Livre::class)->find($id);

        if (!$livre) {
            throw $this->createNotFoundException('Livre not found');
        }

        $em->remove($livre);
        $em->flush();
        $this->addFlash('success', 'Livre deleted successfully.');

        return $this->redirectToRoute('app_livres');
    }
}
