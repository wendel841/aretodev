<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {
        $posts = $this->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->findAll();

        return $this->render('AppBundle:blog:index.html.twig', compact('posts'));
    }

    /**
     * @Route("/delete/{slug}.html", name="blog_delete")
     */
    public function deleteAction(Request $request, $slug)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->findBySlug($slug);

        if (!$post) {
            throw $this->createNotFoundException('No post found for id ' . $slug);
        }

        $this->denyAccessUnlessGranted('edit', $post);

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/create", name="blog_create")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $post = new Post();
        $post->setUser($this->getUser());

        return $this->postForm($request, $post);
    }

    /**
     * @Route("/edit/{slug}.html", name="blog_edit")
     */
    public function editAction(Request $request, $slug)
    {
        $post = $this->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->findBySlug($slug);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id '.$slug
            );
        }

        $this->denyAccessUnlessGranted('edit', $post);

        return $this->postForm($request, $post);
    }

    protected function postForm(Request $request, Post $post)
    {
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em->persist($post);
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash(
                    'notice',
                    $translator->trans('post.error.unique_slug')
                );
                return $this->redirectToRoute('blog_create');
            }

            return $this->redirectToRoute('blog_show', [
                'slug' => $post->getSlug(),
            ]);
        }

        return $this->render('AppBundle:blog:form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}.html", name="blog_show")
     */
    public function showAction(Request $request, $slug)
    {
        $post = $this->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->findBySlug($slug);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id '.$slug
            );
        }

        $this->denyAccessUnlessGranted('view', $post);

        return $this->render('AppBundle:blog:show.html.twig', compact('post'));
    }
}
