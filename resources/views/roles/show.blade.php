@extends('layouts.app-von')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>{{ ucfirst($role->name) }} Role</h4>
        <h6>Assigned permissions</h6>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">

                    <div class="container mt-4">

                        <table class="table table-striped">
                            <thead>
                                <th scope="col" width="20%">Name</th>
                                <th scope="col" width="1%">Guard</th>
                            </thead>

                            @foreach($rolePermissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->guard_name }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>


                    <div class="mt-4">
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-submit me-2 mt-3">Edit</a>
                        <a href="{{ route('roles.index') }}" class="btn btn-cancel mt-3">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection