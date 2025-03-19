<?php
declare(strict_types=1);
namespace App\Controller;

use App\Service\UserService;
use App\Service\ChargeService;
use Fig\Http\Message\StatusCodeInterface;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

#[OA\Info(
    title: "User API",
    version: "1.0.0",
    description: "API pour la gestion des utilisateurs et des charges"
)]
class UserController
{
    public function __construct(private UserService $userService, private ChargeService $chargeService)
    {
    }

    #[OA\Post(
        path: "/user",
        summary: "Créer un utilisateur",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "firstName", type: "string", example: "John"),
                    new OA\Property(property: "lastName", type: "string", example: "Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john.doe@example.com"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Utilisateur créé",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "firstName", type: "string", example: "John"),
                        new OA\Property(property: "lastName", type: "string", example: "Doe"),
                        new OA\Property(property: "email", type: "string", format: "email", example: "john.doe@example.com"),
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Données de requête vides"),
            new OA\Response(response: 422, description: "Adresse email invalide"),
            new OA\Response(response: 500, description: "Erreur interne du serveur"),
            new OA\Response(response: 415, description: "Type de contenu non supporté")
        ]
    )]
    public function createUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($request->getHeaderLine('Content-Type') !== 'application/json') {
            return $response->withStatus(StatusCodeInterface::STATUS_UNSUPPORTED_MEDIA_TYPE, 'Content-Type header must be application/json');
        }

        try {
            $data = json_decode($request->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            return $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST, sprintf('Invalid JSON request: %s', $e->getMessage()));
        }

        if (empty($data['firstName']) || empty($data['lastName']) || empty($data['email'])) {
            return $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST, 'First name, last name and email cannot be empty');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $response->withStatus(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY, 'Invalid email address');
        }

        try {
            $user = $this->userService->createUser(
                $data['firstName'],
                $data['lastName'],
                $data['email']
            );

            $response->getBody()->write(json_encode($user->toArray()));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (Throwable $e) {
            return $response->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

        #[OA\Delete(
        path: "/user/{id}",
        summary: "Supprimer un utilisateur",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l’utilisateur à supprimer",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "Utilisateur supprimé"),
            new OA\Response(response: 500, description: "Erreur interne du serveur")
        ]
    )]
    public function deleteUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $this->userService->deleteUser((int) $request->getAttribute('id'));

            return $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);
        } catch (Throwable $e) {
            return $response->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, sprintf('Unable to delete user: %s', $e->getMessage()));
        }
    }

    #[OA\Post(
        path: "/user/{id}/charge",
        summary: "Ajouter une charge",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l’utilisateur",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "energy", type: "integer", example: 7500, description: "Énergie en Wh"),
                    new OA\Property(property: "cost", type: "integer", example: 3000, description: "Coût en centimes"),
                    new OA\Property(property: "isSuccessful", type: "boolean", example: true, description: "Charge réussie ou non")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Charge ajoutée", content: new OA\JsonContent(type: "object")),
            new OA\Response(response: 400, description: "Requête JSON invalide"),
            new OA\Response(response: 415, description: "Type de contenu non supporté")
        ]
    )]
    public function addCharge(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($request->getHeaderLine('Content-Type') !== 'application/json') {
            return $response->withStatus(StatusCodeInterface::STATUS_UNSUPPORTED_MEDIA_TYPE, 'Content-Type header must be application/json');
        }

        try {
            $data = json_decode($request->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            return $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST, sprintf('Invalid JSON request: %s', $e->getMessage()));
        }

        try {
            $charge = $this->chargeService->addCharge(
                (int) $request->getAttribute('id'),
                (int) $data['energy'],
                (int) $data['cost'],
                (bool) $data['isSuccessful']
            );
        } catch (Throwable $e) {
            return $response->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, $e->getMessage());
        }

        $response->getBody()->write(json_encode($charge->toArray()));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(StatusCodeInterface::STATUS_CREATED);
    }

    #[OA\Get(
        path: "/user/{id}/statistics",
        summary: "Obtenir les statistiques de charge d’un utilisateur",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l’utilisateur",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Statistiques de charge obtenues",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "chargesNumber", type: "integer", example: 5),
                        new OA\Property(property: "totalEnergy", type: "number", format: "float", example: 37.5),
                        new OA\Property(property: "averageCost", type: "number", format: "float", example: 3.0)
                    ]
                )
            )
        ]
    )]
    public function getChargeStatistics(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $statistics = $this->chargeService->getStatistics((int) $request->getAttribute('id'));

        $response->getBody()->write(json_encode($statistics->toArray()));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(StatusCodeInterface::STATUS_OK);
    }
}
