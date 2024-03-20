@extends('layouts.app-von')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Log Activity</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th width="1%">No</th>
                                <th>Nama Log</th>
                                <th>Deskripsi</th>
                                <th>Subject Type</th>
                                <th>Subject ID</th>
                                <th>User</th>
                                <th>ID User</th>
                                <th>Tanggal Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posts as $post)
                            <tr>
                                <td>{{ $post->id }}</td>
                                <td>{{ $post->log_name }}</td>
                                <td>{{ $post->description }}</td>
                                <td>{{ $post->subject_type }}</td>
                                <td>{{ $post->subject_id }}</td>
                                <td>{{ $post->causer_type }}</td>
                                <td>{{ $post->causer_id}}</td>
                                <td>{{ $post->created_at }}</td>
                                <!-- <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('posts.edit', $post->id) }}">Edit</a></li>
                                            <li><a class="dropdown-item" href="{{ route('posts.log',$post->id) }}">Log</a></li>
                                        </ul>
                                    </div>
                                </td> -->
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
