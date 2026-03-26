<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class ContactMessageModel
{
    public function __construct(private readonly Database $database)
    {
    }

    public function create(array $data): int
    {
        $this->database->statement(
            'INSERT INTO contact_messages
                (name, email, phone, subject, message, ip_address, user_agent, created_at)
             VALUES
                (:name, :email, :phone, :subject, :message, :ip_address, :user_agent, NOW())',
            $data
        );

        return (int) $this->database->lastInsertId();
    }
}
