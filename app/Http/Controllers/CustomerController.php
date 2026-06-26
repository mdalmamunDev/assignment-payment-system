<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::query()
            ->when(request('name'), fn($q) => $q->where(function($q) {
                $q->where('name', 'like', '%' . request('name') . '%')
                  ->orWhere('email', 'like', '%' . request('name') . '%')
                  ->orWhere('phone', 'like', '%' . request('name') . '%');
            }))
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->latest()
            ->paginate(request('per_page', 15));

        return retRes('', $customers, 200);
    }

    public function store(StoreCustomerRequest $request)
    {
        Customer::create($request->validated());

        return retRes('Customer created successfully.', null, 2000);
    }

    public function show(Customer $customer)
    {
        $customer->load('projects');

        return retRes('', $customer, 2000);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return retRes('Customer updated successfully.', null, 2000);
    }

    public function destroy(Customer $customer)
    {
        if ($customer->hasProjects()) {
            return retRes('Cannot delete customer with existing projects.', null, 4000);
        }

        $customer->delete();

        return retRes('Customer deleted successfully.', null, 2000);
    }
}