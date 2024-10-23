<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\UserType;
use App\Form\UserEditType;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\FOGGmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityController extends FOGController
{
    #[Route("/login", name: "login", methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    { 
        /*if ($authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute("index");
        }*/

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('oeilglauque/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error
        ]);
    }

    #[Route("/register", name: "register", methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        // 1 : Construction du formulaire
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2 : Gestion du submit (POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 3) Vérification de l'adresse mail et du pseudo
            if(count($userRepository->findBy(["email" => $user->getEmail()])) > 0) {
                $this->addFlash('danger', "Cette adresse mail est déjà utilisée, veuillez en choisir une autre !"); 
                return $this->redirectToRoute('register');
            }
            if(count($userRepository->findBy(["pseudo" => $user->getPseudo()])) > 0) {
                $this->addFlash('danger', "Ce pseudo est déjà utilisé, veuillez en choisir un autre !");
                return $this->redirectToRoute('register');
            }

            // 4) Encodage du mot de passe
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            // 5) Sauvegarde en base
            if($this->getParameter('allow_registration')) {
                //$entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }

            // ... do any other work - like sending them an email, etc
            $this->addFlash('success', "Votre inscription a réussi, vous pouvez dès maintenant vous connecter !");

            return $this->redirectToRoute('index');
        }
        
        return $this->renderForm(
            'oeilglauque/register.html.twig',
            [
                'form' => $form,
                'allow_registration' => $this->getParameter('allow_registration'), 
            ]
        );
    }

    #[Route("/user", name: "ucp", methods: ['GET', 'POST'])]
    public function ucp(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // 1 : Construction du formulaire
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $correct = true;

        $form = $this->createForm(UserEditType::class, null, [
            'user' => $user
        ]);
        
        /*createFormBuilder()
            ->add('email', EmailType::class, [
                'label' => 'Adresse mail', 
                'invalid_message' => 'Veuillez fournir une adresse mail valide',
                'constraints' => [new Assert\NotBlank()],
                'data' => $user->getEmail()
            ])
            ->add('name', TextType::class, ['label' => 'Nom', 'data' => $user->getName(), 'constraints' => [new Assert\NotBlank()]])
            ->add('firstname', TextType::class, ['label' => 'Prénom', 'data' => $user->getFirstName(), 'constraints' => [new Assert\NotBlank()]])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'help' => 'Laissez vide pour ne pas modifier', 
                'first_options'  => ['label' => 'Nouveau mot de passe (laissez vide pour ne pas modifier)'],
                'second_options' => ['label' => 'Nouveau mot de passe (confirmation)'],
                'required' => false, 
            ])
            ->add('save', SubmitType::class, ['label' => 'Mise à jour'])
            ->getForm();
            */


        // 2 : Gestion du submit (POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // 3) Vérification de l'adresse mail
            $sameAddr = $userRepository->findOneBy(["email" => $data['email']]);
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
                $entityManager->flush();
                $this->addFlash('success', "Votre profil a bien été modifié !");
                return $this->redirectToRoute('index');
            }
        }
        
        return $this->render(
            'oeilglauque/ucp.html.twig',
            [
                'form' => $form->createView(), 
                'pseudo' => $user->getPseudo()
            ]
        );
    }

    #[Route("/forgotpwd", name: "forgotPwd", methods: ['GET', 'POST'])]
    public function forgotPwd(Request $request, FOGGmail $mailer, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu');
            } else {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->flush();

                $url = $this->generateUrl('resetPwd', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $mailer->sendTemplatedEmail(new Address($email,$user->getPseudo()), 'Demande de réinitialisation de mot de passe', 'oeilglauque/emails/resetPwdRequest.html.twig', ['user' => $user, 'url' => $url]);

                $this->addFlash('success', 'Mail envoyé, vérifiez votre boite mail.');
                return $this->redirectToRoute('index');
            }
        }

        /*if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu');
            } else {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->flush();

                $url = $this->generateUrl('resetPwd', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
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
        }*/

        return $this->render('oeilglauque/form/forgotPwd.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    #[Route("/resetpwd/{token}", name: "resetPwd", methods: ['GET', 'POST'])]
    public function resetPwd(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher, ?string $token = null): Response
    {
        $session = $request->getSession();

        if ($token) {
            $session->set('reset_token',$token);

            return $this->redirectToRoute('resetPwd');
        }

        $token = $session->get('reset_token');
        if ($token === null) {
            $this->addFlash('danger','Token invalide.');
            return $this->redirectToRoute('forgotPwd');
        }

        $user = $manager->getRepository(User::class)->findOneByResetToken($token);
        if ($user === null) {
            $this->addFlash('danger', 'Token inconnu.');
            return $this->redirectToRoute('forgotPwd');
        }

        $user->setResetToken(null);

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pwd = $form->get('plainPassword')->getData();

            $hashpwd = $passwordHasher->hashPassword($user, $pwd);
            $user->setPassword($hashpwd);
            $manager->flush();

            $session->clear();

            $this->addFlash('succes', 'Mot de passe mis à jour, vous pouvez vous connecter !');
            return $this->redirectToRoute('index');
        }

        /*if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);

            if ($user === null) {
                $this->addFlash('danger', 'Token Inconnu');
                return $this->redirectToRoute('index');
            }

            $user->setResetToken(null);
            $password = $passwordHasher->encodePassword($user, $request->request->get('password'));
            $user->setPassword($password);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour, vous pouvez vous connecter !');

            return $this->redirectToRoute('index');
        }*/

        return $this->render(
            'oeilglauque/form/resetPwd.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(){}
}