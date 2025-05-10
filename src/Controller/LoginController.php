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
        $error = null;

        if ($request->isMethod('POST')) {
            $identifiant = $request->request->get('identifiant');
            $password = $request->request->get('password');

            try {
                $apiUrl = $this->getParameter('api_base_url') . '/api/login';
                $response = $client->request('POST', $apiUrl, [
                    'json' => [
                        'identifiant' => $identifiant,
                        'password' => $password,
                    ],
                ]);

                // Si la réponse est 200, tout va bien
                if ($response->getStatusCode() === 200) {
                    $data = $response->toArray();
                    $session->set('jwt_token', $data['token']);
                    return $this->redirectToRoute('dashboard'); // ou ta page principale
                }

                $error = 'Identifiants invalides.';
            } catch (\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface $e) {
                $error = 'Erreur de communication avec le serveur.';
            } catch (\Exception $e) {
                $error = 'Une erreur inconnue s’est produite.';
            }
        }

        return $this->render('login.html.twig', [
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->remove('jwt_token');
        return $this->redirectToRoute('login');
    }
}
