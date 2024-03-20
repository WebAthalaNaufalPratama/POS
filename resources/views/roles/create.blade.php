@extends('layouts.app-von')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Add new role</h4>
        <h6>Add new role and assign permissions</h6>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">

                    <div class="container mt-4">

                        @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('roles.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input value="{{ old('name') }}" type="text" class="form-control" name="name" placeholder="Name" required>
                            </div>

                            <label for="permissions" class="form-label">Assign Permissions</label>

                            <div class="table-responsive">
                                <table class="table datanew">
                                    <thead>
                                        <th scope="col" width="1%"><input type="checkbox" name="all_permission"></th>
                                        <th scope="col" width="20%">Name</th>
                                        <th scope="col" width="1%">Guard</th>
                                    </thead>

                                    @foreach($permissions as $permission)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="permission[{{ $permission->name }}]" value="{{ $permission->name }}" class='permission'>
                                        </td>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->guard_name }}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>

                            <button type="submit" class="btn btn-submit me-2 mt-3">Save user</button>
                            <a href="{{ route('roles.index') }}" class="btn btn-cancel mt-3">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('[name="all_permission"]').on('click', function() {

            if ($(this).is(':checked')) {
                $.each($('.permission'), function() {
                    $(this).prop('checked', true);
                });
            } else {
                $.each($('.permission'), function() {
                    $(this).prop('checked', false);
                });
            }

        });
    });
</script>
@endsection