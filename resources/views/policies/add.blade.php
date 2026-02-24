@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <div class="d-inline-block bg-primary p-3 rounded-circle mb-3">
                    <i class="fas fa-file-contract text-white fa-2x"></i>
                </div>
                <h1 class="text-dark font-weight-bold mb-2">Add New Policy Document</h1>
                <p class="text-secondary">Create and upload new policy documents with proper categorization and access controls</p>
            </div>

            <!-- Main Form Card -->
            <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
                <div class="card-body p-5">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 rounded-lg shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-danger mr-3"></i>
                                <div>
                                    <h6 class="font-weight-bold text-danger mb-1">Please fix the following errors:</h6>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('policies.store_policies') }}" method="POST" enctype="multipart/form-data" class="needs-validation">
                        @csrf

                        <div class="row">
                            <!-- Category -->
                            <div class="col-lg-12 mb-4">
                                <div class="bg-light p-4 rounded-lg border border-light">
                                    <label for="category_id" class="form-label font-weight-bold text-dark d-block mb-3">
                                        <i class="fas fa-folder-open text-primary mr-2"></i>
                                        Category <span class="text-danger">*</span>
                                    </label>
                                    <select name="category_id" id="category_id" class="form-control form-control-lg rounded-lg border-primary" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle text-primary mr-1"></i> 
                                        Select the document category (Policies, Procedures, Training Manuals, Forms and Templates).
                                    </small>
                                </div>
                            </div>

                            <!-- Document Title -->
                            <div class="col-lg-12 mb-4">
                                <div class="bg-light p-4 rounded-lg border border-light">
                                    <label for="title" class="form-label font-weight-bold text-dark d-block mb-3">
                                        <i class="fas fa-heading text-primary mr-2"></i>
                                        Document Title <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="title" id="title" class="form-control form-control-lg rounded-lg border-primary" value="{{ old('title') }}" required placeholder="Enter policy document title">
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle text-primary mr-1"></i> 
                                        Provide a clear and descriptive title for the document.
                                    </small>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-lg-12 mb-4">
                                <div class="bg-light p-4 rounded-lg border border-light">
                                    <label for="description" class="form-label font-weight-bold text-dark d-block mb-3">
                                        <i class="fas fa-align-left text-primary mr-2"></i>
                                        Description
                                    </label>
                                    <textarea name="description" id="description" class="form-control form-control-lg rounded-lg border-primary" rows="4" placeholder="Enter policy description (optional)">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <!-- Access Level -->
                            <div class="col-lg-12 mb-4">
                                <div class="bg-light p-4 rounded-lg border border-light">
                                    <label for="access_level" class="form-label font-weight-bold text-dark d-block mb-3">
                                        <i class="fas fa-shield-alt text-primary mr-2"></i>
                                        Access Level <span class="text-danger">*</span>
                                    </label>
                                    <select name="access_level" id="access_level" class="form-control form-control-lg rounded-lg border-primary" required>
                                        <option value="">-- Select Access Level --</option>
                                        @foreach ($accessLevels as $value => $label)
                                            <option value="{{ $value }}" {{ old('access_level') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-4">
                                        <div class="d-flex align-items-start mb-2">
                                            <span class="badge bg-success mr-2">All Staff</span>
                                            <span class="text-muted">Visible to all employees</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-warning mr-2">Managerial Only</span>
                                            <span class="text-muted">Visible only to managers, supervisors, and administrators</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Document File -->
                            <div class="col-lg-12 mb-4">
                                <div class="bg-light p-4 rounded-lg border border-light">
                                    <label for="policy_file" class="form-label font-weight-bold text-dark d-block mb-3">
                                        <i class="fas fa-upload text-primary mr-2"></i>
                                        Document File <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="file" class="form-control border-primary rounded-lg" name="policy_file" id="policy_file" required>
                                        <label class="input-group-text bg-primary text-white rounded-lg" for="policy_file">
                                            <i class="fas fa-file-upload"></i>
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle text-primary mr-1"></i> 
                                        Accepted file types: PDF, DOC, DOCX, TXT. Max size: 10MB.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-3 mt-5">
                            <a href="{{ route('policies.view_policies') }}" class="btn btn-secondary btn-lg px-4 py-2 rounded-lg">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-4 py-2 rounded-lg">
                                <i class="fas fa-upload mr-2"></i> Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Guidelines Section -->
            <div class="mt-4 bg-light rounded-lg p-4 border border-light">
                <h3 class="font-weight-bold text-dark mb-3">
                    <i class="fas fa-lightbulb text-warning mr-2"></i>
                    Upload Guidelines
                </h3>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mt-1 mr-3"></i>
                            <div>
                                <p class="text-muted">Use clear and concise titles for better searchability</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mt-1 mr-3"></i>
                            <div>
                                <p class="text-muted">Select appropriate category for better organization</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mt-1 mr-3"></i>
                            <div>
                                <p class="text-muted">Provide detailed descriptions for clarity</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mt-1 mr-3"></i>
                            <div>
                                <p class="text-muted">Set correct access levels to maintain security</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    .bg-primary {
        background-color: #007bff !important;
    }
    
    .text-primary {
        color: #007bff !important;
    }
    
    .border-primary {
        border-color: #007bff !important;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    .text-secondary {
        color: #6c757d !important;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    .text-dark {
        color: #343a40 !important;
    }
    
    .bg-success {
        background-color: #28a745 !important;
    }
    
    .text-success {
        color: #28a745 !important;
    }
    
    .bg-warning {
        background-color: #ffc107 !important;
    }
    
    .text-warning {
        color: #ffc107 !important;
    }
    
    .bg-danger {
        background-color: #dc3545 !important;
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .btn-primary {
        background-color: #007bff !important;
        border-color: #007bff !important;
    }
    
    .btn-primary:hover {
        background-color: #0069d9 !important;
        border-color: #0062cc !important;
    }
    
    .btn-secondary {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268 !important;
        border-color: #545b62 !important;
    }
    
    .btn-success {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }
    
    .btn-success:hover {
        background-color: #218838 !important;
        border-color: #1e7e34 !important;
    }
    
    .btn-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }
    
    .btn-danger:hover {
        background-color: #c82333 !important;
        border-color: #bd2130 !important;
    }
    
    .btn-warning {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
    }
    
    .btn-warning:hover {
        background-color: #e0a800 !important;
        border-color: #d39e00 !important;
    }
    
    .card {
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: 0.375rem;
    }
    
    .shadow-lg {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
    }
    
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
</style>

<script>
    // Update file input label with selected file name
    document.querySelector('#policy_file').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Choose file...';
        const label = e.target.closest('.input-group').querySelector('.input-group-text');
        label.innerHTML = `<i class="fas fa-file-upload mr-2"></i>${fileName}`;
    });

    // Add form validation feedback
    document.querySelector('form').addEventListener('submit', function(e) {
        const inputs = this.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value || input.value === '') {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });

    // Remove validation classes on input change
    document.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid', 'is-valid');
        });
    });
</script>
