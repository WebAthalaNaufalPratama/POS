@extends('layouts.app-von')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Edit new permission</h4>
                <h6>Edit role and manage permissions.</h6>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">

                            <div class="container mt-4">


                                <form method="POST" action="{{ route('permissions.update', $permission->id) }}">
                                    @method('patch')
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input value="{{ $permission->name }}" type="text" class="form-control" name="name" placeholder="Name" required>

                                        @if ($errors->has('name'))
                                        <span class="text-danger text-left">{{ $errors->first('name') }}</span>
                                        @endif
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save permission</button>
                                    <a href="{{ route('permissions.index') }}" class="btn btn-default">Back</a>
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