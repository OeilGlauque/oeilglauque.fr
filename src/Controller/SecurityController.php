<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Form\UserType;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils, AuthorizationCheckerInterface $authChecker) {
        /*
        if ($authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute("index");
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        */
        return $this->render('oeilglauque/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'dates' => "Du 19 au 21 octobre", 
        ));
    }

    /**
     * @Route("/register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        /*
        // 1 : Construction du formulaire
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2 : Gestion du submit (POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encodage du mot de passe
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 4) Sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('index');
        }
        */
        return $this->render(
            'oeilglauque/register.html.twig',
            array('dates' => "Du 19 au 21 octobre")
        );
    }
}
?>