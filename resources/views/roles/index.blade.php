@extends('layouts.app-von')

@section('content')
    
    
<div class="page-wrapper">
    <div class="content">
        <h1 class="mb-3">Roles</h1>
        <div class="lead"></div>

        <div class="mt-2">
            @include('layouts.partials.messages')
        </div>

        <div class="row">
            <div class="col-lg-12 col-sm-12 col-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm float-right mb-3">Add role</a>
                        <div class="table-responsive dataview">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="1%">No</th>
                                    <th>Name</th>
                                    <th width="3%" colspan="3">Action</th>
                                </tr>
                                @foreach ($roles as $key => $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            <a class="btn btn-info btn-sm" href="{{ route('roles.show', $role->id) }}">Show</a>
                                        </td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" href="{{ route('roles.edit', $role->id) }}">Edit</a>
                                        </td>
                                        <td>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id], 'style' => 'display:inline']) !!}
                                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                            <div class="d-flex">
                                {!! $roles->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
 
