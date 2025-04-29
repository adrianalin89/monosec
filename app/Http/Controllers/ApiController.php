<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Store;
use App\Models\Module;
use App\Models\ServerInfo;
use App\Models\StoreStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Validate API key
     */
    public function validateApiKey(Request $request)
    {
        $apiKey = $request->header('X-API-KEY');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is missing'
            ], 401);
        }
        
        $client = Client::where('api_key', $apiKey)->first();
        
        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key'
            ], 401);
        }
        
        return response()->json([
            'success' => true,
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'company' => $client->company_name
            ]
        ]);
    }
    
    /**
     * Register a new Magento store
     */
    public function registerStore(Request $request)
    {
        $apiKey = $request->header('X-API-KEY');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is missing'
            ], 401);
        }
        
        $client = Client::where('api_key', $apiKey)->first();
        
        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key'
            ], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'admin_path' => 'nullable|string',
            'platform_type' => 'nullable|in:magento_ce,mage-os,magento_ee',
            'magento_version' => 'nullable|string|max:20',
            'repository_url' => 'nullable|url',
            'contact_info' => 'nullable|string',
            'developer_info' => 'nullable|string',
            'has_cpanel' => 'boolean',
            'has_root_access' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $storeData = $validator->validated();
        $storeData['client_id'] = $client->id;
        
        // Check if store already exists
        $existingStore = Store::where('client_id', $client->id)
            ->where('url', $storeData['url'])
            ->first();
            
        if ($existingStore) {
            // Update existing store
            $existingStore->update($storeData);
            $store = $existingStore;
            $message = 'Store information updated successfully';
        } else {
            // Create new store
            $store = Store::create($storeData);
            $message = 'Store registered successfully';
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'store_id' => $store->id
        ]);
    }
    
    /**
     * Update store modules
     */
    public function updateModules(Request $request, $storeId)
    {
        $apiKey = $request->header('X-API-KEY');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is missing'
            ], 401);
        }
        
        $client = Client::where('api_key', $apiKey)->first();
        
        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key'
            ], 401);
        }
        
        $store = Store::where('id', $storeId)
            ->where('client_id', $client->id)
            ->first();
            
        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found or not authorized'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'modules' => 'required|array',
            'modules.*.name' => 'required|string',
            'modules.*.version' => 'nullable|string',
            'modules.*.is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Delete existing modules for this store
        Module::where('store_id', $store->id)->delete();
        
        // Add new modules
        foreach ($request->modules as $moduleData) {
            Module::create([
                'store_id' => $store->id,
                'name' => $moduleData['name'],
                'version' => $moduleData['version'] ?? null,
                'is_active' => $moduleData['is_active'] ?? true,
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Modules updated successfully',
            'count' => count($request->modules)
        ]);
    }
    
    /**
     * Update server information
     */
    public function updateServerInfo(Request $request, $storeId)
    {
        $apiKey = $request->header('X-API-KEY');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is missing'
            ], 401);
        }
        
        $client = Client::where('api_key', $apiKey)->first();
        
        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key'
            ], 401);
        }
        
        $store = Store::where('id', $storeId)
            ->where('client_id', $client->id)
            ->first();
            
        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found or not authorized'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'os_info' => 'nullable|string',
            'php_version' => 'nullable|string|max:20',
            'composer_version' => 'nullable|string|max:20',
            'redis_version' => 'nullable|string|max:20',
            'opensearch_version' => 'nullable|string|max:20',
            'mariadb_version' => 'nullable|string|max:20',
            'rabbitmq_version' => 'nullable|string|max:20',
            'other_info' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $serverInfoData = $validator->validated();
        $serverInfoData['store_id'] = $store->id;
        
        // Check if server info already exists
        $serverInfo = ServerInfo::where('store_id', $store->id)->first();
        
        if ($serverInfo) {
            // Update existing server info
            $serverInfo->update($serverInfoData);
            $message = 'Server information updated successfully';
        } else {
            // Create new server info
            ServerInfo::create($serverInfoData);
            $message = 'Server information added successfully';
        }
        
        // Update last check timestamp
        $store->last_check = now();
        $store->save();
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    /**
     * Update store statistics
     */
    public function updateStoreStats(Request $request, $storeId)
    {
        $apiKey = $request->header('X-API-KEY');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is missing'
            ], 401);
        }
        
        $client = Client::where('api_key', $apiKey)->first();
        
        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key'
            ], 401);
        }
        
        $store = Store::where('id', $storeId)
            ->where('client_id', $client->id)
            ->first();
            
        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found or not authorized'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'customer_count' => 'required|integer|min:0',
            'order_count' => 'required|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $statsData = $validator->validated();
        $statsData['store_id'] = $store->id;
        
        // Check if stats already exist
        $stats = StoreStat::where('store_id', $store->id)->first();
        
        if ($stats) {
            // Update existing stats
            $stats->update($statsData);
            $message = 'Store statistics updated successfully';
        } else {
            // Create new stats
            StoreStat::create($statsData);
            $message = 'Store statistics added successfully';
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
