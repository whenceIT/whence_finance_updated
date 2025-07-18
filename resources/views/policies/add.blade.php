@extends('layouts.master')

@section('content')
<div class="container py-0">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-0 d-flex justify-content-between align-items-center">
                <h2 class="fw-bold mb-0 ">Add New Policy</h2>
            </div>

                <div class="card-body p-5">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-5" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('policies.store_policies') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-15">
                            <label for="title" class="form-label fw-bold d-block mb-10">Policy Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control border-primary py-3 px-3" value="{{ old('title') }}" required>
                            <small class="form-text text-muted mt-3 d-block">Provide a clear and descriptive title for the policy.</small>
                        </div>

                        <div class="mb-15 pt-3">
                            <label for="description" class="form-label fw-bold d-block mb-10">Description</label>
                            <textarea name="description" id="description" class="form-control border-primary py-3 px-3" rows="6">{{ old('description') }}</textarea>
                           
                        </div>

                        <div class="mb-15 pt-3">
                            <label for="policy_file" class="form-label fw-bold d-block mb-10">Policy Document <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="file" class="form-control border-primary py-3" name="policy_file" id="policy_file" required>
                                <label class="input-group-text bg-white py-3" for="policy_file"><i class="fas fa-file-upload"></i></label>
                            </div>
                            <small class="form-text text-muted mt-3 d-block">Accepted file types: PDF, DOC, DOCX, TXT. Max size: 10MB.</small>
                        </div>

                        <div class="d-flex justify-content-end mt-15 pt-3">
                            <button type="submit" class="btn btn-primary px-5 py-3">
                                <i></i> Upload Policy
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Update file input label with selected file name
    document.querySelector('#policy_file').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Choose file...';
        const label = e.target.closest('.input-group').querySelector('.input-group-text');
        label.innerText = fileName;
    });
</script>
@endsection
