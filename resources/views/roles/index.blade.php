@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="page-title">
        <h4>Roles</h4>
        <h6>Manage your roles</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('roles.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1">Add Roles</a>
    </div>
</div>

<div class="mt-2">
    @include('layouts.partials.messages')
</div>

<div class="row">
    <div class="col-lg-12 col-sm-12 col-12 d-flex">
        <div class="card flex-fill">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    @foreach ($roles as $key => $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('roles.edit', $role->id) }}" onclick="getData({{ $role->id }})" >Edit</a>
                                    <a class="dropdown-item" href="{{ route('roles.destroy', $role->id) }}" onclick="deleteData({{ $role->id }})">Delete</a>
                                </div>
                            </div>
                        </td>
                        <!-- <td>
                            <a class="btn btn-info btn-sm" href="{{ route('roles.show', $role->id) }}">Show</a>
                        </td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="{{ route('roles.edit', $role->id) }}">Edit</a>
                        </td> -->
                        <!-- <td>
                            {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id], 'style' => 'display:inline']) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                            {!! Form::close() !!}
                        </td> -->
                    </tr>
                    @endforeach
                    </table>
                    <!-- <div class="d-flex">
                        {!! $roles->links() !!}
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        function deleteData(id){
        $.ajax({
            type: "GET",
            url: "/role/"+id+"/delete",
            success: function(response) {
                toastr.success(response.msg, 'Success', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });

                setTimeout(() => {
                    location.reload()
                }, 2000);
            },
            error: function(error) {
                toastr.error(JSON.parse(error.responseText).msg, 'Error', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
            }
        });
    }
    </script>
@endsection