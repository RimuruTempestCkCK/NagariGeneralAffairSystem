<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentExpiryNotification extends Notification
{
    use Queueable;

    public $typeCode;
    public $title;
    public $message;
    public $url;
    public $documentId;
    public $uniqueKey;

    public function __construct($typeCode, $title, $message, $url, $documentId, $uniqueKey)
    {
        $this->typeCode = $typeCode; // e.g., STNK_EXPIRING, ASSET_CERT_EXPIRED
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->documentId = $documentId;
        $this->uniqueKey = $uniqueKey; // Used for deduplication
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type_code' => $this->typeCode,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'document_id' => $this->documentId,
            'unique_key' => $this->uniqueKey,
        ];
    }
}
