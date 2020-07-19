<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class SecurityController extends AbstractController
{

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json([
            'name' => $user->getUsername()
        ]);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $user = $userRepository->findOneBy([
            'username'=>$request->get('username'),
        ]);
        if (!$user || !$encoder->isPasswordValid($user, $request->get('password'))) {
            return $this->json([
                'message' => 'Username or password is wrong.',
            ]);
        }
        $payload = [
            "user" => $user->getUsername(),
            "exp"  => (new \DateTime())->modify("+1 minutes")->getTimestamp(),
        ];

        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), 'HS256');

        return $this->json([
            'message' => 'success!',
            'token' => sprintf('Bearer %s', $jwt),
        ]);
    }

    /**
     * @Route("/auth", name="auth", methods={"GET"})
     */
    public function auth()
    {
        return $this->render('security/auth.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response
    {
        // controller can be blank: it will never be executed!
    }


}
