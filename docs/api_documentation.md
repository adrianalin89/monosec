# Magento Monitor - Documentație API

## Autentificare API

Toate cererile API trebuie să includă cheia API în header-ul cererii:

```
X-API-KEY: [cheia_api_a_clientului]
```

## Endpoint-uri API

### Validare Cheie API

```
GET /api/validate
```

**Răspuns de succes:**
```json
{
  "success": true,
  "message": "API key is valid",
  "client": {
    "id": 1,
    "name": "Nume Client",
    "company_name": "Companie Client"
  }
}
```

### Înregistrare Magazin

```
POST /api/stores
```

**Parametri:**
```json
{
  "url": "https://magazin.example.com",
  "magento_version": "2.4.5",
  "platform_type": "magento_ce",
  "admin_path": "admin",
  "repository_url": "https://github.com/example/repo",
  "developer_name": "Nume Developer",
  "contact_email": "contact@example.com",
  "contact_phone": "+40123456789"
}
```

**Răspuns de succes:**
```json
{
  "success": true,
  "message": "Store registered successfully",
  "store_id": 1
}
```

### Actualizare Module

```
POST /api/stores/{store_id}/modules
```

**Parametri:**
```json
{
  "modules": [
    {
      "name": "Magento_Catalog",
      "version": "1.0.0",
      "is_active": true
    },
    {
      "name": "Vendor_CustomModule",
      "version": "2.1.3",
      "is_active": false
    }
  ]
}
```

**Răspuns de succes:**
```json
{
  "success": true,
  "message": "Modules updated successfully",
  "modules_count": 2
}
```

### Actualizare Informații Server

```
POST /api/stores/{store_id}/server-info
```

**Parametri:**
```json
{
  "os_version": "Ubuntu 22.04 LTS",
  "php_version": "8.1.12",
  "mysql_version": "MariaDB 10.6.11",
  "composer_version": "2.4.4",
  "redis_version": "6.0.16",
  "elasticsearch_version": "OpenSearch 2.3.0",
  "rabbitmq_version": "3.10.7",
  "web_server": "Nginx 1.22.1"
}
```

**Răspuns de succes:**
```json
{
  "success": true,
  "message": "Server information updated successfully"
}
```

### Actualizare Statistici Magazin

```
POST /api/stores/{store_id}/stats
```

**Parametri:**
```json
{
  "customers_count": 1250,
  "orders_count": 3750,
  "products_count": 5000,
  "categories_count": 120
}
```

**Răspuns de succes:**
```json
{
  "success": true,
  "message": "Store statistics updated successfully"
}
```

## Coduri de Eroare

| Cod | Descriere |
|-----|-----------|
| 401 | Cheie API invalidă sau lipsă |
| 404 | Resursa solicitată nu a fost găsită |
| 422 | Validare eșuată - verificați parametrii |
| 500 | Eroare internă de server |

## Exemple de Utilizare

### Exemplu cURL

```bash
curl -X POST \
  https://magento-monitor.example.com/api/stores \
  -H 'X-API-KEY: your_api_key_here' \
  -H 'Content-Type: application/json' \
  -d '{
    "url": "https://magazin.example.com",
    "magento_version": "2.4.5",
    "platform_type": "magento_ce",
    "admin_path": "admin",
    "repository_url": "https://github.com/example/repo",
    "developer_name": "Nume Developer",
    "contact_email": "contact@example.com",
    "contact_phone": "+40123456789"
  }'
```

### Exemplu PHP

```php
$apiKey = 'your_api_key_here';
$data = [
    'url' => 'https://magazin.example.com',
    'magento_version' => '2.4.5',
    'platform_type' => 'magento_ce',
    'admin_path' => 'admin',
    'repository_url' => 'https://github.com/example/repo',
    'developer_name' => 'Nume Developer',
    'contact_email' => 'contact@example.com',
    'contact_phone' => '+40123456789'
];

$ch = curl_init('https://magento-monitor.example.com/api/stores');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-KEY: ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
```
