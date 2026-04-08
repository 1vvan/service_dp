<?php

namespace App\Mail;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Чек замовлення #' . $this->booking->id . ' — AutoCare',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-completed',
        );
    }

    public function attachments(): array
    {
        $this->booking->load(['client', 'car.carModel.brand', 'services', 'status']);

        $pdf = Pdf::loadView('receipts.booking', [
            'booking' => $this->booking,
            'priceCalculation' => $this->booking->price_calculation ?? [],
        ]);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(),
                'receipt-booking-' . $this->booking->id . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
