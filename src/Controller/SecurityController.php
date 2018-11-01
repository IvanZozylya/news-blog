<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) обработать отправку
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Зашифровать пароль
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // 4) сохранить Пользователя!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... сделайть любую другую работу - вроде отправки письма и др
            // может, установитить "флеш" сообщение об успешном выполнении для пользователя

            return $this->redirectToRoute('login');
        }

        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/login",name="login")
     */
    public function login(AuthenticationUtils $helper)
    {
        $error = $helper->getLastAuthenticationError();
        $lastUsername = $helper->getLastUsername();

        return $this->render('security/login.html.twig', [
            // last username entered by the user (if any)
            'last_username' => $helper->getLastUsername(),
            // last authentication error (if any)
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout",name="logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
