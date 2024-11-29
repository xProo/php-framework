<?php

#utilisé pour tester le json

namespace App\Models;

class Contact
{
    private string $email;
    private string $subject;
    private string $message;
    private int $dateOfCreation;
    private int $dateOfLastUpdate;

    public function __construct(string $email, string $subject, string $message)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->dateOfCreation = time();
        $this->dateOfLastUpdate = $this->dateOfCreation;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'dateOfCreation' => $this->dateOfCreation,
            'dateOfLastUpdate' => $this->dateOfLastUpdate
        ];
    }

    public function getFilename(): string
    {
        return date('Y-m-d_H-i-s', $this->dateOfCreation) . '_' . $this->email . '.json';
    }
} 