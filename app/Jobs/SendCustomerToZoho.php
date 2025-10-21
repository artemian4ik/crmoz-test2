<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Customer;
use App\Services\Zoho\Inventory;

class SendCustomerToZoho implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Customer $customer)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $zohoCrmService = new Inventory();
        $response = $zohoCrmService->createContact([
            'contact_name' => $this->customer->contact_name,
            'email' => $this->customer->email,
            'phone' => $this->customer->phone,
            'mobile' => $this->customer->mobile,
            'contact_type' => $this->customer->contact_type,
        ]);

        if ($response->status() === 200 && $response->json()['contact'] === 0) {
            $customerData = $response->json()['contact'];
            $this->customer->contact_id = $customerData['contact_id'];
            $this->customer->status = 'active';
            $this->customer->save();
        }
    }
}
