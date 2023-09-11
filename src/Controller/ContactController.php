<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {

        $form = $this->createForm(ContactType::class);

        if($this->getUser()){
            $form->remove('email');
        }

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $subject = $form->get('subject')->getData();

            if($this->getUser()){
                $senderEmail = $this->getUser()->getEmail();
            }
            else {
                $senderEmail = $form->get('email')->getData();
            }

            $message = $form->get('message')->getData();

            $email = (new Email())
            ->from($senderEmail)
            ->to('admin@barberhub.com')
            ->subject($subject)
            ->html($message);

            $mailer->send($email);

            notyf()
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addSuccess('Merci ! Votre message a été envoyé.');

            return $this->redirectToRoute("app_home");
        }
        
        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView()
        ]);
    }
}
