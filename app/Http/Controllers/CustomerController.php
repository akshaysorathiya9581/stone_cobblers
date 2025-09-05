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
        $customers = User::where('role', 'customer')->get();
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
            'zip_code'         => $postData['zipCode'],
            'additional_notes' => $postData['additionalNotes'] ?? null,
            'referral_source'  => $postData['referralSource'] ?? null,
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
        return view('admin.customers.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // update logic
    }

    public function destroy($id)
    {
        // delete logic
    }

    public function checkEmail(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email' => ['required','email','max:120'],
        ]);

        if ($v->fails()){
            return response()->json([
                'unique'  => false,
                'message' => $v->errors()->first('email') ?? 'Invalid email.',
            ]);
        }

        $exists = User::where('email', $request->email)->exists();
        return response()->json([
            'unique'  => !$exists,
            'message' => $exists ? 'This email is already registered.' : 'Email is available.',
        ]);
    }
}
