@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="page-title">
        <h4>Users</h4>
        <h6>Manage your users</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('users.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1">Add Users</a>
    </div>
</div>



<div class="row">
    <div class="col-lg-12 col-sm-12 col-12 d-flex">
        <div class="card flex-fill">
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.partials.messages')
                </div>

                <table class="table datanew">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Username</th>
                            <th scope="col">Roles</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <th scope="row">{{ $user->id }}</th>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->username }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            {{-- <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('users.show', $user->id) }}" onclick="getData({{ $user->id }})">Show</a>
                                        <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}" onclick="getData({{ $user->id }})">Edit</a>
                                        <a class="dropdown-item" href="{{ route('users.destroy', $user->id) }}" onclick="deleteData({{ $user->id }})">Delete</a>
                                    </div>
                                </div>
                            </td> --}}
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('users.show', $user->id) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Show</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('users.edit', $user->id) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $user->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- <div class="d-flex">
                    {!! $users->links() !!}
                </div> -->

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        function deleteData(id){
            Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url: "/user/"+id+"/delete",
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
        });
    }
    </script>
@endsection