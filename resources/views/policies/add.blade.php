@extends('layouts.master')

@section('content')
<style>
    /* Custom Styles */
    .policy-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    .header-section {
        text-align: center;
        margin-bottom: 3rem;
        padding: 2rem 0;
    }

    .header-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .header-icon i {
        font-size: 2.5rem;
        color: white;
    }

    .header-section h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.5rem;
    }

    .header-section p {
        font-size: 1.125rem;
        color: #718096;
        max-width: 600px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .form-group {
        margin-bottom: 2rem;
    }

    .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.75rem;
        display: block;
    }

    .form-control-lg {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control-lg:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-control-file {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control-file:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .input-group {
        border-radius: 8px;
        overflow: hidden;
    }

    .input-group-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        font-weight: 600;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(160, 174, 192, 0.3);
    }

    .alert {
        border-radius: 8px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .alert-danger {
        background: #fed7d7;
        border-left: 4px solid #fc8181;
    }

    .alert-success {
        background: #c6f6d5;
        border-left: 4px solid #68d391;
    }

    .guidelines-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .guidelines-section h3 {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .guidelines-section .guideline-item {
        margin-bottom: 1rem;
        padding: 0.75rem;
        background: #f7fafc;
        border-radius: 6px;
        border-left: 3px solid rgb(11, 18, 49);
    }

    .guidelines-section .guideline-item p {
        margin: 0;
        color: #4a5568;
        font-size: 1.123rem;
    }

    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .badge.bg-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .badge.bg-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .text-muted {
        color: #718096 !important;
    }

    .text-danger {
        color: #fc8181 !important;
    }

    .text-primary {
        color: #667eea !important;
    }

    @media (max-width: 768px) {
        .header-section h1 {
            font-size: 1.75rem;
        }

        .header-section p {
            font-size: 1rem;
        }

        .form-card {
            padding: 1.5rem;
        }

        .guidelines-section {
            padding: 1.5rem;
        }

        .btn-primary,
        .btn-secondary {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
    }
</style>

<div class="policy-container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-icon">
                <svg style="color:#fff" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-plus-fill" viewBox="0 0 16 16">
                <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M8.5 7v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 1 0"/>
                </svg>        
            </div>
        <h1>Add New Policy Document</h1>
        <p>Create and upload new policy documents with proper categorization and access controls</p>
    </div>

    <!-- Main Form Card -->
    <div class="form-card">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
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
                <div class="col-lg-12 form-group">
                    <label for="category_id" class="form-label">
                        Category <span class="text-danger">*</span>
                    </label>
                    <select name="category_id" id="category_id" class="form-control form-control-lg" required>
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-2">
                        Select the document category (Policies, Procedures, Training Manuals, Forms and Templates).
                    </small>
                </div>

                <!-- Document Title -->
                <div class="col-lg-12 form-group">
                    <label for="title" class="form-label">
                        Document Title <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="title" id="title" class="form-control form-control-lg" value="{{ old('title') }}" required placeholder="Enter policy document title">
                    <small class="text-muted d-block mt-2">
                        Provide a clear and descriptive title for the document.
                    </small>
                </div>

                <!-- Description -->
                <div class="col-lg-12 form-group">
                    <label for="description" class="form-label">
                        Description
                    </label>
                    <textarea name="description" id="description" class="form-control form-control-lg" rows="4" placeholder="Enter policy description (optional)">{{ old('description') }}</textarea>
                </div>

                <!-- Access Level -->
                <div class="col-lg-12 form-group">
                    <label for="access_level" class="form-label">
                        Access Level <span class="text-danger">*</span>
                    </label>
                    <select name="access_level" id="access_level" class="form-control form-control-lg" required>
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

                <!-- Document File -->
                <div class="col-lg-12 form-group">
                    <label for="policy_file" class="form-label">
                        Document File <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="file" class="form-control form-control-lg" name="policy_file" id="policy_file" required>
                        <label class="input-group-text" for="policy_file">
                            <i class="fas fa-file-upload"></i>
                        </label>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Accepted file types: PDF, DOC, DOCX, TXT. Max size: 10MB.
                    </small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3 mt-5">
                <a href="{{ route('policies.view_policies') }}" class="btn btn-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                    </svg> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                    </svg> Upload Document
                </button>
            </div>
        </form>
    </div>

    <!-- Guidelines Section -->
    <div class="guidelines-section">
        <h3>
            <i class="fas fa-lightbulb text-warning mr-2"></i>
            Upload Guidelines
        </h3>
        <div class="row">
            <div class="col-md-6 mb-2">
                <div class="guideline-item">
                    <p>Use clear and concise titles for better searchability</p>
                </div>
            </div>
            <div class="col-md-6 mb-2">
                <div class="guideline-item">
                    <p>Select appropriate category for better organization</p>
                </div>
            </div>
            <div class="col-md-6 mb-2">
                <div class="guideline-item">
                    <p>Provide detailed descriptions for clarity</p>
                </div>
            </div>
            <div class="col-md-6 mb-2">
                <div class="guideline-item">
                    <p>Set correct access levels to maintain security</p>
                </div>
            </div>
        </div>
    </div>
</div>

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
@endsection
