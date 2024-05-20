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
                        <td class="text-center">
                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('roles.edit', $role->id) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $role->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
                                </li>
                            </ul>
                        </td>
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
    });
}
</script>
@endsection