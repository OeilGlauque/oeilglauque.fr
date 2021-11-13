<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Form\UserType;
use App\Form\UserEditType;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityController extends FOGController
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
        return $this->renderPage('oeilglauque/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error
        ));
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher) {
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
            $password = $passwordHasher->hashPassword($user, $user->getPlainPassword());
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
        
        return $this->renderPage(
            'oeilglauque/register.html.twig',
            array(
                'form' => $form->createView(),
                'allow_registration' => $this->getParameter('allow_registration'), 
            )
        );
    }

    /**
     * @Route("/user", name="ucp")
     */
    public function ucp(Request $request, UserPasswordHasherInterface $passwordHasher) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // 1 : Construction du formulaire
        $user = $this->getUser();
        $correct = true;

        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, array(
                'label' => 'Adresse mail', 
                'invalid_message' => 'Veuillez fournir une adresse mail valide',
                'constraints' => new NotBlank(),
                'data' => $user->getEmail()
            ))
            ->add('name', TextType::class, array('label' => 'Nom', 'data' => $user->getName(), 'constraints' => new NotBlank()))
            ->add('firstname', TextType::class, array('label' => 'Prénom', 'data' => $user->getFirstName(), 'constraints' => new NotBlank()))
            ->add('newPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'help' => 'Laissez vide pour ne pas modifier', 
                'first_options'  => array('label' => 'Nouveau mot de passe (laissez vide pour ne pas modifier)'),
                'second_options' => array('label' => 'Nouveau mot de passe (confirmation)'),
                'required' => false, 
            ))
            ->add('save', SubmitType::class, array('label' => 'Mise à jour'))
            ->getForm();


        // 2 : Gestion du submit (POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // 3) Vérification de l'adresse mail
            $sameAddr = $this->getDoctrine()->getRepository(User::class)->findOneBy(["email" => $data['email']]);
            if($sameAddr != null && $sameAddr != $user) {
                $this->addFlash('danger', "Cette adresse mail est déjà utilisée, veuillez en choisir une autre !"); 
                $correct = false;
            }

            // 4) Encodage du mot de passe
            $password = $user->getPassword();
            if($data['newPassword'] != "") {
                $password = $passwordHasher->hashPassword($user, $data['newPassword']);
            }

            // 5) Sauvegarde en base
            if($correct) {
                $user->setEmail($data["email"]);
                $user->setName($data["name"]);
                $user->setFirstName($data["firstname"]);
                $user->setPassword($password);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                $this->addFlash('success', "Votre profil a bien été modifié !");
                return $this->redirectToRoute('index');
            }
        }
        
        return $this->renderPage(
            'oeilglauque/ucp.html.twig',
            array(
                'form' => $form->createView(), 
                'pseudo' => $user->getPseudo()
            )
        );
    }

    /**
     * @Route("/forgotpwd", name="forgotPwd")
     */
    public function forgotPwd(Request $request, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator) {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu');
            } else {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->flush();

                $url = $this->generateUrl('resetPwd', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
                
                $message = (new \Swift_Message('Demande de réinitialisation de mot de passe'))
                ->setFrom([$_ENV['MAILER_ADDRESS'] => 'L\'équipe du FOG'])
                ->setTo([$user->getEmail() => $user->getPseudo()])
                ->setBody(
                    $this->renderView(
                        'oeilglauque/emails/resetPwdRequest.html.twig',
                        ['user' => $user,
                        'url' => $url]
                    ),
                    'text/html'
                );
                $mailer->send($message);

                $this->addFlash('success', 'Mail envoyé, vérifiez votre boite mail.');
                return $this->redirectToRoute('index');
            }
        }

        return $this->renderPage('oeilglauque/forgotPwd.html.twig');
    }
    
    /**
     * @Route("/resetpwd/{token}", name="resetPwd")
     */
    public function resetPwd(Request $request, string $token, UserPasswordHasherInterface $passwordHasher) {

        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);

            if ($user === null) {
                $this->addFlash('danger', 'Token Inconnu');
                return $this->redirectToRoute('index');
            }

            $user->setResetToken(null);
            $password = $passwordHasher->hashPassword($user, $request->request->get('password'));
            $user->setPassword($password);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour, vous pouvez vous connecter !');

            return $this->redirectToRoute('index');
        }

        return $this->renderPage(
            'oeilglauque/resetPwd.html.twig',
            array(
                'token' => $token
            )
        );
    }
}
?>
