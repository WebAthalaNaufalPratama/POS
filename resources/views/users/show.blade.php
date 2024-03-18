@extends('layouts.app-von')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Show User</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">

                    <div class="container mt-4">

                        <div class="container mt-4">
                            <div>
                                Name: {{ $user->name }}
                            </div>
                            <div>
                                Email: {{ $user->email }}
                            </div>
                            <div>
                                Username: {{ $user->username }}
                            </div>
                        </div>

                    </div>
                    <div class="mt-4">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-submit me-2 mt-3">Edit</a>
                        <a href="{{ route('users.index') }}" class="btn btn-cancel mt-3">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection