<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Form\ProviderType;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/provider')]
class ProviderController extends AbstractController
{
    #[Route('/', name: 'app_provider_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGENT')  ")]
    public function index(ProviderRepository $providerRepository): Response
    {
        return $this->render('provider/index.html.twig', [
            'providers' => $providerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_provider_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function new(Request $request, ProviderRepository $providerRepository): Response
    {
        $provider = new Provider();
        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Upload de la photo du provider
            $logo = $form->get('logo')->getData();


            if ($logo) {
                $originalLogoname = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);

                $newLogoname =   $originalLogoname.'-'.uniqid().'.'.$logo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $logo->move(
                        $this->getParameter('provider_directory'),
                        $newLogoname
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $provider->setLogo($newLogoname);

            }
            //fin Upload
            $provider->setCreatedAt(new \DateTimeImmutable());
            $providerRepository->save($provider, true);

            return $this->redirectToRoute('app_provider_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('provider/new.html.twig', [
            'provider' => $provider,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_provider_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGENT')  ")]
    public function show(Provider $provider): Response
    {
        return $this->render('provider/show.html.twig', [
            'provider' => $provider,
        ]);
    }
    #[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/{id}/edit', name: 'app_provider_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Provider $provider, ProviderRepository $providerRepository): Response
    {
        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Upload de la photo du provider
            $logo = $form->get('logo')->getData();


            if ($logo) {
                $originalLogoname = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);

                $newLogoname =   $originalLogoname.'-'.uniqid().'.'.$logo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $logo->move(
                        $this->getParameter('provider_directory'),
                        $newLogoname
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $provider->setLogo($newLogoname);
            }
            //fin Upload

            $providerRepository->save($provider, true);

            return $this->redirectToRoute('app_provider_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('provider/edit.html.twig', [
            'provider' => $provider,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_provider_delete', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function delete(Request $request, Provider $provider, ProviderRepository $providerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$provider->getId(), $request->request->get('_token'))) {
            $providerRepository->remove($provider, true);
        }

        return $this->redirectToRoute('app_provider_index', [], Response::HTTP_SEE_OTHER);
    }
}
