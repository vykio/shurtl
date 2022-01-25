<?php

namespace App\Controller;

use App\Entity\ShortenedUrl;
use App\Entity\User;
use App\Form\ShortenerFormType;
use App\Repository\ShortenedUrlRepository;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/api", name="api_")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/urls", name="shortener", methods={"GET"})
     */
    public function getShortenedUrls(Request $request, SerializerInterface $serializer, ShortenedUrlRepository $urlRepository)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();

        if ($user instanceof User && !$user->isVerified()) {
            return $this->render('registration/email_not_confirmed.html.twig');
        }

        $urls = $urlRepository->findBy(array('user_id' => ($user->getId())));

        return new JsonResponse(json_decode($serializer->serialize($urls, 'json'), 'true'));
    }

    /**
     * @Route("/shortener", methods={"POST"})
     */
    public function createShortenedUrl(Request $request, SerializerInterface $serializer, ShortenedUrlRepository $urlRepository)
    {
        $user = $this->getUser();

        if ($user instanceof User && !$user->isVerified()) {
            return $this->render('registration/email_not_confirmed.html.twig');
        }

        $shortenedUrl = new ShortenedUrl();
        $form = $this->createForm(ShortenerFormType::class, $shortenedUrl);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }
    }
}
