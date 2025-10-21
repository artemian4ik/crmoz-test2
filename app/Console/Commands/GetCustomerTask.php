<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Zoho\Inventory;
use App\Models\Customer;
use App\Models\ZohoStatus;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GetCustomerTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-customer-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get customers from Zoho Inventory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Getting customers from Zoho Inventory...');
            
            $inventoryService = new Inventory();
            
            $result = $inventoryService->getContacts(1, 200);
            $removeOldCustomers = [];
            
            if ($result['success']) {
                $customers = $result['data']['contacts'] ?? [];
                
                $this->info('Found ' . count($customers) . ' customers');
                
                foreach ($customers as $customer) {
                    $removeOldCustomers[] = $customer['contact_id'];
                    
                    $customerData = [
                        'contact_id' => $customer['contact_id'],
                        'contact_name' => $customer['contact_name'],
                        'customer_name' => $customer['customer_name'] ?? null,
                        'vendor_name' => $customer['vendor_name'] ?? null,
                        'company_name' => $customer['company_name'] ?? null,
                        'website' => $customer['website'] ?? null,
                        'language_code' => $customer['language_code'] ?? null,
                        'language_code_formatted' => $customer['language_code_formatted'] ?? null,
                        'contact_type' => $customer['contact_type'] ?? 'customer',
                        'contact_type_formatted' => $customer['contact_type_formatted'] ?? null,
                        'status' => $customer['status'] ?? 'active',
                        'customer_sub_type' => $customer['customer_sub_type'] ?? null,
                        'source' => $customer['source'] ?? null,
                        'is_linked_with_zohocrm' => $customer['is_linked_with_zohocrm'] ?? false,
                        'payment_terms' => $customer['payment_terms'] ?? 0,
                        'payment_terms_label' => $customer['payment_terms_label'] ?? null,
                        'currency_id' => $customer['currency_id'] ?? null,
                        'currency_code' => $customer['currency_code'] ?? 'UAH',
                        'twitter' => $customer['twitter'] ?? null,
                        'facebook' => $customer['facebook'] ?? null,
                        'outstanding_receivable_amount' => $customer['outstanding_receivable_amount'] ?? 0,
                        'outstanding_receivable_amount_bcy' => $customer['outstanding_receivable_amount_bcy'] ?? 0,
                        'outstanding_payable_amount' => $customer['outstanding_payable_amount'] ?? 0,
                        'outstanding_payable_amount_bcy' => $customer['outstanding_payable_amount_bcy'] ?? 0,
                        'unused_credits_receivable_amount' => $customer['unused_credits_receivable_amount'] ?? 0,
                        'unused_credits_receivable_amount_bcy' => $customer['unused_credits_receivable_amount_bcy'] ?? 0,
                        'unused_credits_payable_amount' => $customer['unused_credits_payable_amount'] ?? 0,
                        'unused_credits_payable_amount_bcy' => $customer['unused_credits_payable_amount_bcy'] ?? 0,
                        'first_name' => $customer['first_name'] ?? null,
                        'last_name' => $customer['last_name'] ?? null,
                        'email' => $customer['email'] ?? null,
                        'phone' => $customer['phone'] ?? null,
                        'mobile' => $customer['mobile'] ?? null,
                        'portal_status' => $customer['portal_status'] ?? 'disabled',
                        'portal_status_formatted' => $customer['portal_status_formatted'] ?? null,
                        'custom_fields' => $customer['custom_fields'] ?? [],
                        'custom_field_hash' => $customer['custom_field_hash'] ?? [],
                        'tags' => $customer['tags'] ?? [],
                        'ach_supported' => $customer['ach_supported'] ?? false,
                        'has_attachment' => $customer['has_attachment'] ?? false,
                        'zoho_created_time' => isset($customer['created_time']) ? Carbon::parse($customer['created_time']) : null,
                        'zoho_last_modified_time' => isset($customer['last_modified_time']) ? Carbon::parse($customer['last_modified_time']) : null,
                        'deleted_at' => null,
                    ];
                    
                    Customer::updateOrCreate(
                        ['contact_id' => $customer['contact_id']],
                        $customerData
                    );
                    
                    $this->info('Processed: ' . $customer['contact_name']);
                }

                Customer::whereNotIn('contact_id', $removeOldCustomers)->delete();
                
                $this->info('Successfully synced ' . count($customers) . ' customers');
                ZohoStatus::createStatus('get-customers', 'success', 'Customers fetched successfully');
                
                return Command::SUCCESS;
            } else {
                $errorMessage = $result['error'] ?? 'Unknown error';
                $this->error('Failed to get customers: ' . $errorMessage);
                ZohoStatus::createStatus('get-customers', 'error', $errorMessage);
                
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Get Customers Task Error: ' . $e->getMessage());
            Log::error('Get Customers Task Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            ZohoStatus::createStatus('get-customers', 'error', $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}

