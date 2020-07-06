<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TaskRepository $taskRepository
     */
    public function __construct(EntityManagerInterface $entityManager, TaskRepository $taskRepository)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
    }

    /**
     * @Route("/tasks", name="task_list", methods={"GET","HEAD"})
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tasks = $this->taskRepository->findByStatus(Task::STATUS_NEW);
        $data = [];

        foreach ($tasks as $task) {
            $data[] = [
                'id' => $task->getId(),
                'title' => $task->getTitle()
            ];
        }

        return $this->json(['data' => $data]);
    }

    /**
     * @Route("/tasks/{id}", name="task_show", methods={"GET"})
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        /** @var Task $task */
        $task = $this->taskRepository->find($id);

        if (!$task) {
            return $this->json([
                'message' => 'No task found for id ' . $id
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'message' => 'Check out this great task: ' . $task->getTitle()
        ]);
    }

    /**
     * @Route("/tasks", name="task_create", methods={"POST"})
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $task = new Task();
        $task->setTitle($data['title'] ?? null);

        $errors = $validator->validate($task);
        if (count($errors) > 0) {
            return $this->json([
                'message' => (string)$errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Saved new task with id ' . $task->getId()
        ], Response::HTTP_CREATED);
    }


    /**
     * @Route("/tasks/{id}", name="task_update", methods={"PUT"})
     *
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update($id, Request $request): JsonResponse
    {
        /** @var Task $task */
        $task = $this->taskRepository->find($id);

        if (!$task) {
            return $this->json([
                'message' => 'No task found for id ' . $id
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        !empty($data['title']) && $task->setTitle($data['title']);
        !empty($data['status']) && $task->setStatus($data['status']);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Updated task with id ' . $task->getId()
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/tasks/{id}", name="task_delete", methods={"DELETE"})
     *
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        /** @var Task $task */
        $task = $this->taskRepository->find($id);

        if (!$task) {
            return $this->json([
                'message' => 'No task found for id ' . $id
            ], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Task has been removed'
        ]);
    }
}
