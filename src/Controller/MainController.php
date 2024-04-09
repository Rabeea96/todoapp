<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Builder\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MainController extends AbstractController
{
    #[Route('/', name:'todos_list', methods:['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Used to create a form based on the Entity class - the form variable should be passed to the view
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);

        // Used to list the todos from the database
        $repository = $entityManager->getRepository(Todo::class);
        $todos = $repository->findAll();

        return $this->render('index.html.twig', 
        [
            'todos' => $todos,
            'form' => $form->createView()
        ]);
    }

    #[Route('/create', name:'todo_create', methods:['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager) : RedirectResponse
    {
        // Check if form is valid
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        // Display form errors
        // dd($form->getErrors(true));
        // exit;

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($todo);
            $entityManager->flush();

            // Redirect back to the todo-list
            return $this->redirect($this->generateUrl('todos_list'));
        }

        // Redirect back to the todo-list + display error message (flash message)
        $this->addFlash(
            'danger', 'The TODO description must be at least 3 characters long'
        );
        return $this->redirect($this->generateUrl('todos_list'));
    }

    #[Route('/update/{id}', name:'todo_update', methods:['POST'])]
    public function update(Todo $todo, Request $request, EntityManagerInterface $entityManager) : RedirectResponse
    {
        // Update the TODO
        $isCompleted = (bool)$request->request->get('isCompleted');    
        $todo->setIsCompleted($isCompleted);
        $entityManager->flush();

        // Add flash message
        $this->addFlash('success', 'Todo item updated successfully.');

        // Redirect back to the todo-list
        return $this->redirect($this->generateUrl('todos_list'));
    }

    #[Route('/delete/{id}', name:'todo_delete', methods:['POST'])]
    public function delete(Todo $todo, EntityManagerInterface $entityManager) : RedirectResponse
    {
        // Remove the TODO
        $entityManager->remove($todo);
        $entityManager->flush();

        // Add flash message
        $this->addFlash('success', 'Todo item deleted successfully.');

        // Redirect back to the todo-list
        return $this->redirect($this->generateUrl('todos_list'));
    }
}