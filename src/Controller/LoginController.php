<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function login(Request $request, HttpClientInterface $client, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $identifiant = $request->request->get('identifiant');
            $password = $request->request->get('password');

            try {
                $response = $client->request('POST', $this->getParameter('api_base_url') . '/api/login', [
                    'json' => [
                        'identifiant' => $identifiant,
                        'password' => $password,
                    ],
                ]);

                $data = $response->toArray();
                $session->set('jwt_token', $data['token']);

                return $this->redirectToRoute('client_index'); // page liste clients
            } catch (\Exception $e) {
                return $this->render('login.html.twig', [
                    'error' => 'Identifiants invalides.',
                ]);
            }
        }

        return $this->render('login.html.twig');
    }
}
