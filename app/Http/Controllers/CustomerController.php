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
        $fullName = trim(
            ($postData['first_name'] ?? '') . ' ' . ($postData['last_name'] ?? '')
        );

        // Role (defaults to customer)
        $role = $postData['role'] ?? 'customer';

        // Modules: expect array; clean and reindex. If empty -> null
        $modulesRaw = $postData['modules'] ?? null;
        $modulesValue = null;
        if (is_array($modulesRaw)) {
            // remove empty values and reindex
            $modulesClean = array_values(array_filter($modulesRaw, function ($m) {
                return $m !== null && $m !== '';
            }));
            if (count($modulesClean)) {
                $modulesValue = json_encode($modulesClean);
            }
        } elseif (is_string($modulesRaw) && $modulesRaw !== '') {
            // if client sent JSON string, try to decode it
            $decoded = json_decode($modulesRaw, true);
            if (is_array($decoded)) {
                $modulesClean = array_values(array_filter($decoded, function ($m) {
                    return $m !== null && $m !== '';
                }));
                if (count($modulesClean)) {
                    $modulesValue = json_encode($modulesClean);
                }
            }
        }

        $user = User::create([
            'name'             => $fullName,
            'first_name'       => $postData['first_name'] ?? null,
            'last_name'        => $postData['last_name'] ?? null,
            'email'            => $postData['email'] ?? null,
            'phone'            => $postData['phone'] ?? null,
            'address'          => $postData['address'] ?? null,
            'city'             => $postData['city'] ?? null,
            'state'            => $postData['state'] ?? null,
            'zipCode'          => $postData['zipCode'] ?? null,
            'additionalNotes'  => $postData['additionalNotes'] ?? null,
            'referralSource'   => $postData['referralSource'] ?? null,
            'status'           => $postData['customer_status'] ?? 'Active',
            'role'             => $role,
            'modules'          => $modulesValue,
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
// load customer (or 404)
        $customer = User::findOrFail($id);

        // load projects related to customer
        $customerProjects = $customer->projects()
            ->orderBy('created_at', 'desc')
            ->get();

        // derived values
        $projectsCount = $customerProjects->count();

        // compute a numeric total value if project has budget/budget_min/budget_max
        $totalValue = $customerProjects->reduce(function ($carry, $p) {
            $val = 0.0;

            // primary: explicit numeric budget
            if (isset($p->budget) && is_numeric($p->budget)) {
                $val = (float) $p->budget;
            } else {
                // fallback: range min/max
                $min = (isset($p->budget_min) && is_numeric($p->budget_min)) ? (float)$p->budget_min : 0.0;
                $max = (isset($p->budget_max) && is_numeric($p->budget_max)) ? (float)$p->budget_max : $min;
                $val = ($min + $max) / 2.0;
            }

            return $carry + $val;
        }, 0.0);

        // group projects by normalized status for nested tabs
        $projectsByStatus = $customerProjects->groupBy(function ($p) {
            $s = strtolower(trim((string)($p->status ?? 'planning')));
            // normalize a few common statuses
            if (in_array($s, ['planning', 'planned'])) return 'planning';
            if (in_array($s, ['in progress','progress','ongoing'])) return 'progress';
            if (in_array($s, ['on hold','hold'])) return 'hold';
            if (in_array($s, ['completed','complete','done'])) return 'completed';
            if (in_array($s, ['cancelled','canceled'])) return 'cancelled';
            return $s;
        });

        // available main tabs
        $tabs = [
            ['id' => 1, 'text' => 'Customer Info'],
            ['id' => 2, 'text' => 'Projects'],
        ];

        return view('admin.customers.show', compact(
            'id',
            'customer',
            'customerProjects',
            'projectsCount',
            'totalValue',
            'projectsByStatus',
            'tabs'
        ));
    }

    public function edit($id)
    {
        $customer = User::where('id', $id)->first();
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $postData = $request->all();

        // Combine first and last name
        $fullName = trim(($postData['first_name'] ?? '') . ' ' . ($postData['last_name'] ?? ''));

        // === Validation (optional but recommended) ===
        $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'required|email|unique:users,email,' . $id,
            'phone'            => 'required|string|max:20',
            'address'          => 'required|string|max:255',
            'city'             => 'required|string|max:100',
            'state'            => 'required|string|max:100',
            'zipCode'          => 'required|string|max:20',
            'role'             => 'required|in:admin,customer',
            'modules'          => 'nullable|array',
            'modules.*'        => 'string',
        ]);

        // Prepare base data
        $data = [
            'name'             => $fullName,
            'first_name'       => $postData['first_name'],
            'last_name'        => $postData['last_name'],
            'email'            => $postData['email'],
            'phone'            => $postData['phone'],
            'address'          => $postData['address'],
            'city'             => $postData['city'],
            'state'            => $postData['state'],
            'zipCode'          => $postData['zipCode'],
            'additionalNotes'  => $postData['additionalNotes'] ?? null,
            'referralSource'   => $postData['referralSource'] ?? null,
            'status'           => $postData['customer_status'] ?? 'Active',
            'role'             => $postData['role'] ?? 'customer',
            'modules'          => isset($postData['modules']) ? json_encode($postData['modules']) : json_encode([]),
        ];

        // === Update the record ===
        $customer = User::findOrFail($id);
        $customer->update($data);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Customer updated successfully.',
            'id'      => $customer->id,
            'role'    => $customer->role,
            'modules' => json_decode($customer->modules, true),
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
