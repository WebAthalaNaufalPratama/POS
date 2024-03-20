@extends('layouts.app-von')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ $route }}" method="post">
                        @if ($method == 'PUT') @method('PUT') @endif
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title',$post->title) }}">
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea name="content" id="content" class="form-control">{{ old('content',$post->content) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                        Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection