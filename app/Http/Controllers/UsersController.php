<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\UserCreatedMail;

class UsersController extends Controller
{
    public function index() 
    {
        $users = User::latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show form for creating user
     * 
     * @return \Illuminate\Http\Response
     */
    public function create() 
    {
        return view('users.create');
    }

    /**
     * Store a newly created user
     * 
     * @param User $user
     * @param StoreUserRequest $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request) 
    {
        // Check if the email already exists
        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()
                ->withErrors(['email' => 'The email address is already in use.'])
                ->withInput();
        }
    
        // Get or generate a random password
        $password = $request->input('password') ?? Str::random(12);
    
        // Create the user with the validated data and the provided/generated password
        $user = User::create(array_merge($request->validated(), [
            'password' => $password,  // Using Hash::make for hashing the password
        ]));
    
        // Send the password to the user's email
        // Mail::to($user->email)->send(new UserCreatedMail($user, $password));
    
        return redirect()->route('users.index')
            ->withSuccess(__('User created successfully.'));
    }

    /**
     * Show user data
     * 
     * @param User $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function show(User $user) 
    {
        return view('users.show', [
            'user' => $user
        ]);
    }

    /**
     * Edit user data
     * 
     * @param User $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user) 
    {
        return view('users.edit', [
            'user' => $user,
            'userRole' => $user->roles->pluck('name')->toArray(),
            'roles' => Role::latest()->get()
        ]);
    }

    /**
     * Update user data
     * 
     * @param User $user
     * @param UpdateUserRequest $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, UpdateUserRequest $request) 
    {
        $user->update($request->validated());

        $user->syncRoles($request->get('role'));

        return redirect()->route('users.index')
            ->withSuccess(__('User updated successfully.'));
    }

    /**
     * Delete user data
     * 
     * @param User $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy($user) 
    {
        $data = User::find($user);
        // dd($data);

        $activity = Activity::create([
            'log_name' => 'default',
            'description' => 'Deleted role: ' . $data->name,
            'subject_type' => User::class,
            'subject_id' => $data->id,
            'causer_type' => Auth::user() ? get_class(Auth::user()) : null,
            'causer_id' => Auth::id(),
        ]);

        
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return redirect()->route('users.index')
            ->withSuccess(__('User deleted successfully.'));
    }

    public function edit_profile(){
        $idUser = Auth::user();
        $user = User::where('id', $idUser->id)->first();
        return view('users.profile', compact('user'));
    }

    public function update_profile(Request $request){
        // Get the current authenticated user
        $user = Auth::user();

        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string', 
        ]);

        // Update user data
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        // Update password if it is provided
        if ($request->filled('password')) {
            $user->password = $request->input('password');
        }

        // Save the updated user data
        if ($user->save()) {
            return redirect()->back()->with('success', 'Berhasil Mengupdate Profile User!');
        } else {
            return redirect()->back()->with('fail', 'Gagal Mengupdate Profile User!');
        }
    }
}
