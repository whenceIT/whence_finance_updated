# Requirements Document

## Introduction

This document specifies the requirements for the Audit Checklist Branch Statistics API feature. The system provides a REST API endpoint that retrieves aggregated branch-specific statistics from multiple database tables and returns them in JSON format. These statistics are used to auto-populate fields in the audit checklist form when an office is selected. The feature includes both backend API implementation in Laravel PHP and frontend JavaScript integration for seamless user experience.

## Glossary

- **API_Endpoint**: The REST API route `/risk/branch-stats` that accepts POST requests with office and date parameters
- **Branch_Statistics**: Aggregated numerical data including active loans count, incomplete files count, system collections amount, wallet collections amount, and staff count for a specific office
- **Office**: A physical branch location identified by a unique office_id in the offices table
- **Active_Loan**: A loan record with status='disbursed' and not soft-deleted
- **Incomplete_File**: A client record with at least one required field (first_name, last_name, gender, dob, mobile, address, city, province) that is null or empty string, and who has an active loan
- **System_Collections**: The sum of credit amounts from loan_transactions with transaction_type='repayment', reversed=0, and not soft-deleted, optionally filtered by date range
- **Wallet_Collections**: Placeholder value (currently 0) reserved for future implementation of wallet-based collection tracking
- **Staff_Count**: The count of non-deleted user records associated with a specific office
- **Period_Range**: Optional date range defined by period_start and period_end parameters for filtering time-based statistics
- **CSRF_Token**: Laravel's Cross-Site Request Forgery protection token required for POST requests
- **Form_Fields**: HTML input elements in the audit checklist form that display the retrieved statistics

## Requirements

### Requirement 1: API Endpoint Creation

**User Story:** As a risk management system, I want to provide a dedicated API endpoint for branch statistics, so that frontend applications can retrieve aggregated data efficiently.

#### Acceptance Criteria

1. THE API_Endpoint SHALL accept POST requests at route `/risk/branch-stats`
2. THE API_Endpoint SHALL require authentication via sentinel middleware
3. THE API_Endpoint SHALL validate the presence of CSRF_Token in the request
4. THE API_Endpoint SHALL be registered in the Laravel routes configuration

### Requirement 2: Request Parameter Validation

**User Story:** As a backend system, I want to validate all incoming request parameters, so that only valid data is processed and errors are caught early.

#### Acceptance Criteria

1. WHEN a request is received, THE API_Endpoint SHALL validate that office_id is present, is an integer, and exists in the offices table
2. WHEN period_start is provided, THE API_Endpoint SHALL validate it is a valid date in Y-m-d format
3. WHEN period_end is provided, THE API_Endpoint SHALL validate it is a valid date in Y-m-d format
4. WHEN both period_start and period_end are provided, THE API_Endpoint SHALL validate that period_end is after or equal to period_start
5. IF validation fails, THEN THE API_Endpoint SHALL return HTTP status 422 with a JSON response containing success=false and an error message

### Requirement 3: Active Loans Calculation

**User Story:** As an auditor, I want to know the total number of active loans for a branch, so that I can assess the branch's current loan portfolio size.

#### Acceptance Criteria

1. WHEN calculating active loans for an office, THE API_Endpoint SHALL count all loan records where office_id matches the requested office
2. WHEN calculating active loans, THE API_Endpoint SHALL include only loans with status='disbursed'
3. WHEN calculating active loans, THE API_Endpoint SHALL exclude soft-deleted loan records (deleted_at IS NOT NULL)
4. THE API_Endpoint SHALL return the active loans count as field s3_total_active in the response
5. THE s3_total_active value SHALL be a non-negative integer

### Requirement 4: Incomplete Files Calculation

**User Story:** As an auditor, I want to identify clients with incomplete documentation, so that I can assess data quality and compliance risks at the branch.

#### Acceptance Criteria

1. WHEN calculating incomplete files, THE API_Endpoint SHALL identify clients who have at least one active loan at the specified office
2. WHEN evaluating client completeness, THE API_Endpoint SHALL check that all required fields (first_name, last_name, gender, dob, mobile, address, city, province) are not null and not empty strings
3. WHEN a client has any required field that is null or empty, THE API_Endpoint SHALL count that client as having an incomplete file
4. WHEN calculating incomplete files, THE API_Endpoint SHALL exclude soft-deleted client records
5. WHEN calculating incomplete files, THE API_Endpoint SHALL count each distinct client only once regardless of how many active loans they have
6. THE API_Endpoint SHALL return the incomplete files count as field s3_incomplete_files in the response
7. THE s3_incomplete_files value SHALL be a non-negative integer

### Requirement 5: System Collections Calculation

**User Story:** As an auditor, I want to know the total system collections for a branch within a specific period, so that I can verify repayment performance and cash flow.

#### Acceptance Criteria

1. WHEN calculating system collections, THE API_Endpoint SHALL sum the credit column from loan_transactions where office_id matches the requested office
2. WHEN calculating system collections, THE API_Endpoint SHALL include only transactions with transaction_type='repayment'
3. WHEN calculating system collections, THE API_Endpoint SHALL exclude reversed transactions (reversed=1)
4. WHEN calculating system collections, THE API_Endpoint SHALL exclude soft-deleted transaction records
5. WHEN period_start is provided, THE API_Endpoint SHALL include only transactions where date >= period_start
6. WHEN period_end is provided, THE API_Endpoint SHALL include only transactions where date <= period_end
7. WHEN no transactions match the criteria, THE API_Endpoint SHALL return 0.0 for system collections
8. THE API_Endpoint SHALL return the system collections sum as field s4_system_collections in the response
9. THE s4_system_collections value SHALL be a non-negative float

