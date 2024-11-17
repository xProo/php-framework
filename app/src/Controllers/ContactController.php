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
            return $this->handleGetRequest();
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

    private function sendResponse(array $data, int $statusCode): void
    {
        header('Content-Type: application/json', true, $statusCode);
        echo json_encode($data);
    }
}
