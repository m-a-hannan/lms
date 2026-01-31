# LMS Feature → Page/Action → Tables Matrix

| Feature | Page/Action | SQL Tables |
| --- | --- | --- |
| Login | `login.php` | `users`, `user_profiles` |
| Logout | `logout.php` | (no DB write) |
| Registration | `register.php` | `users`, `user_profiles` (and possibly `user_roles`) |
| Change password (self) | `change_password.php` | `users` |
| Permission guard / RBAC | `include/auth.php`, `include/permission_guard.php`, `permissions.php` | `roles`, `permissions`, `page_list`, `user_roles`, `users` |
| Password reset request (user) | `login.php`, `actions/request_password_reset.php` | `password_reset_requests`, `users`, `notifications` |
| Admin set temp password | `user_list.php`, `actions/admin_set_user_password.php` | `users`, `password_reset_requests`, `notifications` |
| User list + approve/block/suspend/delete | `user_list.php`, `actions/admin_process_user.php` | `users`, `user_roles`, `user_profiles`, `notifications`, `loans`, `reservations`, `audit_logs` |
| User profiles | `user_profile_list.php`, `edit_profile.php`, `view_profile.php` | `user_profiles`, `users` |
| Roles | `role_list.php` | `roles` |
| User-role assignments | `manage_user_role.php`, `user_role_list.php` | `user_roles`, `roles`, `users` |
| Permissions management | `permission_management.php`, `library_rbac_matrix.php` | `permissions`, `page_list`, `roles` |
| Books | `book_list.php`, `crud_files/add_book.php`, `crud_files/edit_book.php` | `books` |
| Categories | `category_list.php`, `crud_files/add_category.php`, `crud_files/edit_category.php` | `categories`, `book_categories` |
| Book categories mapping | `book_category_list.php`, `crud_files/add_book_category.php`, `crud_files/edit_book_category.php` | `book_categories` |
| Book editions | `book_edition_list.php`, `crud_files/add_book_edition.php`, `crud_files/edit_book_edition.php` | `book_editions` |
| Physical copies | `book_copy_list.php`, `crud_files/add_book_copy.php`, `crud_files/edit_book_copy.php` | `book_copies` |
| Digital resources (ebook metadata) | `digital_resource_list.php`, `crud_files/add_digital_resource.php`, `crud_files/edit_digital_resource.php` | `digital_resources` |
| Digital files (ebook files) | `digital_file_list.php`, `crud_files/add_digital_file.php`, `crud_files/edit_digital_file.php` | `digital_files` |
| Category view page | `category_view.php` | `books`, `categories`, `book_categories`, `book_editions`, `book_copies` |
| Loan request (user) | `actions/request_loan.php` | `loans`, `book_copies`, `book_editions`, `books` |
| Loan approval/reject (admin) | `loan_list.php`, `actions/admin_process_loan.php` | `loans`, `book_copies`, `book_editions`, `books` |
| Return request (user) | `actions/request_return.php` | `returns`, `loans`, `book_copies`, `book_editions`, `books` |
| Return approval/reject (admin) | `return_list.php`, `actions/admin_process_return.php` | `returns`, `loans`, `book_copies`, `book_editions`, `books`, `reservations` |
| Reservation request (user) | `actions/request_reservation.php` | `reservations`, `book_copies`, `book_editions`, `books` |
| Reservation approval/reject (admin) | `reservation_list.php`, `actions/admin_process_reservation.php` | `reservations`, `book_copies`, `books` |
| User dashboard (loan/reservation/return status) | `user_dashboard.php` | `loans`, `reservations`, `returns`, `books`, `book_editions`, `book_copies` |
| Notifications list | `notification_list.php` | `notifications` |
| Remove notification | `actions/remove_notification.php` | `notifications` |
| Clear notifications | `actions/clear_notifications.php` | `notifications` |
| Workflow notifications | `include/library_helpers.php` (called by actions) | `notifications` |
| Home search + suggestions | `home.php`, `actions/search_suggest.php` | `books`, `search_logs` |
| Search results page | `search_results.php` | `books`, `book_editions`, `book_copies`, `categories` (if used), `search_logs` |
| Reports dashboard | `reports.php` | `users`, `user_roles`, `books`, `book_editions`, `book_copies`, `loans`, `reservations`, `returns`, `notifications`, `search_logs`, `payments`, `fines` |
| Reports activity heatmap | `reports.php` | `audit_logs` (if enabled) |
| Fines | `fine_list.php`, `crud_files/add_fine.php`, `crud_files/edit_fine.php` | `fines` |
| Fine waivers | `fine_waiver_list.php`, `crud_files/add_fine_waiver.php`, `crud_files/edit_fine_waiver.php` | `fine_waivers` |
| Payments | `payment_list.php`, `crud_files/add_payment.php`, `crud_files/edit_payment.php` | `payments` |
| Library policies | `library_policy_list.php`, `crud_files/add_library_policy.php`, `crud_files/edit_library_policy.php` | `library_policies` |
| Policy changes | `policy_change_list.php`, `crud_files/add_policy_change.php`, `crud_files/edit_policy_change.php` | `policy_changes` |
| System settings | `system_setting_list.php`, `system_settings/*`, `crud_files/add_system_setting.php`, `crud_files/edit_system_setting.php` | `system_settings` |
| Holidays | `holiday_list.php`, `crud_files/add_holiday.php`, `crud_files/edit_holiday.php` | `holidays` |
| Audit logs | `audit_log_list.php`, `crud_files/add_audit_log.php`, `crud_files/edit_audit_log.php` | `audit_logs` |
| Backups list | `backup_list.php`, `crud_files/add_backup.php`, `crud_files/edit_backup.php` | `backups` |
| Announcements | `announcement_list.php`, `crud_files/add_announcement.php`, `crud_files/edit_announcement.php` | `announcements` |
| ERD view | `erd.php` | (no DB write; reference only) |
| Data view | `data_view.php` | (read-only, varies) |
| CRUD templates | `templates/*.php` | (target table depends on page) |
