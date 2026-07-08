-- Database Dump for turnero_saas
SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `appointment_types`
--

DROP TABLE IF EXISTS `appointment_types`;
CREATE TABLE `appointment_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` int NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `appointment_types_company_id_foreign` (`company_id`),
  CONSTRAINT `appointment_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=30002;

--
-- Dumping data for table `appointment_types`
--

INSERT INTO `appointment_types` (`id`, `company_id`, `name`, `duration`, `price`, `is_active`, `created_at`, `updated_at`) VALUES ('1', '1', 'Estándar', '30', '0.00', '1', '2026-07-08 10:39:11', '2026-07-08 10:39:11');

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `patient_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `patient_first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `patient_insurance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `patient_dni` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','cancelled','rescheduled','pending_payment') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `cancel_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lock_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locked_until` timestamp NULL DEFAULT NULL,
  `original_date` date DEFAULT NULL,
  `original_time` time DEFAULT NULL,
  `rescheduled_by` bigint unsigned DEFAULT NULL,
  `rescheduled_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `active_slot_unique` varchar(255) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (case when `status` in (_utf8mb4'active',_utf8mb4'rescheduled',_utf8mb4'pending_payment') then concat(`company_id`, _utf8mb4'-', `date`, _utf8mb4'-', `time`) else null end) STORED,
  `professional_id` bigint unsigned DEFAULT NULL,
  `appointment_type_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  UNIQUE KEY `appointments_cancel_token_unique` (`cancel_token`),
  UNIQUE KEY `uq_active_slots` (`active_slot_unique`),
  KEY `appointments_rescheduled_by_foreign` (`rescheduled_by`),
  KEY `appointments_company_id_date_status_index` (`company_id`,`date`,`status`),
  KEY `appointments_patient_phone_index` (`patient_phone`),
  KEY `appointments_patient_id_foreign` (`patient_id`),
  KEY `appointments_professional_id_foreign` (`professional_id`),
  KEY `appointments_appointment_type_id_foreign` (`appointment_type_id`),
  CONSTRAINT `appointments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `appointments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_appointment_type_id_foreign` FOREIGN KEY (`appointment_type_id`) REFERENCES `appointment_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_rescheduled_by_foreign` FOREIGN KEY (`rescheduled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

-- No content for table `appointments`

--
-- Table structure for table `blocked_days`
--

DROP TABLE IF EXISTS `blocked_days`;
CREATE TABLE `blocked_days` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `professional_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  UNIQUE KEY `blocked_days_company_id_date_unique` (`company_id`,`date`),
  KEY `blocked_days_professional_id_foreign` (`professional_id`),
  CONSTRAINT `blocked_days_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blocked_days_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blocked_days`
--

-- No content for table `blocked_days`

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) /*T![clustered_index] CLUSTERED */,
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache_locks`
--

