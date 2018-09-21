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

class SecurityController extends CustomController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils, AuthorizationCheckerInterface $authChecker) { 
        if ($authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute("index");
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('oeilglauque/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'dates' => $this->getCurrentEdition()->getDates(), 
        ));
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        // 1 : Construction du formulaire
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2 : Gestion du submit (POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Vérification de l'adresse mail et du pseudo
            if(count($this->getDoctrine()->getRepository(User::class)->findBy(["email" => $user->getEmail()])) > 0) {
                $this->addFlash('danger', "Cette adresse mail est déjà utilisée, veuillez en choisir une autre !"); 
                return $this->redirectToRoute('register');
            }
            if(count($this->getDoctrine()->getRepository(User::class)->findBy(["pseudo" => $user->getPseudo()])) > 0) {
                $this->addFlash('danger', "Ce pseudo est déjà utilisé, veuillez en choisir un autre !");
                return $this->redirectToRoute('register');
            }

            // 4) Encodage du mot de passe
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 5) Sauvegarde en base
            if($this->getParameter('allow_registration')) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }

            // ... do any other work - like sending them an email, etc
            $this->addFlash('success', "Votre inscription a réussi, vous pouvez dès maintenant vous connecter !");

            return $this->redirectToRoute('index');
        }
        
        return $this->render(
            'oeilglauque/register.html.twig',
            array(
                'form' => $form->createView(), 
                'dates' => $this->getCurrentEdition()->getDates(), 
                'allow_registration' => $this->getParameter('allow_registration'), 
            )
        );
    }
}
?>