### Requirement 6: Wallet Collections Placeholder

**User Story:** As a system architect, I want to reserve a field for wallet collections, so that future wallet-based collection tracking can be integrated without breaking the API contract.

#### Acceptance Criteria

1. THE API_Endpoint SHALL include field s4_wallet_collections in the response
2. THE s4_wallet_collections value SHALL be set to 0 until wallet collection tracking is implemented
3. THE s4_wallet_collections value SHALL be a non-negative float

### Requirement 7: Staff Count Calculation

**User Story:** As an auditor, I want to know the total number of staff assigned to a branch, so that I can assess staffing levels and resource allocation.

#### Acceptance Criteria

1. WHEN calculating staff count, THE API_Endpoint SHALL count all user records where office_id matches the requested office
2. WHEN calculating staff count, THE API_Endpoint SHALL exclude soft-deleted user records (deleted_at IS NOT NULL)
3. THE API_Endpoint SHALL return the staff count as field s6_total_staff in the response
4. THE s6_total_staff value SHALL be a non-negative integer

### Requirement 8: Response Structure

**User Story:** As a frontend developer, I want a consistent and predictable API response structure, so that I can reliably parse and display the data.

#### Acceptance Criteria

1. WHEN the API request is successful, THE API_Endpoint SHALL return HTTP status 200
2. WHEN the API request is successful, THE API_Endpoint SHALL return a JSON response with success=true
3. WHEN the API request is successful, THE API_Endpoint SHALL include a data object containing all five statistics fields (s3_total_active, s3_incomplete_files, s4_system_collections, s4_wallet_collections, s6_total_staff)
4. WHEN validation fails, THE API_Endpoint SHALL return HTTP status 422 with success=false and a message field containing the validation error
5. IF an exception occurs during processing, THEN THE API_Endpoint SHALL return HTTP status 500 with success=false and a message field containing the error description
6. THE API_Endpoint SHALL not modify any database records (read-only operation)

### Requirement 9: Frontend Office Selection Handler

**User Story:** As a user filling out an audit checklist, I want statistics to load automatically when I select an office, so that I don't have to manually enter data that the system already knows.

#### Acceptance Criteria

1. WHEN the office dropdown value changes, THE Frontend SHALL extract the selected office_id
2. WHEN the office dropdown is cleared (empty value), THE Frontend SHALL clear all statistics fields
3. WHEN a valid office is selected, THE Frontend SHALL extract period_start and period_end values from the form
4. WHEN a valid office is selected, THE Frontend SHALL display a loading indicator
5. WHEN a valid office is selected, THE Frontend SHALL send a POST request to the API_Endpoint with office_id, period_start, period_end, and CSRF_Token

### Requirement 10: Frontend AJAX Request Handling

**User Story:** As a frontend application, I want to handle API responses and errors gracefully, so that users receive appropriate feedback regardless of outcome.

#### Acceptance Criteria

1. WHEN the AJAX request succeeds with success=true, THE Frontend SHALL hide the loading indicator
2. WHEN the AJAX request succeeds with success=true, THE Frontend SHALL populate all five Form_Fields with the corresponding data values
3. WHEN the AJAX request succeeds with success=false, THE Frontend SHALL hide the loading indicator and display the error message to the user
4. IF the AJAX request fails due to network error or server unavailability, THEN THE Frontend SHALL hide the loading indicator and display a user-friendly error message
5. WHEN populating Form_Fields, THE Frontend SHALL format currency values (s4_system_collections, s4_wallet_collections) to 2 decimal places
6. WHEN populating Form_Fields, THE Frontend SHALL add a visual indicator (CSS class) to show fields were auto-filled

### Requirement 11: Error Recovery

**User Story:** As a user, I want clear error messages and the ability to retry, so that temporary issues don't prevent me from completing my work.

#### Acceptance Criteria

1. WHEN an error occurs, THE Frontend SHALL display a descriptive error message to the user
2. WHEN an error occurs, THE Frontend SHALL allow the user to retry by selecting the office again
3. WHEN the API returns a validation error, THE Frontend SHALL display the specific validation message
4. WHEN a network error occurs, THE Frontend SHALL display a generic retry message without exposing technical details

### Requirement 12: Performance and Optimization

**User Story:** As a system administrator, I want the API to respond quickly and handle concurrent requests efficiently, so that users experience minimal delays.

#### Acceptance Criteria

1. THE API_Endpoint SHALL respond within 500 milliseconds for typical office queries
2. THE API_Endpoint SHALL respond within 1000 milliseconds for large office queries
3. THE API_Endpoint SHALL support concurrent requests without data corruption or locking issues
4. WHERE database indexes exist on frequently queried columns (loans.office_id, loans.status, loan_transactions.office_id, loan_transactions.date, users.office_id), THE API_Endpoint SHALL utilize these indexes for query optimization

### Requirement 13: Security and Access Control

**User Story:** As a security administrator, I want the API to enforce authentication and authorization, so that only authorized users can access branch statistics.

#### Acceptance Criteria

1. THE API_Endpoint SHALL require user authentication via sentinel middleware
2. THE API_Endpoint SHALL validate CSRF_Token for all POST requests
3. THE API_Endpoint SHALL return only aggregated statistics without exposing personally identifiable information (PII)
4. THE API_Endpoint SHALL log all access attempts for audit trail purposes
5. WHERE rate limiting is configured, THE API_Endpoint SHALL enforce a maximum of 60 requests per minute per user
