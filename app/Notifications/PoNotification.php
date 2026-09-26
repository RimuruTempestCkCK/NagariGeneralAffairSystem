<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PoNotification extends Notification
{
    use Queueable;

    public $typeCode;
    public $title;
    public $message;
    public $url;
    public $poId;

    public function __construct($typeCode, $title, $message, $url, $poId)
    {
        $this->typeCode = $typeCode; // e.g., PO_NEW, PO_APPROVED, PO_REJECTED
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->poId = $poId;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Internal only for now
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type_code' => $this->typeCode,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'po_id' => $this->poId,
        ];
    }
}
