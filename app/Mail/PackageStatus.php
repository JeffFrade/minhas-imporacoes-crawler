<?php

namespace App\Mail;

use App\Repositories\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PackageStatus extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Package
     */
    private $package;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('package-email')
            ->with([
                'trackingNumber' => $this->package->tracking_number,
                'status' => $this->package->status
            ]);
    }
}
