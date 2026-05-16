# Requirements Document

## Introduction

This document specifies the requirements for refactoring the TrainingHubPerformancePusher service to provide real-time, performance-based training recommendations. The system will remove dependency on the Notifix service and query actual performance data directly from the database to recommend relevant training materials based on detected performance issues.

## Glossary

- **System**: The TrainingHubPerformancePusher service and associated API endpoints
- **User**: An authenticated staff member accessing the application
- **Recommendation**: A structured data object containing performance issue details and suggested training materials
- **Performance_Issue**: A detected condition indicating suboptimal performance (loan defaults, low client count, or staff turnover)
- **Training_Material**: Educational content stored as GeneralUpload or GeneralTopic records
- **Active_Client**: A client record where deleted_at IS NULL
- **Defaulted_Loan**: A loan with status = 'defaulted' OR first_repayment_date < CURRENT_DATE
- **Frontend_Modal**: A UI component that displays recommendations to the user
- **API_Endpoint**: The /api/training-recommendations route

## Requirements

### Requirement 1: Performance Data Retrieval

**User Story:** As a system administrator, I want the system to query actual performance data from the database, so that recommendations are based on current, accurate information.

#### Acceptance Criteria

1. WHEN the System receives a request for recommendations, THE System SHALL query the loans table for Defaulted_Loan records associated with the user
2. WHEN the System receives a request for recommendations, THE System SHALL query the clients table for Active_Client records associated with the user
3. WHEN the System receives a request for recommendations, THE System SHALL query the users table for staff count at the user's office where role_id = 4
4. THE System SHALL execute all performance queries without modifying database state

### Requirement 2: Loan Default Detection

**User Story:** As a loan officer, I want to receive training recommendations when I have defaulted or overdue loans, so that I can improve my vetting and due diligence practices.

#### Acceptance Criteria

1. WHEN a user has one or more Defaulted_Loan records, THE System SHALL generate a recommendation of type 'perf_loan_default'
2. WHEN a user has zero Defaulted_Loan records, THE System SHALL NOT generate a loan default recommendation
3. WHEN generating a loan default recommendation, THE System SHALL include the count of Defaulted_Loan records in the message
4. WHEN generating a loan default recommendation, THE System SHALL search for Training_Material using keywords: 'loan', 'vetting', 'due diligence', 'credit assessment', 'risk assessment'
5. WHEN generating a loan default recommendation, THE System SHALL set the label to 'Vetting & Due Diligence'
6. WHEN generating a loan default recommendation, THE System SHALL set the icon to 'fa-exclamation-triangle'
7. WHEN generating a loan default recommendation, THE System SHALL set the color to '#e74c3c'

### Requirement 3: Low Client Count Detection

**User Story:** As a staff member, I want to receive training recommendations when my active client count is low, so that I can improve my client management and retention skills.

#### Acceptance Criteria

1. WHEN a user has fewer than 15 Active_Client records, THE System SHALL generate a recommendation of type 'perf_low_clients'
2. WHEN a user has 15 or more Active_Client records, THE System SHALL NOT generate a low client count recommendation
3. WHEN generating a low client count recommendation, THE System SHALL include the actual client count in the message
4. WHEN generating a low client count recommendation, THE System SHALL search for Training_Material using keywords: 'client', 'customer service', 'client retention', 'relationship management'
5. WHEN generating a low client count recommendation, THE System SHALL set the label to 'Client Management'
6. WHEN generating a low client count recommendation, THE System SHALL set the icon to 'fa-user-plus'
7. WHEN generating a low client count recommendation, THE System SHALL set the color to '#3498db'

### Requirement 4: Staff Turnover Detection

**User Story:** As a manager, I want to receive training recommendations when my office has low staff count, so that I can improve my leadership and staff retention practices.

#### Acceptance Criteria

1. WHEN a user has an office_id AND the office has fewer than 15 users with role_id = 4, THE System SHALL generate a recommendation of type 'perf_staff_turnover'
2. WHEN a user has no office_id, THE System SHALL NOT generate a staff turnover recommendation
3. WHEN a user's office has 15 or more users with role_id = 4, THE System SHALL NOT generate a staff turnover recommendation
4. WHEN generating a staff turnover recommendation, THE System SHALL include the actual staff count in the message
5. WHEN generating a staff turnover recommendation, THE System SHALL search for Training_Material using keywords: 'leadership', 'management', 'team building', 'staff retention'
6. WHEN generating a staff turnover recommendation, THE System SHALL set the label to 'Leadership & Management'
7. WHEN generating a staff turnover recommendation, THE System SHALL set the icon to 'fa-users'
8. WHEN generating a staff turnover recommendation, THE System SHALL set the color to '#f39c12'

