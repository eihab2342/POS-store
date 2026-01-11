<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query()->where('role', 'admin');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            if ($request->is_active === '1') {
                $query->where('is_active', true);
            } elseif ($request->is_active === '0') {
                $query->where('is_active', false);
            }
        }

        $admins = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admins.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role = 'admin';
        $user->is_active = $request->boolean('is_active', true);
        $user->save();

        return redirect()->route('admins.index')->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    public function edit(User $admin)
    {
        if ($admin->role !== 'admin') {
            abort(404);
        }

        return view('admins.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        if ($admin->role !== 'admin') {
            abort(404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $admin->name = $data['name'];
        $admin->email = $data['email'];

        if (! empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->is_active = $request->boolean('is_active', true);
        $admin->save();

        return redirect()->route('admins.index')->with('success', 'تم تعديل المستخدم بنجاح');
    }

    public function destroy(User $admin)
    {
        if ($admin->id === Auth::id()) {
            return redirect()->route('admins.index')->with('error', 'لا يمكن حذف المستخدم الحالي');
        }

        if ($admin->role !== 'admin') {
            abort(404);
        }

        $admin->delete();

        return redirect()->route('admins.index')->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:users,id'],
        ]);

        $ids = collect($data['ids'])->reject(fn ($id) => $id == Auth::id());

        User::whereIn('id', $ids)->where('role', 'admin')->delete();

        return redirect()->route('admins.index')->with('success', 'تم حذف المستخدمين المحددين');
    }
}
