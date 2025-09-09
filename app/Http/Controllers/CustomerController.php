<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CustomerController extends Controller
{
    public function index()
    {
        // Get all users with customer role
        $customers = User::where('role', 'customer')
        ->withCount('projects')
        ->withSum('projects', 'budget') // assuming projects table has `budget`
        ->get();

        $totalCustomers   = User::count();
        $activeCustomers  = User::where('status', 'Active')->count();
        $vipCustomers     = User::where('status', 'VIP')->count();

        return view('admin.customers.index', compact('customers', 'totalCustomers', 'activeCustomers', 'vipCustomers'));
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
}
