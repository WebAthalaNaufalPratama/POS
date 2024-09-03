@extends('layouts.app-von')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Update Profile</h4>
        <h6>Update Profile Users.</h6>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">

                    <div class="container mt-4">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input value="{{ old('name', $user->name) }}" type="text" class="form-control" name="name" required>

                                @if ($errors->has('name'))
                                <span class="text-danger text-left">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input value="{{ old('email', $user->email) }}" type="email" class="form-control" name="email" placeholder="Email address" required>
                                @if ($errors->has('email'))
                                <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input value="{{ old('username', $user->username) }}" type="text" class="form-control" name="username" placeholder="Username" required>
                                @if ($errors->has('username'))
                                <span class="text-danger text-left">{{ $errors->first('username') }}</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Password">
                                @if ($errors->has('password'))
                                <span class="text-danger text-left">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">Update user</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
