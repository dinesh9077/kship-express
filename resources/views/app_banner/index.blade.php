@extends('layouts.backend.app')
@section('title', 'App Banner')
@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>App Banners</h4>
                                <a href="{{ route('app.banner.create') }}" class="btn btn-primary">Add New Banner</a>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Banner Image</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($banners as $banner)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $banner->title }}</td>
                                                <td>
                                                    @if ($banner->banner_image)
                                                        <img src="{{ asset('storage/' . $banner->banner_image) }}"
                                                            alt="Banner" width="100">
                                                    @endif
                                                </td>
                                                <td><span class="badge badge-{{ $banner->status == 1 ? 'success' : 'danger'}}">{{ $banner->status == 1 ? 'Active' : 'In-active' }}</td>
                                                <td>
                                                    <a href="{{ route('app.banner.edit', $banner->id) }}"
                                                        class="btn btn-sm btn-info">Edit</a>
                                                    <form action="{{ route('app.banner.delete', $banner->id) }}"
                                                        method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure?')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No banners found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
