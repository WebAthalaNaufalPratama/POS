@extends('layouts.app-von')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Add new users</h4>
                <h6>Add new users and assign permissions.</h6>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">

                            <div class="container mt-4">
                                <form method="POST" action="">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input value="{{ old('name') }}" type="text" class="form-control" name="name" placeholder="Name" required>

                                        @if ($errors->has('name'))
                                        <span class="text-danger text-left">{{ $errors->first('name') }}</span>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input value="{{ old('email') }}" type="email" class="form-control" name="email" placeholder="Email address" required>
                                        @if ($errors->has('email'))
                                        <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input value="{{ old('username') }}" type="text" class="form-control" name="username" placeholder="Username" required>
                                        @if ($errors->has('username'))
                                        <span class="text-danger text-left">{{ $errors->first('username') }}</span>
                                        @endif
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save user</button>
                                    <a href="{{ route('users.index') }}" class="btn btn-default">Back</a>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection