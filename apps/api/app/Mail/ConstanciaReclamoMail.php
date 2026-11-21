<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ConstanciaReclamoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Complaint $complaint) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Constancia Libro de Reclamaciones {$this->complaint->correlative_code} - Horus Group SRL",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reclamo-constancia',
            with: [
                'complaint' => $this->complaint,
                'company' => config('horus.company'),
            ],
        );
    }

    /**
     * @return list<Attachment>
     */
    public function attachments(): array
    {
        $path = $this->complaint->receipt_pdf_path;
        if ($path !== null && Storage::disk('local')->exists($path)) {
            return [
                Attachment::fromStorageDisk('local', $path)
                    ->as("{$this->complaint->correlative_code}.pdf")
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
