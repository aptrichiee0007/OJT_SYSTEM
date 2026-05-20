<img width="1918" height="962" alt="6" src="https://github.com/user-attachments/assets/9fb1da6a-40b2-4ff5-8497-540cc0232b60" />OJT Student Portal
A comprehensive web-based OJT Student Portal designed to streamline the On-the-Job Training process, allowing students to manage their progress and administrators to oversee deployments, document verification, and performance tracking.

🚀 Key Features
Student Dashboard:

Time Clock: Integrated system to log daily OJT hours.

Progress Tracking: Visual representation of required vs. completed hours.

Document Vault: Secure upload and management of OJT requirements.

Leave Requests: Automated filing and tracking of absence/leave requests.

DTR Generation: One-click "Print to PDF" functionality for Daily Time Records.

Admin Dashboard:

Student Management: Bulk import/export of student masterlists.

Deployment Oversight: Assign students to specific companies and supervisors.

Verification System: Centralized review and approval of student-submitted documents.

Account Recovery: Administrative control over password resets and account status.

Analytics: Visual dashboard overview using Chart.js to monitor deployment status.

📸 Screenshots
<img width="1912" height="960" alt="1" src="https://github.com/user-attachments/assets/48d71b32-6822-415f-a2a2-8b73adeb4d62" />
<img width="1918" height="961" alt="2" src="https://github.com/user-attachments/assets/a9c095f6-4746-4811-9671-d17a9ceb5027" />
<img width="1914" height="967" alt="3" src="https://github.com/user-attachments/assets/49840998-8f29-436a-9ce6-ad4eb0cf2d6b" />
<img width="1918" height="962" alt="6" src="https://github.com/user-attachments/assets/0347d03c-0e8c-45a3-8c07-082f7dd88cfe" />
<img width="1914" height="962" alt="7" src="https://github.com/user-attachments/assets/9d0aefc4-ac9b-4098-9725-9ce9af729b73" />
<img width="1917" height="958" alt="8" src="https://github.com/user-attachments/assets/7ed99405-7006-4138-a7b6-f567ac9dc12b" />
<img width="1912" height="959" alt="4" src="https://github.com/user-attachments/assets/76ced86f-31e2-4960-8640-cec0d5ae4de2" />
<img width="1915" height="962" alt="5" src="https://github.com/user-attachments/assets/47be610b-b656-48d0-ad37-178c3b8754f7" />


🛠 Technical Stack
Backend: PHP

Database: MySQL

Frontend: HTML5, CSS3, JavaScript (with Boxicons for UI and Chart.js for analytics)

⚙️ Setup & Installation
Prerequisites
PHP (v7.4 or higher recommended)

MySQL/MariaDB

Apache or Nginx web server

Database Setup
Create a MySQL database (e.g., ojt_system).

Import the provided schema files located in the /db directory.
(Note: Ensure your database.php file is configured with the correct local credentials.)

Configuration
Update your database configuration in model/database.php:

PHP
private $host = 'localhost';
private $db_name = 'ojt_system';
private $username = 'your_username';
private $password = 'your_password';
Running the Application
Place the project folder in your server's root directory (e.g., htdocs for XAMPP).

Start your Apache and MySQL services.

Access the portal via your browser at http://localhost/ojt_system.

📁 Project Structure
/assets/: CSS styles, JavaScript, and custom icons.

/controller/: PHP logic handling form submissions, actions, and authentication.

/model/: Database connection and data retrieval logic.

/uploads/: Secure storage for student documents and resumes.

student_dashboard.php: Main student interface.

admin_dashboard.php: Main administrative interface.

📝 Usage Notes
Admin Panel: Access is restricted based on user_role. Ensure the 'Admin' role is correctly assigned in the users table.

Bulk Import: When adding students via CSV, ensure your file follows the strictly required format: First Name, Last Name, Email, Password.

PDF Generation: The "Print to PDF" feature uses the deployment ID to fetch and render student-specific timesheets. Ensure print_timesheet.php has appropriate permissions.
