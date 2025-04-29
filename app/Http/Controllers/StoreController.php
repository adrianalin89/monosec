<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Module;
use App\Models\ServerInfo;
use App\Models\StoreStat;
use App\Models\StoreSecurityStatus;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Http\Requests\StoreStoreRequest;

class StoreController extends Controller
{
    /**
     * Display a listing of the stores.
     */
    public function index(Request $request)
    {
        $query = Store::with('client');

        // Apply filters
        if ($request->has('url')) {
            $query->where('url', 'like', '%' . $request->input('url') . '%');
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->has('platform_type')) {
            $query->where('platform_type', $request->input('platform_type'));
        }

        if ($request->has('company')) {
            $query->whereHas('client', function($q) use ($request) {
                $q->where('company_name', 'like', '%' . $request->input('company') . '%');
            });
        }

        $stores = $query->paginate(10);

        return view('stores.index', compact('stores'));
    }

    /**
     * Display the specified store.
     */
    public function show(Store $store)
    {
        $store->load('client');

        return view('stores.show', compact('store'));
    }

    /**
     * Display the modules for the specified store.
     */
    public function showModules(Store $store)
    {
        $modules = Module::where('store_id', $store->id)
            ->orderBy('name')
            ->paginate(20);

        return view('stores.modules', compact('store', 'modules'));
    }

    /**
     * Display the server information for the specified store.
     */
    public function showServerInfo(Store $store)
    {
        $serverInfo = ServerInfo::where('store_id', $store->id)->first();

        return view('stores.server_info', compact('store', 'serverInfo'));
    }

    /**
     * Display the statistics for the specified store.
     */
    public function showStats(Store $store)
    {
        $stats = StoreStat::where('store_id', $store->id)->first();

        return view('stores.stats', compact('store', 'stats'));
    }

    /**
     * Display the security status for the specified store.
     */
    public function showSecurity(Store $store)
    {
        $securityController = new SecurityMonitorController();
        $securityStatus = $securityController->getStoreSecurityStatus($store->id);

        return view('stores.security', compact('store', 'securityStatus'));
    }

    /**
     * Show the form for creating a new store.
     */
    public function create()
    {
        $clients = Client::orderBy("name")->get();
        return view("stores.create", compact("clients"));
    }

    /**
     * Store a newly created store in storage.
     */
    public function store(StoreStoreRequest $request)
    {
        $validated = $request->validated();

        $store = Store::create($validated);

        // Optionally create related empty records if needed
        // ServerInfo::create(["store_id" => $store->id]);
        // StoreStat::create(["store_id" => $store->id]);

        return redirect()->route("stores.show", $store)
            ->with("success", "Magazinul a fost adăugat cu succes.");
    }
}
