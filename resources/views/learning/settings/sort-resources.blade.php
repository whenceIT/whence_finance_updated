@extends('layouts.learning')

@section('title', 'Sort Resources - Whence Learn')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Sort Resources
                <small>Manage topic sort order for {{ $course->title }}</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('learning.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('learning.settings') }}">Settings</a></li>
                <li><a href="{{ route('learning.settings.courses') }}">Manage Courses</a></li>
                <li class="active">Sort Resources</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Course Topics - {{ $course->title }}</h3>
                            <div class="box-tools pull-right">
                                <a href="{{ route('learning.settings.courses') }}" class="btn btn-default btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back to Courses
                                </a>
                            </div>
                        </div>

                        <div class="box-body">
                            @if(count($topics) > 0)
                            <div style="overflow-x: auto;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Training Material ID</th>
                                            <th>Topic Name</th>
                                            <th>Topic Type</th>
                                            <th>File Path</th>
                                            <th>Video File Path</th>
                                            <th>Audio File Path</th>
                                            <th>PDF File Path</th>
                                            <th>PPT File Path</th>
                                            <th>Document File Path</th>
                                            <th>File Name</th>
                                            <th>Duration</th>
                                            <th>Sort Order</th>
                                            <th>Status</th>
                                            <th>View Count</th>
                                            <th>Created At</th>
                                            <th>Updated At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topics as $topic)
                                        <tr>
                                            <td>{{ $topic->id }}</td>
                                            <td>{{ $topic->training_material_id }}</td>
                                            <td>{{ $topic->topic_name }}</td>
                                            <td>{{ ucfirst($topic->topic_type) }}</td>
                                            <td>{{ $topic->file_path }}</td>
                                            <td>{{ $topic->video_file_path }}</td>
                                            <td>{{ $topic->audio_file_path }}</td>
                                            <td>{{ $topic->pdf_file_path }}</td>
                                            <td>{{ $topic->ppt_file_path }}</td>
                                            <td>{{ $topic->document_file_path }}</td>
                                            <td>{{ $topic->file_name }}</td>
                                            <td>{{ $topic->duration }}</td>
                                            <td>
                                                <form action="{{ route('learning.settings.courses.topics.update-sort-order', ['topicId' => $topic->id]) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <input type="number" name="sort_order" value="{{ $topic->sort_order }}" min="0" class="form-control input-sm" style="width: 80px; display: inline-block;">
                                                    <button type="submit" class="btn btn-primary btn-xs" style="margin-left: 5px;">
                                                        <i class="fa fa-save"></i> Save
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <span class="label label-{{ $topic->is_active ? 'success' : 'danger' }}">
                                                    {{ $topic->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($topic->view_count ?? 0) }}</td>
                                            <td>{{ $topic->created_at }}</td>
                                            <td>{{ $topic->updated_at }}</td>
                                            <td>
                                                <a href="{{ route('learning.training-materials.edit-topic', ['topicId' => $topic->id]) }}" class="btn btn-info btn-xs" title="Edit Topic">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="callout callout-info">
                                <h4>No Topics Found</h4>
                                <p>There are no topics available for this course to sort.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('footer-scripts')
    <script>
        // Toastr notifications handled by layout flash message
    </script>
@endsection
