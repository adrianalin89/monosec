<?php

namespace App\Http\Controllers;

use App\Models\SecurityPatch;
use App\Models\Store;
use App\Models\StoreSecurityStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class SecurityMonitorController extends Controller
{
    /**
     * Check for new security patches
     */
    public function checkForUpdates()
    {
        try {
            // Fetch the Adobe security bulletins page for Magento
            $response = Http::get('https://helpx.adobe.com/security/products/magento.html');

            if ($response->successful()) {
                $html = $response->body();

                // Parse the HTML
                $dom = new DOMDocument();
                @$dom->loadHTML($html);
                $xpath = new DOMXPath($dom);

                // Find all security bulletin links
                $bulletins = [];
                $rows = $xpath->query('//table//tr[position()>1]');

                foreach ($rows as $row) {
                    $linkNode = $xpath->query('.//a', $row)->item(0);
                    $dateNodes = $xpath->query('.//td', $row);

                    if ($linkNode && $dateNodes->length >= 3) {
                        $bulletinId = trim($linkNode->textContent);
                        $bulletinUrl = $linkNode->getAttribute('href');
                        $postedDate = trim($dateNodes->item(1)->textContent);
                        $updatedDate = trim($dateNodes->item(2)->textContent);

                        // Extract the bulletin ID (e.g., APSB25-26)
                        if (preg_match('/APSB\d+-\d+/', $bulletinId, $matches)) {
                            $id = $matches[0];

                            $bulletins[] = [
                                'id' => $id,
                                'url' => 'https://helpx.adobe.com' . $bulletinUrl,
                                'posted_date' => $postedDate,
                                'updated_date' => $updatedDate
                            ];
                        }
                    }
                }

                // Process each bulletin
                foreach ($bulletins as $bulletin) {
                    $this->processBulletin($bulletin);
                }

                return [
                    'success' => true,
                    'message' => 'Security patches check completed',
                    'bulletins_processed' => count($bulletins)
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch security bulletins'
            ];
        } catch (\Exception $e) {
            Log::error('Error checking for security updates: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error checking for security updates: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process a security bulletin
     */
    private function processBulletin($bulletin)
    {
        try {
            // Check if bulletin already exists
            $existingPatch = SecurityPatch::where('patch_name', $bulletin['id'])->first();

            if ($existingPatch) {
                // Skip if already processed
                return;
            }

            // Fetch the bulletin details
            $response = Http::get($bulletin['url']);

            if ($response->successful()) {
                $html = $response->body();

                // Parse the HTML
                $dom = new DOMDocument();
                @$dom->loadHTML($html);
                $xpath = new DOMXPath($dom);
                // Extract affected versions
                $affectedVersionsRaw = []; // Store raw strings
                $versionRows = $xpath->query('//h2[contains(text(), "Affected Versions")]/following::table[1]//tr[position()>1]');

                foreach ($versionRows as $row) {
                    $cells = $xpath->query(".//td", $row);
                    if ($cells->length >= 3) {
                        $product = trim($cells->item(0)->textContent);
                        $versionText = trim($cells->item(1)->textContent); // Get the raw version text

                        if (strpos($product, 'Adobe Commerce') !== false || strpos($product, 'Magento') !== false) {
                            // Store the raw text, maybe clean it up slightly
                            $cleanedVersionText = preg_replace("/\s+/", " ", $versionText); // Normalize whitespace
                            if (!empty($cleanedVersionText) && !in_array($cleanedVersionText, $affectedVersionsRaw)) {
                                $affectedVersionsRaw[] = $cleanedVersionText;
                            }
                        }
                    }
                }
                // Extract vulnerability details
                $vulnerabilities = [];
                $vulnRows = $xpath->query('//h2[contains(text(), "Vulnerability Details")]/following::table[1]//tr[position()>1]');

                foreach ($vulnRows as $row) {
                    $cells = $xpath->query('.//td', $row);
                    if ($cells->length >= 9) {
                        $category = trim($cells->item(0)->textContent);
                        $impact = trim($cells->item(1)->textContent);
                        $severity = trim($cells->item(2)->textContent);
                        $cvssScore = trim($cells->item(5)->textContent);
                        $cveNumber = trim($cells->item(7)->textContent);

                        $vulnerabilities[] = [
                            'category' => $category,
                            'impact' => $impact,
                            'severity' => $severity,
                            'cvss_score' => $cvssScore,
                            'cve_number' => $cveNumber
                        ];
                    }
                }

                // Determine severity score (0-10)
                $severityScore = 0;
                $severityLevel = 'lithe';

                foreach ($vulnerabilities as $vulnerability) {
                    // Extract CVSS score if available
                    if (preg_match('/(\d+\.\d+)/', $vulnerability['cvss_score'], $matches)) {
                        $score = (float)$matches[1];
                        $severityScore = max($severityScore, $score);
                    }

                    // Check for critical impacts
                    if (strpos(strtolower($vulnerability['impact']), 'remote code execution') !== false) {
                        $severityScore = 10;
                    } elseif (strpos(strtolower($vulnerability['severity']), 'critical') !== false) {
                        $severityScore = max($severityScore, 8);
                    }
                }

                // Determine severity level
                if ($severityScore >= 7) {
                    $severityLevel = 'severe';
                } elseif ($severityScore >= 4) {
                    $severityLevel = 'critical';
                }

                // Extract release date
                $releaseDate = null;
                if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $bulletin['posted_date'], $matches)) {
                    $releaseDate = $matches[3] . '-' . $matches[1] . '-' . $matches[2];
                }

                // Determine patch type
                $type = 'feature';
                foreach ($vulnerabilities as $vulnerability) {
                    if (strpos(strtolower($vulnerability['severity']), 'critical') !== false ||
                        strpos(strtolower($vulnerability['severity']), 'important') !== false) {
                        $type = 'security';
                        break;
                    }
                }

                // Create security patch record
                $securityPatch = SecurityPatch::create([
                    'magento_version' => implode('; ', $affectedVersionsRaw), // Use raw versions, separated by semicolon
                    'patch_name' => $bulletin['id'],
                    'release_date' => $releaseDate,
                    'type' => $type,
                    'severity_score' => $severityScore,
                    'severity_level' => $severityLevel,
                    'description' => 'Security update for Adobe Commerce and Magento Open Source. ' .
                        count($vulnerabilities) . ' vulnerabilities addressed.'
                ]);

                // Check all stores for this patch
                $this->checkStoresForPatch($securityPatch);
            }
        } catch (\Exception $e) {
            Log::error('Error processing bulletin ' . $bulletin['id'] . ': ' . $e->getMessage());
        }

    }

    /**
     * Check all stores for a specific security patch
     */
    private function checkStoresForPatch(SecurityPatch $patch)
    {
        $stores = Store::all();
        $rawAffectedVersions = explode("; ", $patch->magento_version);
        Log::debug("Checking stores for patch {$patch->patch_name}. Raw affected versions: " . $patch->magento_version);

        foreach ($stores as $store) {
            // Skip if no version info
            if (empty($store->magento_version)) {
                Log::debug("Skipping store {$store->id} (no version info) for patch {$patch->patch_name}.");
                continue;
            }

            $storeVersion = trim($store->magento_version);
            Log::debug("Checking store {$store->id} (version: {$storeVersion}) against patch {$patch->patch_name}.");

            // Check if store version is affected (Simplified check - needs improvement for ranges like \"and earlier\")
            // TODO: Implement a more robust version comparison logic (e.g., using a library)
            $isAffected = false;
            foreach ($rawAffectedVersions as $affectedVersionString) {
                // Simple check: does the raw string contain the store version?
                // This is a basic check and might not be fully accurate for ranges.
                if (str_contains(strtolower($affectedVersionString), strtolower($storeVersion))) {
                    Log::debug("Store {$store->id} version {$storeVersion} FOUND in affected string \"{$affectedVersionString}\". Marking as affected.");
                    $isAffected = true;
                    break;
                }
                // Add a check for simple ranges like "2.4.5 and earlier"
                if (preg_match("/(\d+\.\d+\.\d+(?:-p\d+)?)\s+and\s+earlier/i", $affectedVersionString, $matches)) {
                    $upperBound = $matches[1];
                    // Use version_compare for basic comparison
                    if (version_compare($storeVersion, $upperBound, "<=")) {
                        Log::debug("Store {$store->id} version {$storeVersion} is <= upper bound {$upperBound} from \"{$affectedVersionString}\". Marking as affected.");
                        $isAffected = true;
                        break;
                    }
                }
            }

            if ($isAffected) {
                Log::info("Store {$store->id} is affected by patch {$patch->patch_name}. Creating status record.");
                // Create security status record if not already existing for this store/patch combo
                StoreSecurityStatus::firstOrCreate(
                    [
                        "store_id" => $store->id,
                        "security_patch_id" => $patch->id,
                    ],
                    [
                        "is_applied" => false,
                        "risk_score" => $patch->severity_score,
                        "notes" => "Security patch " . $patch->patch_name . " is available and affects this store."
                    ]
                );
            } else {
                Log::debug("Store {$store->id} (version: {$storeVersion}) is NOT affected by patch {$patch->patch_name}.");
            }
        }
    }

    /**
     * Display security dashboard overview
     */
    public function dashboardOverview()
    {
        $securityPatches = SecurityPatch::orderBy('release_date', 'desc')->get();
        $storeSecurityStatuses = StoreSecurityStatus::with(['store', 'securityPatch'])->get();

        $totalStores = Store::count();
        $storesWithIssues = Store::whereHas('securityStatuses', function($query) {
            $query->where('is_applied', false);
        })->count();

        $highRiskStores = Store::whereHas('securityStatuses', function($query) {
            $query->where('is_applied', false)
                ->where('risk_score', '>=', 7);
        })->count();

        $recentPatches = SecurityPatch::orderBy('release_date', 'desc')
            ->take(5)
            ->get();

        return view('security.overview', compact(
            'securityPatches',
            'storeSecurityStatuses',
            'totalStores',
            'storesWithIssues',
            'highRiskStores',
            'recentPatches'
        ));
    }

    /**
     * Run the check updates command manually
     */
    public function runCheckUpdatesCommand()
    {
        \Artisan::call('security:check-updates');
        $output = \Artisan::output();

        return redirect()->route('security.overview')
            ->with('success', 'Security updates check completed. ' . $output);
    }

    /**
     * Get security status for a store
     */
    public function getStoreSecurityStatus($storeId)
    {
        $store = Store::findOrFail($storeId);

        $securityStatuses = StoreSecurityStatus::with("securityPatch")
            ->where("store_id", $storeId)
            ->get();

        $appliedPatches = $securityStatuses->where("is_applied", true)->count();
        $pendingPatches = $securityStatuses->where("is_applied", false)->count();

        $highRiskPatches = $securityStatuses->where("is_applied", false)
            ->filter(function ($status) {
                return $status->risk_score >= 7;
            })->count();

        $overallRisk = "Low";
        if ($highRiskPatches > 0) {
            $overallRisk = "High";
        } elseif ($pendingPatches > 0) {
            $overallRisk = "Medium";
        }

        return [
            "store" => $store,
            "security_statuses" => $securityStatuses,
            "applied_patches" => $appliedPatches,
            "pending_patches" => $pendingPatches,
            "high_risk_patches" => $highRiskPatches,
            "overall_risk" => $overallRisk
        ];
    }

    /**
     * Mark a security patch as applied
     */
    public function markPatchAsApplied(Request $request, $storeId, $statusId)
    {
        $status = StoreSecurityStatus::where("store_id", $storeId)
            ->where("id", $statusId)
            ->firstOrFail();

        $status->is_applied = true;
        $status->notes = $request->input("notes", $status->notes);
        $status->save();

        return redirect()->back()->with("success", "Security patch marked as applied.");
    }
}