-- No content for table `cache_locks`

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE `companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `professional_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `professional_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consultation_price` decimal(10,2) DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'America/Argentina/Buenos_Aires',
  `primary_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0d6efd',
  `mp_public_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mp_access_token` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cancellation_hours_limit` int unsigned NOT NULL DEFAULT '48',
  `same_patient_rebooking_hours` int unsigned NOT NULL DEFAULT '0',
  `role_permissions` longtext COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  UNIQUE KEY `companies_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=30002;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `slug`, `logo`, `address`, `city`, `state`, `country`, `phone`, `email`, `website`, `professional_name`, `professional_title`, `specialty`, `license_number`, `consultation_price`, `timezone`, `primary_color`, `mp_public_key`, `mp_access_token`, `created_at`, `updated_at`, `cancellation_hours_limit`, `same_patient_rebooking_hours`, `role_permissions`) VALUES ('1', 'NutriSalud Viedma', 'nutrisalud-viedma', 'logos/1V9TgJigNBK3GNNBqhcc0EyoyBw0ZzWYkw0tbs2O.jpeg', 'Av. Caseros 123', 'Viedma', 'Río Negro', 'Argentina', '2920-123456', 'contacto@nutrisalud.com', NULL, 'Dra. María García', 'Lic. en Nutrición', 'Nutrición Deportiva', NULL, '5000.00', 'America/Argentina/Buenos_Aires', '#0d6efd', NULL, NULL, '2026-07-08 10:39:11', '2026-07-08 11:31:11', '48', '0', '{\"staff\":{\"edit_company_info\":false,\"manage_schedules\":true,\"manage_blocked_days\":true,\"create_appointments\":true,\"cancel_appointments\":true},\"doctor\":{\"edit_company_info\":false,\"manage_schedules\":true,\"manage_blocked_days\":true,\"create_appointments\":true,\"cancel_appointments\":true}}');

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

-- No content for table `jobs`

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=30002;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('1', '0001_01_01_000000_create_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('2', '0001_01_01_000001_create_cache_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('3', '0001_01_01_000002_create_jobs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('4', '2026_02_16_200000_create_companies_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('5', '2026_02_16_200001_add_company_id_and_role_to_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('6', '2026_02_16_200002_create_schedule_settings_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('7', '2026_02_16_200003_create_blocked_days_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('8', '2026_02_16_200004_create_appointments_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('9', '2026_07_01_212636_add_mercadopago_fields_to_companies_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('10', '2026_07_02_002603_create_whatsapp_tables', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('11', '2026_07_02_143712_add_unique_slot_index_to_appointments', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('12', '2026_07_02_144441_make_patient_email_nullable_in_appointments_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('13', '2026_07_02_170928_update_appointments_status_enum', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('14', '2026_07_04_141357_add_cancellation_hours_limit_to_companies_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('15', '2026_07_05_110315_add_same_patient_rebooking_hours_to_companies_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('16', '2026_07_07_144806_create_patients_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('17', '2026_07_07_145122_add_patient_id_to_appointments_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('18', '2026_07_07_154019_create_professionals_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('19', '2026_07_07_154058_add_professional_id_and_permissions_to_tables', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('20', '2026_07_08_101500_create_appointment_types_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('21', '2026_07_08_101600_add_appointment_type_id_to_tables', '1');

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) /*T![clustered_index] CLUSTERED */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

-- No content for table `password_reset_tokens`

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
CREATE TABLE `patients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  UNIQUE KEY `patients_company_id_email_unique` (`company_id`,`email`),
  CONSTRAINT `patients_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

-- No content for table `patients`

--
-- Table structure for table `professionals`
--

DROP TABLE IF EXISTS `professionals`;
CREATE TABLE `professionals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `professionals_company_id_foreign` (`company_id`),
  CONSTRAINT `professionals_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=30002;

--
-- Dumping data for table `professionals`
--

INSERT INTO `professionals` (`id`, `company_id`, `name`, `specialty`, `email`, `phone`, `avatar`, `is_active`, `created_at`, `updated_at`) VALUES ('1', '1', 'Dra. María García', 'Nutrición Deportiva', 'contacto@nutrisalud.com', '2920-123456', NULL, '1', '2026-07-08 10:39:11', '2026-07-08 10:39:11');
INSERT INTO `professionals` (`id`, `company_id`, `name`, `specialty`, `email`, `phone`, `avatar`, `is_active`, `created_at`, `updated_at`) VALUES ('2', '1', 'Dr. Mauro Tello', 'Generalista', 'maurotello73@gmail.com', '+542920538998', NULL, '1', '2026-07-08 10:51:55', '2026-07-08 10:51:55');

--
-- Table structure for table `schedule_settings`
--

DROP TABLE IF EXISTS `schedule_settings`;
CREATE TABLE `schedule_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `day_of_week` tinyint NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `slot_duration` int NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `professional_id` bigint unsigned DEFAULT NULL,
  `appointment_type_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `schedule_settings_company_id_foreign` (`company_id`),
  KEY `schedule_settings_professional_id_foreign` (`professional_id`),
  KEY `schedule_settings_appointment_type_id_foreign` (`appointment_type_id`),
  CONSTRAINT `schedule_settings_appointment_type_id_foreign` FOREIGN KEY (`appointment_type_id`) REFERENCES `appointment_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `schedule_settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedule_settings_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=30002;

--
-- Dumping data for table `schedule_settings`
--

INSERT INTO `schedule_settings` (`id`, `company_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration`, `is_active`, `created_at`, `updated_at`, `professional_id`, `appointment_type_id`) VALUES ('1', '1', '1', '08:00:00', '12:00:00', '30', '1', '2026-07-08 10:39:11', '2026-07-08 10:39:11', '1', '1');
INSERT INTO `schedule_settings` (`id`, `company_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration`, `is_active`, `created_at`, `updated_at`, `professional_id`, `appointment_type_id`) VALUES ('2', '1', '2', '08:00:00', '12:00:00', '30', '1', '2026-07-08 10:39:11', '2026-07-08 10:39:11', '1', '1');
INSERT INTO `schedule_settings` (`id`, `company_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration`, `is_active`, `created_at`, `updated_at`, `professional_id`, `appointment_type_id`) VALUES ('3', '1', '3', '08:00:00', '12:00:00', '30', '1', '2026-07-08 10:39:11', '2026-07-08 10:39:11', '1', '1');
INSERT INTO `schedule_settings` (`id`, `company_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration`, `is_active`, `created_at`, `updated_at`, `professional_id`, `appointment_type_id`) VALUES ('4', '1', '4', '08:00:00', '12:00:00', '30', '1', '2026-07-08 10:39:11', '2026-07-08 10:39:11', '1', '1');
INSERT INTO `schedule_settings` (`id`, `company_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration`, `is_active`, `created_at`, `updated_at`, `professional_id`, `appointment_type_id`) VALUES ('5', '1', '5', '08:00:00', '12:00:00', '30', '1', '2026-07-08 10:39:11', '2026-07-08 10:39:11', '1', '1');
INSERT INTO `schedule_settings` (`id`, `company_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration`, `is_active`, `created_at`, `updated_at`, `professional_id`, `appointment_type_id`) VALUES ('6', '1', '3', '08:00:00', '12:00:00', '30', '1', '2026-07-08 10:52:08', '2026-07-08 10:52:08', '2', '1');
INSERT INTO `schedule_settings` (`id`, `company_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration`, `is_active`, `created_at`, `updated_at`, `professional_id`, `appointment_type_id`) VALUES ('7', '1', '4', '08:00:00', '12:00:00', '30', '1', '2026-07-08 10:52:08', '2026-07-08 10:52:08', '2', '1');
INSERT INTO `schedule_settings` (`id`, `company_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration`, `is_active`, `created_at`, `updated_at`, `professional_id`, `appointment_type_id`) VALUES ('8', '1', '5', '08:00:00', '12:00:00', '30', '1', '2026-07-08 10:52:08', '2026-07-08 10:52:08', '2', '1');

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('3d1OYCmYiaU8auLFaVqZnY51NYOuNysemZn91Mf3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMGtoR21sMXR2U0VTTFZ4SzZVb3VqZldReEtaSVJNN0Zod1VUNFRYTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2ginI7czo1OiJyb3V0ZSI7czo1OiJsb2ginI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', '1783527216');
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('UHjuRc7Lg2c09W1VKTH01j8zVM8tpa3bi4LT8m59', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicFg2NmNxQVI4RHFwSGNDdGxSOTJQNkdGaG9UaVhhelk0MldaZkNXYyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9mb3Jnb3QtcGFzc3dvcmQiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO319', '1783510938');

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint unsigned DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `professional_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_company_id_foreign` (`company_id`),
  KEY `users_professional_id_foreign` (`professional_id`),
  CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=30002;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `company_id`, `role`, `professional_id`) VALUES ('1', 'Admin NutriSalud', 'admin@nutrisalud.com', NULL, '$2y$12$wxUUNPmVFt7KXbW0uSPkb.wpd0TzbeQaWNTfKSerzGk/atfW/kgMy', NULL, '2026-07-08 10:39:11', '2026-07-08 10:39:11', '1', 'doctor_admin', '1');

--
-- Table structure for table `whatsapp_business_accounts`
--

DROP TABLE IF EXISTS `whatsapp_business_accounts`;
CREATE TABLE `whatsapp_business_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `phone_number_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `waba_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  UNIQUE KEY `whatsapp_business_accounts_phone_number_id_unique` (`phone_number_id`),
  KEY `whatsapp_business_accounts_company_id_foreign` (`company_id`),
  CONSTRAINT `whatsapp_business_accounts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_business_accounts`
