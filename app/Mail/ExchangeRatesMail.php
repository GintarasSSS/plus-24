<?php

namespace App\Mail;

use App\Models\Rate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExchangeRatesMail extends Mailable
{
    use Queueable, SerializesModels;

    private array $rates;

    public function __construct($rates)
    {
        $this->rates = $rates;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Exchange Rates Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.rates'
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $fileName = $this->generateReport();

        return [
            Attachment::fromPath(base_path() . '/public/' . $fileName)
        ];
    }

    private function generateReport(): string
    {
        $fileName = 'rates.csv';

        $fp=fopen($fileName, "w+");
        fputcsv($fp,  ['Base', 'Date', 'Rates']);
        fputcsv($fp, [$this->rates['base'], $this->rates['date'], json_encode($this->rates['rates'])]);
        fclose($fp);

        return $fileName;
    }
}
