# Library Management Project for PGDICT#49 Group A
Project Tree

```
LMS
.
├── app
│   ├── actions
│   │   ├── books
│   │   │   ├── download_ebook.php
│   │   │   ├── download_sample_books_csv.php
│   │   │   ├── import_books_bulk.php
│   │   │   └── search_suggest.php
│   │   ├── loans
│   │   │   ├── admin_process_loan.php
│   │   │   └── request_loan.php
│   │   ├── media
│   │   │   ├── bulk_delete_media.php
│   │   │   └── delete_media.php
│   │   ├── reservations
│   │   │   ├── admin_process_reservation.php
│   │   │   └── request_reservation.php
│   │   ├── returns
│   │   │   ├── admin_process_return.php
│   │   │   └── request_return.php
│   │   ├── system
│   │   │   ├── clear_notifications.php
│   │   │   └── remove_notification.php
│   │   └── users
│   │       ├── admin_process_user.php
│   │       ├── process_reset_password.php
│   │       └── request_password_reset.php
│   ├── includes
│   │   ├── auth.php
│   │   ├── config.php
│   │   ├── connection.php
│   │   ├── footer.php
│   │   ├── footer_resources.php
│   │   ├── header.php
│   │   ├── header_resources.php
│   │   ├── library_helpers.php
│   │   ├── mailer.php
│   │   ├── permission_guard.php
│   │   └── permissions.php
│   ├── modules
│   │   ├── books
│   │   │   ├── book_bulk_import.php
│   │   │   ├── book_category_list.php
│   │   │   ├── book_copy_list.php
│   │   │   ├── book-details.php
│   │   │   ├── book_edition_list.php
│   │   │   ├── book_list.php
│   │   │   ├── bookloader.php
│   │   │   ├── category_list.php
│   │   │   ├── category_view.php
│   │   │   ├── crud
│   │   │   │   ├── add_book_category.php
│   │   │   │   ├── add_book_copy.php
│   │   │   │   ├── add_book_edition.php
│   │   │   │   ├── add_book.php
│   │   │   │   ├── add_category.php
│   │   │   │   ├── add_digital_file.php
│   │   │   │   ├── add_digital_resource.php
│   │   │   │   ├── delete_book_category.php
│   │   │   │   ├── delete_book_copy.php
│   │   │   │   ├── delete_book_edition.php
│   │   │   │   ├── delete_book.php
│   │   │   │   ├── delete_category.php
│   │   │   │   ├── delete_digital_file.php
│   │   │   │   ├── delete_digital_resource.php
│   │   │   │   ├── edit_book_category.php
│   │   │   │   ├── edit_book_copy.php
│   │   │   │   ├── edit_book_edition.php
│   │   │   │   ├── edit_book.php
│   │   │   │   ├── edit_category.php
│   │   │   │   ├── edit_digital_file.php
│   │   │   │   └── edit_digital_resource.php
│   │   │   ├── digital_file_list.php
│   │   │   ├── digital_resource_list.php
│   │   │   ├── home.php
│   │   │   └── search_results.php
│   │   ├── fines
│   │   │   ├── crud
│   │   │   │   ├── add_fine.php
│   │   │   │   ├── add_fine_waiver.php
│   │   │   │   ├── add_payment.php
│   │   │   │   ├── delete_fine.php
│   │   │   │   ├── delete_fine_waiver.php
│   │   │   │   ├── delete_payment.php
│   │   │   │   ├── edit_fine.php
│   │   │   │   ├── edit_fine_waiver.php
│   │   │   │   └── edit_payment.php
│   │   │   ├── fine_list.php
│   │   │   ├── fine_waiver_list.php
│   │   │   └── payment_list.php
│   │   ├── loans
│   │   │   ├── crud
│   │   │   │   ├── add_loan.php
│   │   │   │   ├── delete_loan.php
│   │   │   │   └── edit_loan.php
│   │   │   └── loan_list.php
│   │   ├── reports
│   │   │   ├── crud
│   │   │   ├── library_stock_summary.php
│   │   │   └── reports.php
│   │   ├── reservations
│   │   │   ├── crud
│   │   │   │   ├── add_reservation.php
│   │   │   │   ├── delete_reservation.php
│   │   │   │   └── edit_reservation.php
│   │   │   └── reservation_list.php
│   │   ├── returns
│   │   │   ├── crud
│   │   │   │   ├── add_return.php
│   │   │   │   ├── delete_return.php
│   │   │   │   └── edit_return.php
│   │   │   └── return_list.php
│   │   ├── settings
│   │   │   ├── crud
│   │   │   │   ├── add_holiday.php
│   │   │   │   ├── add_library_policy.php
│   │   │   │   ├── add_policy_change.php
│   │   │   │   ├── add_system_setting.php
│   │   │   │   ├── delete_holiday.php
│   │   │   │   ├── delete_library_policy.php
│   │   │   │   ├── delete_policy_change.php
│   │   │   │   ├── delete_system_setting.php
│   │   │   │   ├── edit_holiday.php
│   │   │   │   ├── edit_library_policy.php
│   │   │   │   ├── edit_policy_change.php
│   │   │   │   └── edit_system_setting.php
│   │   │   ├── crud_check.php
│   │   │   ├── designation.php
│   │   │   ├── erd.php
│   │   │   ├── holiday_list.php
│   │   │   ├── library_policy_list.php
│   │   │   ├── library_rbac_matrix.php
│   │   │   ├── permission_management.php
│   │   │   ├── policy_change_list.php
│   │   │   ├── system_setting_list.php
│   │   │   └── system_settings
│   │   │       ├── home.php
│   │   │       ├── index.php
│   │   │       └── sidebar.php
│   │   ├── system
│   │   │   ├── announcement_list.php
│   │   │   ├── audit_log_list.php
│   │   │   ├── backup_list.php
│   │   │   ├── crud
│   │   │   │   ├── add_announcement.php
│   │   │   │   ├── add_audit_log.php
│   │   │   │   ├── add_backup.php
│   │   │   │   ├── add_notification.php
│   │   │   │   ├── delete_announcement.php
│   │   │   │   ├── delete_audit_log.php
│   │   │   │   ├── delete_backup.php
│   │   │   │   ├── delete_notification.php
│   │   │   │   ├── edit_announcement.php
│   │   │   │   ├── edit_audit_log.php
│   │   │   │   ├── edit_backup.php
│   │   │   │   └── edit_notification.php
│   │   │   ├── gallery_list.php
│   │   │   └── notification_list.php
│   │   └── users
│   │       ├── change_password.php
│   │       ├── crud
│   │       │   ├── add_role.php
│   │       │   ├── add_user.php
│   │       │   ├── add_user_profile.php
│   │       │   ├── add_user_role.php
│   │       │   ├── delete_role.php
│   │       │   ├── delete_user.php
│   │       │   ├── delete_user_profile.php
│   │       │   ├── delete_user_role.php
│   │       │   ├── edit_role.php
│   │       │   ├── edit_user.php
│   │       │   ├── edit_user_profile.php
│   │       │   └── edit_user_role.php
│   │       ├── edit_profile.php
│   │       ├── login.php
│   │       ├── logout.php
│   │       ├── manage_user_role.php
│   │       ├── register.php
│   │       ├── reset_password.php
│   │       ├── role_list.php
│   │       ├── user_dashboard.php
│   │       ├── user_list.php
│   │       ├── user_profile_list.php
│   │       ├── user_role_list.php
│   │       └── view_profile.php
│   └── views
│       ├── dashboard.php
│       ├── data_view.php
│       └── sidebar.php
├── composer.json
├── composer.lock
├── DB
│   ├── ER Diagram.svg
│   ├── LATEST_DB_EXPORT.txt
│   └── lms_db-backup-20260203-181644.sql
├── docs
│   ├── ER Diagram.png
│   ├── ER Diagram.svg
│   ├── lms_db.sql
│   ├── lms-features-matrix.md
│   ├── lms-features.md
│   ├── lms-h-db-audit-version.sql
│   ├── lms-h-db-audit-version-trigger.sql
│   ├── lms-h-db.sql
│   ├── LMS-Landing-page.jpg
│   └── Progect-49(Group-A - Library Management System).txt
├── public
│   ├── actions
│   │   ├── admin_process_loan.php
│   │   ├── admin_process_reservation.php
│   │   ├── admin_process_return.php
│   │   ├── admin_process_user.php
│   │   ├── bulk_delete_media.php
│   │   ├── clear_notifications.php
│   │   ├── delete_media.php
│   │   ├── download_ebook.php
│   │   ├── download_sample_books_csv.php
│   │   ├── import_books_bulk.php
│   │   ├── process_reset_password.php
│   │   ├── remove_notification.php
│   │   ├── request_loan.php
│   │   ├── request_password_reset.php
│   │   ├── request_reservation.php
│   │   ├── request_return.php
│   │   └── search_suggest.php
│   ├── announcement_list.php
│   ├── assets
│   │   ├── css
│   │   │   ├── adminlte.css
│   │   │   ├── adminlte.rtl.css
│   │   │   ├── book-details.css
│   │   │   ├── custom.css
│   │   │   ├── home.css
│   │   │   ├── login-form.css
│   │   │   ├── main.css
│   │   │   └── style.css
│   │   ├── img
│   │   │   ├── AdminLTEFullLogo.png
│   │   │   ├── AdminLTELogo.png
│   │   │   ├── avatar2.png
│   │   │   ├── avatar3.png
│   │   │   ├── avatar4.png
│   │   │   ├── avatar5.png
│   │   │   ├── avatar.png
│   │   │   ├── book1.jpg
│   │   │   ├── book2.jpg
│   │   │   ├── book3.jpg
│   │   │   ├── book4.jpg
│   │   │   ├── boxed-bg.jpg
│   │   │   ├── boxed-bg.png
│   │   │   ├── credit
│   │   │   │   ├── american-express.png
│   │   │   │   ├── cirrus.png
│   │   │   │   ├── mastercard.png
│   │   │   │   ├── paypal2.png
│   │   │   │   ├── paypal.png
│   │   │   │   └── visa.png
│   │   │   ├── default-150x150.png
│   │   │   ├── ER Diagram.svg
│   │   │   ├── icons.png
│   │   │   ├── lms-logo.png
│   │   │   ├── photo1.png
│   │   │   ├── photo2.png
│   │   │   ├── photo3.jpg
│   │   │   ├── photo4.jpg
│   │   │   ├── prod-1.jpg
│   │   │   ├── prod-2.jpg
│   │   │   ├── prod-3.jpg
│   │   │   ├── prod-4.jpg
│   │   │   ├── prod-5.jpg
│   │   │   ├── user1-128x128.jpg
│   │   │   ├── user2-160x160.jpg
│   │   │   ├── user3-128x128.jpg
│   │   │   ├── user4-128x128.jpg
│   │   │   ├── user5-128x128.jpg
│   │   │   ├── user6-128x128.jpg
│   │   │   ├── user7-128x128.jpg
│   │   │   └── user8-128x128.jpg
│   │   └── js
│   │       ├── adminlte.js
│   │       ├── app.js
│   │       ├── custom.js
│   │       ├── home.js
│   │       ├── pages
│   │       │   ├── add_book.js
│   │       │   ├── book_bulk_import.js
│   │       │   ├── dashboard.js
│   │       │   ├── edit_book.js
│   │       │   ├── edit_profile.js
│   │       │   ├── erd.js
│   │       │   ├── index.js
│   │       │   ├── library_rbac_matrix.js
│   │       │   ├── library_stock_summary.js
│   │       │   ├── login.js
│   │       │   ├── permission_management.js
│   │       │   ├── reports.js
│   │       │   └── user_list.js
│   │       └── password_toggle.js
│   ├── audit_log_list.php
│   ├── backup_list.php
│   ├── book_bulk_import.php
│   ├── book_category_list.php
│   ├── book_copy_list.php
│   ├── book-details.php
│   ├── book_edition_list.php
│   ├── book_list.php
│   ├── bookloader.php
│   ├── category_list.php
│   ├── category_view.php
│   ├── change_password.php
│   ├── crud_check.php
│   ├── crud_files
│   │   ├── add_announcement.php
│   │   ├── add_audit_log.php
│   │   ├── add_backup.php
│   │   ├── add_book_category.php
│   │   ├── add_book_copy.php
│   │   ├── add_book_edition.php
│   │   ├── add_book.php
│   │   ├── add_category.php
│   │   ├── add_digital_file.php
│   │   ├── add_digital_resource.php
│   │   ├── add_fine.php
│   │   ├── add_fine_waiver.php
│   │   ├── add_holiday.php
│   │   ├── add_library_policy.php
│   │   ├── add_loan.php
│   │   ├── add_notification.php
│   │   ├── add_payment.php
│   │   ├── add_policy_change.php
│   │   ├── add_reservation.php
│   │   ├── add_return.php
│   │   ├── add_role.php
│   │   ├── add_system_setting.php
│   │   ├── add_user.php
│   │   ├── add_user_profile.php
│   │   ├── add_user_role.php
│   │   ├── delete_announcement.php
│   │   ├── delete_audit_log.php
│   │   ├── delete_backup.php
│   │   ├── delete_book_category.php
│   │   ├── delete_book_copy.php
│   │   ├── delete_book_edition.php
│   │   ├── delete_book.php
│   │   ├── delete_category.php
│   │   ├── delete_digital_file.php
│   │   ├── delete_digital_resource.php
│   │   ├── delete_fine.php
│   │   ├── delete_fine_waiver.php
│   │   ├── delete_holiday.php
│   │   ├── delete_library_policy.php
│   │   ├── delete_loan.php
│   │   ├── delete_notification.php
│   │   ├── delete_payment.php
│   │   ├── delete_policy_change.php
│   │   ├── delete_reservation.php
│   │   ├── delete_return.php
│   │   ├── delete_role.php
│   │   ├── delete_system_setting.php
│   │   ├── delete_user.php
│   │   ├── delete_user_profile.php
│   │   ├── delete_user_role.php
│   │   ├── edit_announcement.php
│   │   ├── edit_audit_log.php
│   │   ├── edit_backup.php
│   │   ├── edit_book_category.php
│   │   ├── edit_book_copy.php
│   │   ├── edit_book_edition.php
│   │   ├── edit_book.php
│   │   ├── edit_category.php
│   │   ├── edit_digital_file.php
│   │   ├── edit_digital_resource.php
│   │   ├── edit_fine.php
│   │   ├── edit_fine_waiver.php
│   │   ├── edit_holiday.php
│   │   ├── edit_library_policy.php
│   │   ├── edit_loan.php
│   │   ├── edit_notification.php
│   │   ├── edit_payment.php
│   │   ├── edit_policy_change.php
│   │   ├── edit_reservation.php
│   │   ├── edit_return.php
│   │   ├── edit_role.php
│   │   ├── edit_system_setting.php
│   │   ├── edit_user.php
│   │   ├── edit_user_profile.php
│   │   └── edit_user_role.php
│   ├── dashboard.php
│   ├── data_view.php
│   ├── designation.php
│   ├── digital_file_list.php
│   ├── digital_resource_list.php
│   ├── edit_profile.php
│   ├── erd.php
│   ├── fine_list.php
│   ├── fine_waiver_list.php
│   ├── gallery_list.php
│   ├── holiday_list.php
│   ├── home.php
│   ├── index.php
│   ├── library_policy_list.php
│   ├── library_rbac_matrix.php
│   ├── library_stock_summary.php
│   ├── loan_list.php
│   ├── login.php
│   ├── logout.php
│   ├── manage_user_role.php
│   ├── notification_list.php
│   ├── payment_list.php
│   ├── permission_management.php
│   ├── policy_change_list.php
│   ├── register.php
│   ├── reports.php
│   ├── reservation_list.php
│   ├── reset_password.php
│   ├── return_list.php
│   ├── role_list.php
│   ├── search_results.php
│   ├── sidebar.php
│   ├── system_setting_list.php
│   ├── system_settings
│   │   ├── home.php
│   │   ├── index.php
│   │   └── sidebar.php
│   ├── uploads
│   │   ├── book_cover
│   │   │   ├── 1769333690_Dan-Brown_Digital-Fortress_book-cover-2025.jpg
│   │   │   ├── 1769508220_Harry-Potter-and-the-sorcorers-stone.jpg
│   │   │   ├── 1769508610_Lord_of_Mysteries.png
│   │   │   ├── 1769520195_Dan-Brown_The_Lost_Symbol_book-cover.jpg
│   │   │   ├── 1769668594_Dan-Brown_The_Vinci_Code_book-cover.jpg
│   │   │   └── 1770013915_best_plagcheckers-3.png
│   │   ├── ebooks
│   │   │   └── 1769776235_Online-Plagirism-Checker.pdf
│   │   └── profile_picture
│   │       ├── 1769424167_avatar.png
│   │       └── 1770014149_avatar.png
│   ├── user_dashboard.php
│   ├── user_list.php
│   ├── user_profile_list.php
│   ├── user_role_list.php
│   └── view_profile.php
├── README.md
├── scripts
│   ├── db_export_precommit.sh
│   ├── install-githooks.sh
│   └── restore_db_on_server.sh
└── vendor
    ├── autoload.php
    ├── composer
    │   ├── autoload_classmap.php
    │   ├── autoload_namespaces.php
    │   ├── autoload_psr4.php
    │   ├── autoload_real.php
    │   ├── autoload_static.php
    │   ├── ClassLoader.php
    │   ├── installed.json
    │   ├── installed.php
    │   ├── InstalledVersions.php
    │   ├── LICENSE
    │   └── platform_check.php
    └── phpmailer
        └── phpmailer
            ├── COMMITMENT
            ├── composer.json
            ├── get_oauth_token.php
            ├── language
            │   └── phpmailer.lang-bn.php
            ├── LICENSE
            ├── README.md
            ├── SECURITY.md
            ├── SMTPUTF8.md
            ├── src
            │   ├── DSNConfigurator.php
            │   ├── Exception.php
            │   ├── OAuth.php
            │   ├── OAuthTokenProvider.php
            │   ├── PHPMailer.php
            │   ├── POP3.php
            │   └── SMTP.php
            └── VERSION
						
```
55 directories, 426 files