### Requirement 5: Training Material Search

**User Story:** As a user, I want the system to recommend relevant training materials for my performance issues, so that I can access targeted learning resources.

#### Acceptance Criteria

1. WHEN searching for training uploads by keywords, THE System SHALL query the general_uploads table using LIKE pattern matching on the name field
2. WHEN searching for training uploads by keywords, THE System SHALL combine multiple keywords using OR logic
3. WHEN searching for training uploads, THE System SHALL order results by views_count in descending order
4. WHEN searching for training uploads, THE System SHALL limit results to a maximum of 5 records
5. WHEN searching for training topics by keywords, THE System SHALL query the general_topics table using LIKE pattern matching on name and description fields
6. WHEN searching for training topics by keywords, THE System SHALL combine multiple keywords using OR logic
7. WHEN searching for training topics, THE System SHALL limit results to a maximum of 3 records

### Requirement 6: Recommendation Structure

**User Story:** As a frontend developer, I want recommendations to have a consistent structure, so that I can reliably display them in the UI.

#### Acceptance Criteria

1. THE System SHALL include a type field in each recommendation with values from the set {'perf_loan_default', 'perf_low_clients', 'perf_staff_turnover'}
2. THE System SHALL include a non-empty label field in each recommendation
3. THE System SHALL include a non-empty icon field in each recommendation
4. THE System SHALL include a color field in each recommendation formatted as a hexadecimal color code
5. THE System SHALL include a non-empty message field in each recommendation
6. THE System SHALL include a link field in each recommendation containing a valid URL
7. THE System SHALL include an uploads field in each recommendation containing a Collection of GeneralUpload models
8. THE System SHALL include a topics field in each recommendation containing a Collection of GeneralTopic models

### Requirement 7: API Endpoint

**User Story:** As a frontend developer, I want a dedicated API endpoint for fetching recommendations, so that I can retrieve them asynchronously after page load.

#### Acceptance Criteria

1. THE System SHALL provide a GET endpoint at /api/training-recommendations
2. WHEN the API_Endpoint receives a request, THE System SHALL authenticate the user
3. WHEN the API_Endpoint receives a request from an authenticated user, THE System SHALL return recommendations for that user's ID
4. WHEN the API_Endpoint successfully processes a request, THE System SHALL return a JSON response with success: true and a recommendations array
5. WHEN the API_Endpoint encounters an error, THE System SHALL return an appropriate HTTP error status code

### Requirement 8: Frontend Integration

**User Story:** As a user, I want to see training recommendations automatically after logging in, so that I am promptly informed of areas where I can improve.

#### Acceptance Criteria

1. WHEN a user loads the master blade template, THE System SHALL wait 5 seconds before making an API request
2. WHEN the 5-second delay completes, THE System SHALL send a GET request to /api/training-recommendations
3. WHEN the API response contains one or more recommendations, THE System SHALL display the Frontend_Modal
4. WHEN the API response contains zero recommendations, THE System SHALL NOT display the Frontend_Modal
5. WHEN displaying recommendations in the Frontend_Modal, THE System SHALL render each recommendation with its icon, label, message, and training materials
6. WHEN displaying recommendations in the Frontend_Modal, THE System SHALL apply the recommendation's color to visual elements
7. WHEN a user dismisses the Frontend_Modal, THE System SHALL hide the modal from view

### Requirement 9: Recommendation Limits

**User Story:** As a system designer, I want to limit the number of recommendations and training materials, so that users are not overwhelmed with information.

#### Acceptance Criteria

1. THE System SHALL return a maximum of 3 recommendations per user (one per performance issue type)
2. THE System SHALL include a maximum of 5 training uploads per recommendation
3. THE System SHALL include a maximum of 3 training topics per recommendation
4. THE System SHALL NOT generate duplicate recommendation types for a single user

### Requirement 10: System Behavior Guarantees

**User Story:** As a system administrator, I want the recommendation system to be reliable and predictable, so that it operates consistently across all users.

#### Acceptance Criteria

1. WHEN the System generates recommendations for a user, THE System SHALL return between 0 and 3 recommendations inclusive
2. WHEN the System is called multiple times with the same user ID and unchanged database state, THE System SHALL return identical results
3. WHEN the System generates recommendations, THE System SHALL NOT modify any database records
4. WHEN the System encounters a user with no office_id, THE System SHALL handle the condition gracefully without errors
5. WHEN the System encounters a user with no performance issues, THE System SHALL return an empty recommendations array
