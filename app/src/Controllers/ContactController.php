<?php

namespace App\Controllers;

use App\Controllers\AbstractController;

use App\Http\Request;
use App\Http\Response;


class ContactController extends AbstractController
{
    public function process(Request $request): Response
    {
        if ($request->getMethod() === 'GET') {
            if (preg_match('#^/contact/(.+)$#', $request->getUri(), $matches)) {
                return $this->handleGetSingleRequest($matches[1]);
            }
            return $this->handleGetRequest();
        }
        if ($request->getMethod() === 'PATCH') {
            if (preg_match('#^/contact/(.+)$#', $request->getUri(), $matches)) {
                return $this->handlePatchRequest($matches[1], $request);
            }
        }
        return $this->handleRequest($request);
    }

    public function handleRequest(Request $request): Response
    {
        // Lire les données brutes
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        // Valider les données
        $requiredFields = ['email', 'subject', 'message'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return new Response("Missing field: $field", 400);
            }
        }

        // Vérifier qu'il n'y a pas de champs non autorisés
        foreach ($data as $key => $value) {
            if (!in_array($key, $requiredFields)) {
                return new Response( "Field not allowed: $key", 400);
            }
        }

        // Modifier le format du nom de fichier
        $timestamp = time();
        $dateFormatted = date('Y-m-d_H-i-s', $timestamp);
        $filename = "{$dateFormatted}_{$data['email']}.json";

        // Créer le contenu à sauvegarder
        $content = [
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'dateOfCreation' => $timestamp,
            'dateOfLastUpdate' => $timestamp,
        ];

        // Sauvegarder dans le dossier /app/var/contacts
        $filePath = __DIR__ . '/../../var/contacts/' . $filename;
        file_put_contents($filePath, json_encode($content));

     
        return new Response(json_encode(['file' => $filename]), 201, ['Content-Type' => 'application/json']);
    }

    private function handleGetRequest(): Response
    {
        $contactsDir = __DIR__ . '/../../var/contacts/';
        $files = glob($contactsDir . '*.json');
        $contacts = [];

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $contacts[] = json_decode($content, true);
        }

        return new Response(
            json_encode($contacts), 
            200, 
            ['Content-Type' => 'application/json']
        );
    }

    private function handleGetSingleRequest(string $email): Response
    {
        $contactsDir = __DIR__ . '/../../var/contacts/';
        $files = glob($contactsDir . '*_' . $email . '.json');

        if (empty($files)) {
            return new Response(
                json_encode(['error' => 'Contact not found']),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        $filePath = $files[0];
        $content = file_get_contents($filePath);
        return new Response(
            $content,
            200,
            ['Content-Type' => 'application/json']
        );
    }

    private function handlePatchRequest(string $email, Request $request): Response
    {
        // Trouver le fichier correspondant
        $contactsDir = __DIR__ . '/../../var/contacts/';
        
        // Debug: Afficher les fichiers trouvés
        error_log("Searching for files matching: " . $contactsDir . '*_' . $email . '.json');
        $files = glob($contactsDir . '*_' . $email . '.json');
        error_log("Found files: " . print_r($files, true));

        if (empty($files)) {
            return new Response(
                json_encode(['error' => 'Contact not found']),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        // Lire les données existantes
        $filePath = $files[0];
        $existingData = json_decode(file_get_contents($filePath), true);

        // Lire les données de la requête
        $rawData = file_get_contents('php://input');
        $updateData = json_decode($rawData, true);

        // Debug: Afficher les données reçues
        error_log("Update data received: " . print_r($updateData, true));

        // Vérifier les champs autorisés
        $allowedFields = ['email', 'subject', 'message'];
        foreach ($updateData as $key => $value) {
            if (!in_array($key, $allowedFields)) {
                return new Response(
                    json_encode(['error' => "Field not allowed: $key"]),
                    400,
                    ['Content-Type' => 'application/json']
                );
            }
        }

        // Mettre à jour les données
        foreach ($updateData as $key => $value) {
            $existingData[$key] = $value;
        }
        $existingData['dateOfLastUpdate'] = time();

        // Si l'email a changé, renommer le fichier
        $newFilePath = $filePath;
        if (isset($updateData['email'])) {
            $newFilename = date('Y-m-d_H-i-s', $existingData['dateOfCreation']) . '_' . $updateData['email'] . '.json';
            $newFilePath = $contactsDir . $newFilename;
        }

        // Sauvegarder les modifications
        file_put_contents($newFilePath, json_encode($existingData));
        if ($newFilePath !== $filePath) {
            unlink($filePath);
        }

        return new Response(
            json_encode($existingData),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    private function sendResponse(array $data, int $statusCode): void
    {
        header('Content-Type: application/json', true, $statusCode);
        echo json_encode($data);
    }
}
