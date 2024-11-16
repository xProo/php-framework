<?php

namespace App\Controllers;

use App\Controllers\AbstractController;

use App\Http\Request;
use App\Http\Response;


class ContactController extends AbstractController
{
    public function process(Request $request): Response
    {
        return $this->handleRequest($request);
    }

    public function handleRequest(Request $request): Response
    {
        var_dump($request);
        // Vérifier le type de contenu
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            return new Response('Content type must be application/json', 400);
        }

        // Décoder le corps de la requête
        $data = json_decode(file_get_contents('php://input'), true);

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

        // Générer le nom de fichier
        $timestamp = time();
        $filename = "{$timestamp}_{$data['email']}.json";

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

     
        return new Response("File created: $filename", 201) ; 
        
    }

    private function sendResponse(array $data, int $statusCode): void
    {
        header('Content-Type: application/json', true, $statusCode);
        echo json_encode($data);
    }
}