--

-- No content for table `whatsapp_business_accounts`

--
-- Table structure for table `whatsapp_conversations`
--

DROP TABLE IF EXISTS `whatsapp_conversations`;
CREATE TABLE `whatsapp_conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `patient_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inicio',
  `context_json` longtext COLLATE utf8mb4_bin DEFAULT NULL,
  `last_message_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  UNIQUE KEY `whatsapp_conversations_company_id_patient_phone_unique` (`company_id`,`patient_phone`),
  CONSTRAINT `whatsapp_conversations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_conversations`
--

-- No content for table `whatsapp_conversations`

--
-- Table structure for table `whatsapp_message_logs`
--

DROP TABLE IF EXISTS `whatsapp_message_logs`;
CREATE TABLE `whatsapp_message_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `whatsapp_conversation_id` bigint unsigned DEFAULT NULL,
  `whatsapp_message_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direction` enum('inbound','outbound') COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  UNIQUE KEY `whatsapp_message_logs_whatsapp_message_id_unique` (`whatsapp_message_id`),
  KEY `whatsapp_message_logs_company_id_foreign` (`company_id`),
  KEY `whatsapp_message_logs_whatsapp_conversation_id_foreign` (`whatsapp_conversation_id`),
  CONSTRAINT `whatsapp_message_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `whatsapp_message_logs_whatsapp_conversation_id_foreign` FOREIGN KEY (`whatsapp_conversation_id`) REFERENCES `whatsapp_conversations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_message_logs`
--

-- No content for table `whatsapp_message_logs`

SET FOREIGN_KEY_CHECKS = 1;
