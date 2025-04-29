# Schema Bază de Date pentru Sistemul de Monitorizare Magento

## Tabele principale

### 1. Clients (Clienți)
- `id` - bigint, primary key, auto increment
- `name` - varchar(255), numele clientului
- `email` - varchar(255), email-ul clientului
- `company_name` - varchar(255), numele companiei
- `api_key` - varchar(64), cheia API unică pentru client
- `has_credentials` - boolean, indicator dacă avem credențiale în managerul de parole
- `created_at` - timestamp
- `updated_at` - timestamp

### 2. Stores (Magazine)
- `id` - bigint, primary key, auto increment
- `client_id` - bigint, foreign key către clients.id
- `url` - varchar(255), URL-ul magazinului
- `admin_path` - varchar(255), calea către admin
- `platform_type` - enum('magento_ce', 'mage-os', 'magento_ee'), tipul platformei
- `magento_version` - varchar(20), versiunea Magento
- `repository_url` - varchar(255), URL-ul repository-ului
- `contact_info` - text, informații de contact
- `developer_info` - varchar(255), informații despre developer
- `has_cpanel` - boolean, indicator dacă are cPanel
- `has_root_access` - boolean, indicator dacă are acces root
- `last_check` - timestamp, ultima verificare
- `created_at` - timestamp
- `updated_at` - timestamp

### 3. Modules (Module)
- `id` - bigint, primary key, auto increment
- `store_id` - bigint, foreign key către stores.id
- `name` - varchar(255), numele modulului
- `version` - varchar(50), versiunea modulului
- `is_active` - boolean, indicator dacă modulul este activ
- `created_at` - timestamp
- `updated_at` - timestamp

### 4. ServerInfo (Informații Server)
- `id` - bigint, primary key, auto increment
- `store_id` - bigint, foreign key către stores.id
- `os_info` - varchar(255), informații despre sistemul de operare
- `php_version` - varchar(20), versiunea PHP
- `composer_version` - varchar(20), versiunea Composer
- `redis_version` - varchar(20), versiunea Redis
- `opensearch_version` - varchar(20), versiunea OpenSearch
- `mariadb_version` - varchar(20), versiunea MariaDB
- `rabbitmq_version` - varchar(20), versiunea RabbitMQ
- `other_info` - text, alte informații
- `created_at` - timestamp
- `updated_at` - timestamp

### 5. StoreStats (Statistici Magazin)
- `id` - bigint, primary key, auto increment
- `store_id` - bigint, foreign key către stores.id
- `customer_count` - integer, numărul de clienți
- `order_count` - integer, numărul de comenzi
- `created_at` - timestamp
- `updated_at` - timestamp

### 6. SecurityPatches (Patch-uri de Securitate)
- `id` - bigint, primary key, auto increment
- `magento_version` - varchar(20), versiunea Magento afectată
- `patch_name` - varchar(255), numele patch-ului
- `release_date` - date, data lansării
- `type` - enum('security', 'feature'), tipul patch-ului
- `severity_score` - tinyint, scorul de severitate (0-10)
- `severity_level` - enum('lithe', 'critical', 'severe'), nivelul de severitate
- `description` - text, descrierea patch-ului
- `created_at` - timestamp
- `updated_at` - timestamp

### 7. StoreSecurityStatus (Status Securitate Magazin)
- `id` - bigint, primary key, auto increment
- `store_id` - bigint, foreign key către stores.id
- `security_patch_id` - bigint, foreign key către security_patches.id
- `is_applied` - boolean, indicator dacă patch-ul este aplicat
- `risk_score` - tinyint, scorul de risc (0-10)
- `notes` - text, note suplimentare
- `created_at` - timestamp
- `updated_at` - timestamp

### 8. Users (Utilizatori Sistem)
- `id` - bigint, primary key, auto increment
- `name` - varchar(255), numele utilizatorului
- `email` - varchar(255), email-ul utilizatorului
- `password` - varchar(255), parola hash
- `remember_token` - varchar(100), token pentru "remember me"
- `created_at` - timestamp
- `updated_at` - timestamp

## Relații între tabele

1. Un **Client** poate avea mai multe **Magazine** (one-to-many)
2. Un **Magazin** poate avea mai multe **Module** (one-to-many)
3. Un **Magazin** are o singură înregistrare de **Informații Server** (one-to-one)
4. Un **Magazin** are o singură înregistrare de **Statistici Magazin** (one-to-one)
5. Un **Patch de Securitate** poate fi asociat cu mai multe **Status-uri de Securitate Magazin** (one-to-many)
6. Un **Magazin** poate avea mai multe **Status-uri de Securitate** (one-to-many)

Această schemă acoperă toate cerințele pentru stocarea informațiilor despre clienți, magazine Magento, module instalate, informații despre server, statistici și monitorizarea securității.
