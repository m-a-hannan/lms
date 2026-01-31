# LMS Features Summary

## Features Inventory (Current)

**Authentication & Access**
- Login/logout
- Registration
- Change password
- Admin-set temporary password flow
- Password reset request (user → admin notification → admin sets temp password)

**Users & Roles**
- User list with approve/block/suspend/delete
- User profiles + profile list (with photos)
- Roles, role management, and user-role assignments
- Permission management + RBAC matrix

**Library Catalog & Inventory**
- Books master list
- Categories + book categories
- Book editions
- Physical copies list
- Digital resources + digital files (eBooks)

**Circulation Workflow**
- Loan requests, approvals/rejections, returns
- Reservation requests, approvals/rejections
- Return requests, approvals/rejections
- Admin workflows via actions + edit pages

**Dashboards**
- Admin/Librarian dashboard
- User dashboard

**Search**
- Home search with suggestions
- Search results page
- Suggestion endpoint

**Notifications**
- Notification list
- Dropdown notifications with clear/remove
- System notifications for workflow events

**Fines & Payments**
- Fines list + fine waivers
- Payments list

**Policies & Settings**
- Library policies + policy change list
- System settings pages

**Audit & Backups**
- Audit log list
- Backups list

**Reports**
- Reports dashboard with multiple report widgets/charts

**Other Management**
- Announcements
- Holidays
- Designations
- Data view / CRUD templates
- ERD page

---

## Feature → Pages/Actions → Tables Matrix

### Authentication & Access
- **Login / Logout** → `login.php`, `logout.php` → `users`, `user_profiles`
- **Registration** → `register.php` → `users`, `user_profiles` (and possibly `user_roles`)
- **Change password (self)** → `change_password.php` → `users`
- **Permission guard / RBAC** → `include/auth.php`, `include/permission_guard.php`, `permissions.php` → `roles`, `permissions`, `page_list`, `user_roles`, `users`

### Password Reset (admin-assisted)
- **User request modal** → `login.php`, `actions/request_password_reset.php` → `password_reset_requests`, `users`, `notifications`
- **Admin set temp password** → `user_list.php`, `actions/admin_set_user_password.php` → `users`, `password_reset_requests`, `notifications`

### Users & Roles
- **User list + approve/block/suspend/delete** → `user_list.php`, `actions/admin_process_user.php` → `users`, `user_roles`, `user_profiles`, `notifications`, `loans`, `reservations`, `audit_logs`
- **User profiles** → `user_profile_list.php`, `edit_profile.php`, `view_profile.php` → `user_profiles`, `users`
- **Roles + role assignments** → `role_list.php`, `manage_user_role.php`, `user_role_list.php` → `roles`, `user_roles`
- **Permissions management** → `permission_management.php`, `library_rbac_matrix.php` → `permissions`, `page_list`, `roles`

### Library Inventory / Catalog
- **Books** → `book_list.php`, `crud_files/add_book.php`, `crud_files/edit_book.php` → `books`
- **Categories** → `category_list.php`, `crud_files/add_category.php`, `crud_files/edit_category.php` → `categories`, `book_categories`
- **Editions** → `book_edition_list.php`, `crud_files/add_book_edition.php`, `crud_files/edit_book_edition.php` → `book_editions`
- **Physical copies** → `book_copy_list.php`, `crud_files/add_book_copy.php`, `crud_files/edit_book_copy.php` → `book_copies`
- **Digital resources** → `digital_resource_list.php`, `crud_files/add_digital_resource.php`, `crud_files/edit_digital_resource.php` → `digital_resources`
- **Digital files** → `digital_file_list.php`, `crud_files/add_digital_file.php`, `crud_files/edit_digital_file.php` → `digital_files`
- **Category view page** → `category_view.php` → `books`, `categories`, `book_categories`, `book_editions`, `book_copies`

### Circulation / Workflow
- **Loan request (user)** → `actions/request_loan.php` → `loans`, `book_copies`, `book_editions`, `books`
- **Loan approval/reject (admin)** → `loan_list.php`, `actions/admin_process_loan.php` → `loans`, `book_copies`, `book_editions`, `books`
- **Return request (user)** → `actions/request_return.php` → `returns`, `loans`, `book_copies`, `book_editions`, `books`
- **Return approval/reject (admin)** → `return_list.php`, `actions/admin_process_return.php` → `returns`, `loans`, `book_copies`, `book_editions`, `books`, `reservations`
- **Reservation request (user)** → `actions/request_reservation.php` → `reservations`, `book_copies`, `book_editions`, `books`
- **Reservation approval/reject (admin)** → `reservation_list.php`, `actions/admin_process_reservation.php` → `reservations`, `book_copies`, `books`
- **User dashboard (loan/reservation/return status)** → `user_dashboard.php` → `loans`, `reservations`, `returns`, `books`, `book_editions`, `book_copies`

### Notifications
- **Notifications list** → `notification_list.php` → `notifications`
- **Remove/clear notifications** → `actions/remove_notification.php`, `actions/clear_notifications.php` → `notifications`
- **Workflow notifications (loan/return/reservation, password reset)** → `include/library_helpers.php` (called by actions) → `notifications`

### Search
- **Home search + suggestions** → `home.php`, `actions/search_suggest.php` → `books`, `search_logs`
- **Search results page** → `search_results.php` → `books`, `book_editions`, `book_copies`, `categories` (if used), `search_logs`

### Reports / Analytics
- **Reports dashboard** → `reports.php` → `users`, `user_roles`, `books`, `book_editions`, `book_copies`, `loans`, `reservations`, `returns`, `notifications`, `search_logs`, `payments`, `fines` (depends on which report cards are enabled)
- **Activity heatmap/report data** → `reports.php` → `audit_logs` (if used), plus the above tables

### Fines & Payments
- **Fines** → `fine_list.php`, `crud_files/add_fine.php`, `crud_files/edit_fine.php` → `fines`
- **Fine waivers** → `fine_waiver_list.php`, `crud_files/add_fine_waiver.php`, `crud_files/edit_fine_waiver.php` → `fine_waivers`
- **Payments** → `payment_list.php`, `crud_files/add_payment.php`, `crud_files/edit_payment.php` → `payments`

### Policies & Settings
- **Library policies** → `library_policy_list.php`, `crud_files/add_library_policy.php`, `crud_files/edit_library_policy.php` → `library_policies`
- **Policy changes** → `policy_change_list.php`, `crud_files/add_policy_change.php`, `crud_files/edit_policy_change.php` → `policy_changes`
- **System settings** → `system_setting_list.php`, `system_settings/*`, `crud_files/add_system_setting.php`, `crud_files/edit_system_setting.php` → `system_settings`
- **Holidays** → `holiday_list.php`, `crud_files/add_holiday.php`, `crud_files/edit_holiday.php` → `holidays`

### Auditing / Backups
- **Audit logs** → `audit_log_list.php`, `crud_files/add_audit_log.php`, `crud_files/edit_audit_log.php` → `audit_logs`
- **Backups list** → `backup_list.php`, `crud_files/add_backup.php`, `crud_files/edit_backup.php` → `backups`

### Announcements
- **Announcements** → `announcement_list.php`, `crud_files/add_announcement.php`, `crud_files/edit_announcement.php` → `announcements`

### Misc / Admin Tools
- **ERD view** → `erd.php` → (no DB write; reference only)
- **Data view** → `data_view.php` → (read-only, varies)
- **Templates** → `templates/*.php` → (CRUD scaffolding; target table depends on page)
