@extends('layouts.app-von')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Permissions</h4>
        <h6>Manage your permissions here.</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('permissions.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1">Add Permissions</a>
    </div>
</div>

<div class="mt-2">
    @include('layouts.partials.messages')
</div>

<div class="card-body">
    <div class="table-responsive dataview">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col" width="15%">Name</th>
                    <th scope="col">Guard</th>
                    <th scope="col" colspan="3" width="1%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->guard_name }}</td>
                    <td><a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-info btn-sm">Edit</a></td>
                    <td>
                        {!! Form::open(['method' => 'DELETE','route' => ['permissions.destroy', $permission->id],'style'=>'display:inline']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection