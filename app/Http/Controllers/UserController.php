<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Search users by name or email via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('search');
        $role = $request->get('role');
        
        $users = User::with('roles')
            ->when($query, function ($q) use ($query) {
                $q->where(function($subQuery) use ($query) {
                    $subQuery->where('name', 'like', '%' . $query . '%')
                             ->orWhere('email', 'like', '%' . $query . '%');
                });
            })
            ->when($role, function ($q) use ($role) {
                $q->whereHas('roles', function ($roleQuery) use ($role) {
                    $roleQuery->where('name', $role);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        if ($request->ajax()) {
            return response()->json([
                'html' => view('users.partials.user-table', compact('users'))->render(),
                'pagination' => view('users.partials.pagination', compact('users'))->render()
            ]);
        }
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
                'password' => ['required', Rules\Password::defaults()],
                'role' => ['required', 'string', 'exists:roles,name']
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Assign role
            $user->assignRole($validated['role']);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully!',
                    'redirect' => route('users.index')
                ]);
            }

            return redirect()->route('users.index')->with('success', 'User created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the user. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withErrors(['general' => 'An error occurred while creating the user. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
                'role' => ['required', 'string', 'exists:roles,name']
            ]);

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            // Sync roles
            $user->syncRoles([$validated['role']]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully!',
                    'redirect' => route('users.index')
                ]);
            }

            return redirect()->route('users.index')->with('success', 'User updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the user. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withErrors(['general' => 'An error occurred while updating the user. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting the current user
            if ($user->id === auth()->user()?->id) {
                return redirect()->back()->with('error', 'You cannot delete your own account.');
            }

            $user->delete();
            return redirect()->route('users.index')->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while deleting the user.');
        }
    }

    /**
     * Restore a soft deleted user
     */
    public function restore($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();
            return redirect()->route('users.index')->with('success', 'User restored successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while restoring the user.');
        }
    }

    /**
     * Permanently delete a user
     */
    public function forceDelete($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            
            // Prevent deleting the current user
            if ($user->id === auth()->user()?->id) {
                return redirect()->back()->with('error', 'You cannot delete your own account.');
            }

            $user->forceDelete();
            return redirect()->route('users.index')->with('success', 'User permanently deleted!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while permanently deleting the user.');
        }
    }

    /**
     * Update user inline via AJAX
     */
    public function updateInline(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!',
                'user' => $user->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the user.'
            ], 500);
        }
    }
} 