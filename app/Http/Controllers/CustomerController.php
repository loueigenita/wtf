<?php

// app/Http/Controllers/CustomerController.php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use NumberFormatter;
class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['index', 'show', 'createOrder', 'storeOrder']);
    }

    public function index()
    {
        $customers = Customer::latest()->paginate(10);

        return inertia('customers/Index', compact('customers'));
    }

    public function create()
    {
        return inertia('customers/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'email' => 'required|email',
            'contact' => 'required',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return inertia('customers/Edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'email' => 'required|email',
            'contact' => 'required',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function show(Customer $customer)
    {
        $orders = Order::where('customer_id', $customer->id)->get();
        $totalOrdersAmount = $orders->sum('amount') ?? 0;

        
        if (request()->input('pdf')) {
            $pdf = PDF::loadView('pdf/show_pdf', compact('customer', 'orders', 'totalOrdersAmount'));

            $filename = 'customer_orders_' . $customer->id . '.pdf';
    
            return $pdf->download($filename);
        }

        $data = [
            'customer' => $customer,
            'orders' => $orders,
            
        ];

        return Inertia::render('customers/Show', $data);
    }
    public function createOrder(Customer $customer)
    {
        return inertia('orders/Create', compact('customer'));
    }

    public function storeOrder(Request $request, Customer $customer)
    {
        $request->validate([
            'order_name' => 'required',
            'order_details' => 'required',
            'amount' => 'required|numeric',
        ]);

        $orderData = $request->only(['order_name', 'order_details', 'amount']);
        $orderData['customer_id'] = $customer->id;

        Order::create($orderData);

        return back()->with('success', 'Order Deleted successfully.');
    }

    public function editOrder(Customer $customer, Order $order)
    {
        return inertia('orders/Edit', [
            'customer' => $customer,
            'order' => $order,
        ]);
    }

   
public function updateOrder(Request $request)
{
    $customerId = $request->route('customer'); // Fetch the customer ID from the route parameter
    $orderId = $request->route('order'); // Fetch the order ID from the route parameter

    // Use the IDs to fetch the corresponding Customer and Order models
    $customer = Customer::findOrFail($customerId);
    $order = Order::findOrFail($orderId);

    $request->validate([
        'order_name' => 'required',
        'order_details' => 'required',
        'amount' => 'required|numeric',
    ]);

    $order->update($request->only(['order_name', 'order_details', 'amount']));

    return redirect()->route('customers.show', $customerId)->with('success', 'Order updated successfully.');
}
}
