<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerContactedMail;

use App\Models\User;
use App\Models\Quote;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get all users with customer role
        $customers = User::where('role', 'customer')
        ->withCount('projects')
        ->withSum('quotes', 'total') // assuming quotes table has `total`
        ->orderBy('created_at', 'desc')
        ->get();
        
        // dd($customers);
        $totalCustomers   = User::count();
        $activeCustomers  = User::where('status', 'Active')->count();
        $vipCustomers     = User::where('status', 'VIP')->count();

        if ($user->role === 'admin') {
            $totalRevenue = Quote::sum('total');
        } elseif ($user->role === 'customer') {
            $totalRevenue = Quote::where('user_id', $user->id)->sum('total');
        }

        return view('admin.customers.index', compact('customers', 'totalCustomers', 'activeCustomers', 'vipCustomers', 'totalRevenue'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $postData = $request->all();
        $fullName = trim($postData['first_name'].' '.$postData['last_name']);

        $user = User::create([
            'name'             => $fullName,
            'first_name'       => $postData['first_name'],
            'last_name'        => $postData['last_name'],
            'email'            => $postData['email'],
            'phone'            => $postData['phone'],
            'address'          => $postData['address'],
            'city'             => $postData['city'],
            'state'            => $postData['state'],
            'zipCode'         => $postData['zipCode'],
            'additionalNotes' => $postData['additionalNotes'] ?? null,
            'referralSource'  => $postData['referralSource'] ?? null,
            'status'  => $postData['customer_status'] ?? 'Active',
            'password'         => Hash::make('123456'),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Customer created successfully.',
            'id'      => $user->id,
            'name'    => $user->name,
        ], 201);
    }

    public function show($id)
    {
        return view('admin.customers.show', compact('id'));
    }

    public function edit($id)
    {
        $customer = User::where('id', $id)->first();
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $postData = $request->all();
        $fullName = trim($postData['first_name'].' '.$postData['last_name']);

        $data = [
            'name'             => $fullName,
            'first_name'       => $postData['first_name'],
            'last_name'        => $postData['last_name'],
            'email'            => $postData['email'],
            'phone'            => $postData['phone'],
            'address'          => $postData['address'],
            'city'             => $postData['city'],
            'state'            => $postData['state'],
            'zipCode'         => $postData['zipCode'],
            'additionalNotes' => $postData['additionalNotes'] ?? null,
            'referralSource'  => $postData['referralSource'] ?? null,
            'status'  => $postData['customer_status'] ?? 'Active'
        ];

        $customer = User::findOrFail($id);
        $customer->update($data);

        return response()->json([
            'status' => 'ok',
            'message' => 'Customer updated successfully.',
            'id' => $customer->id
        ]);
    }

    public function destroy($id)
    {
        // delete logic
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'id' => ['nullable','integer']
        ]);

        $email = $request->input('email');
        $ignoreId = $request->input('id');

        $query = User::where('email', $email);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $exists = $query->exists();

        if ($exists) {
            return response()->json(['unique' => false, 'message' => 'Email is already registered.']);
        }

        return response()->json(['unique' => true, 'message' => '']);
    }

    public function updateLastContact($id, Request $request)
    {
        $customer = User::find($id);

        if (! $customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found.'
            ], 404);
        }

        $customer->last_contact = now();
        $customer->save();

        // Send mail to customer
        Mail::to($customer->email)->send(new CustomerContactedMail($customer));

        return response()->json([
            'status' => 'success',
            'message' => 'Last contact updated and email sent successfully.',
            'last_contact' => $customer->last_contact->toDateTimeString()
        ]);
    }
}
