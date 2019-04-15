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

    /**
     * @Route("/user", name="ucp")
     */
    public function ucp(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
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
                $password = $passwordEncoder->encodePassword($user, $data['newPassword']);
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
        
        return $this->render(
            'oeilglauque/ucp.html.twig',
            array(
                'form' => $form->createView(), 
                'pseudo' => $user->getPseudo(), 
                'dates' => $this->getCurrentEdition()->getDates(), 
            )
        );
    }

    /**
     * Contrôle la page d'oubli de mot de passe
     * @Route("/forgotPassword", name="forgotPassword")
     */

    public function forgotPassword(Request $request, \Swift_Mailer $mailer){


        $form = $this->createFormBuilder(null, [
            'action' => '/forgotPassword',
            'method' => 'POST',
        ])
            ->add('mail', EmailType::class, [
                'label' => "Adresse mail",
            ])
            ->getForm();

        // Gestion du submit (POST)
        $form->handleRequest($request);
        $mail = $form->getData()['mail'];

        if($form->isSubmitted() && $form->isValid()){
            if(count($this->getDoctrine()->getRepository(User::class)->findBy(["email" => $mail])) == 0) {
                $this->addFlash('danger', "Cette adresse mail n'est pas utilisée. Veuillez choisir une adresse mail valide !");
                return $this->redirectToRoute('forgotPassword');
            }

            // On ajoute le token de reinitialisation à la base
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(["email" => $mail]);
            $pseudo = $user->getPseudo();
            $tok = $this->getToken($mail);
            $user->setForgottenPassword($tok);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            // On envoie le mail
            $message = (new \Swift_Message('Réinitialisation de votre mot de passe'))
                ->setFrom('noreply@test.kon')
                ->setTo($mail)
                ->setBody(
                    $this->renderView(
                        'oeilglauque/email/passwordResetMail.html.twig',
                        [
                            'pseudo' => $pseudo,
                            'url' => 'http://127.0.0.1:8000/passwordReset?tokid=' . $tok,
                            'tok' => $tok
                        ]
                    ),
                    'text/html'
                );
            $mailer->send($message);
            return $this->render('oeilglauque/forgotPassword.html.twig',
                [
                    'mailsent' => true
                ]);
        }

        return $this->render(
            'oeilglauque/forgotPassword.html.twig',
            [
                'form' =>  $form->createView(),
                'mail' => $mail,
                'mailsent' => false
            ]);
    }


    /**
     * Contrôle la page de réinitialisation du mot de passe
     * @Route("/passwordReset", name="passwordReset")
     */
    public function passwordReset(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        // Récupération du token de reinitialisation et recherche de l'utilisateur correspondant
        $tok = $request->query->get('tokid');
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(["forgottenPassword" => $tok]);

        if($user === null || $tok == "") {
            return $this->render('oeilglauque/passwordReset.html.twig',
                [
                    'valid' => false
                ]
            );
        }

        $form = $this->createFormBuilder(null, [
            'method' => 'POST',
        ])
            ->add('newPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'first_options'  => array('label' => 'Nouveau mot de passe'),
                'second_options' => array('label' => 'Nouveau mot de passe (confirmation)'),
                'required' => true,
            ))
            ->add('save', SubmitType::class, array('label' => 'Enregistrer le nouveau mot de passe'))
            ->getForm();

        // Gestion du submit (POST)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encodage du mot de passe
            $password = $passwordEncoder->encodePassword($user, $form->getData()['newPassword']);

            // Mise a jour du mot de passe, suppression du token de reinitialisation
            $user->setPassword($password);
            $user->setForgottenPassword(null);

            // Mise à jour de la base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', "Votre profil a bien été modifié !");

            return $this->redirectToRoute('index');
        }

        $pseudo = $user->getPseudo();
        return $this->render('oeilglauque/passwordReset.html.twig',
            [
                'form' => $form->createView(),
                'pseudo' => $pseudo,
                'valid' => true
            ]
        );
    }

    private function getToken($email){
        $time = time();
        $tok = $time . '' . $email;
        $tok = hash('sha256',$tok);
        return $tok;
    }
}
?>
