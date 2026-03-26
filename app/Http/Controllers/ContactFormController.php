<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Request;
use App\Services\ContactFormService;

class ContactFormController extends Controller
{
    public function submit(Request $request): \App\Core\Response
    {
        $result = $this->app->make(ContactFormService::class)->submit(
            $request->all(),
            $request->ip(),
            $request->userAgent()
        );

        if (($result['errors'] ?? []) !== []) {
            return $this->validationRedirect($result['errors'], $request->all(), '/kontakt/');
        }

        $message = ($result['delivery'] ?? 'logged') === 'sent'
            ? 'Wiadomość została wysłana.'
            : 'Wiadomość została zapisana. Skontaktujemy się najszybciej jak to możliwe.';

        return $this->redirect('/kontakt/', $message);
    }
}
