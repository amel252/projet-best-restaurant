<?php

namespace App\Controller;

use App\Entity\User;
use App\Classe\Mail;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
    ) {
    }
    
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, Mail $mail): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            return $this->processSendingPasswordResetEmail($email,$mail);
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form,
        ]);
    }

    /* Confir page apres que l'utilisateur demande reset PWD*/
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        /*Génerer faux Token */
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }
        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /* Valider le process de reset quand user clique sur le lien mailjet */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, ?string $token = null): Response
    {
        if ($token) {
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('app_reset_password');
        }
        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('Aucun lien de réinitialisation de mot de passe n’a été trouvé dans l’URL ou dans la session.');
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash(
                'reset_password_error',
                'Le lien de réinitialisation est invalide ou a expiré. Veuillez faire une nouvelle demande.');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        // si token est valid; l'utilisateur est autorisé a changer MDP
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token est utilisé qu'une fois .
            $this->resetPasswordHelper->removeResetRequest($token);

            $plainPassword = $form->get('plainPassword')->getData();
            // Encode(hash)le plain password, et mis a jour.
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $this->entityManager->flush();
            // la session est netoyé apres le changement MDP 
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form,
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData,Mail $mail): RedirectResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
            
        ]);
    
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }
        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->redirectToRoute('app_check_email');
        }

        $resetUrl = $this->generateUrl(
            'app_reset_password',
            ['token' => $resetToken->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $result= $mail->send(
            (string) $user->getEmail(),
            (string) $user->getFirstName(),
            'Réinitialisation de votre mot de passe - Vite & Gourmand',
            [
                'reset_url' => $resetUrl,
                'firstName'=> $user->getFirstName(),
            ],
            8544229
        );
        // Afficher le résultat de l'envoi
        $this->setTokenObjectInSession($resetToken);
        return $this->redirectToRoute('app_check_email');
    }
}
