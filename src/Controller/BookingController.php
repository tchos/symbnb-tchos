<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * Page qui affichera le formulaire de création d'une réservation
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);

        // handlerequest() permet de parcourir la requête et d'extraire les informations du formulaire
        $form->handleRequest($request);

        // vérification et validation du formulaire avant d'enregistrer les données du formulaire dans la BD
        if($form->isSubmitted() && $form->isValid())
        {
            $user = $this->getUser();
            $booking->setBooker($user)
                    ->setAd($ad);

            // Si les dates ne sont disponibles alors message d'erreur
            if(!$booking->isBookableDates())
            {
                $this->addFlash(
                    'warning',
                    "Les dates que vous avez choisies ne peuvent être réservées : elles sont déjà prises"
                );
            } else {
                // Sinon enregistrement et redirection
                $manager->persist($booking);
                $manager->flush();

                // redirection vers la page de succès
                return $this->redirectToRoute('booking_show', ['id' => $booking->getId(), 'withAlert' => true]);
            }
            
            
        }
        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher la page d'une réservation
     * @Route("/booking/{id}", name="booking_show")
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function show(Booking $booking, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', 'Votre commentaire a bien été pris en compte !');
        }

        return $this->render('booking/show.html.twig', [ 
            'booking' => $booking,
            'form'    => $form->createView()
        ]);
    }
}
