@extends("layouts.app")

@section("content")
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __("Installed Modules") }}: {{ $store->url }}</span>
                    <a href="{{ route("stores.show", $store) }}" class="btn btn-sm btn-secondary">{{ __("Back to Store") }}</a>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="moduleSearch" class="form-control" placeholder="{{ __("Search modules by name or version...") }}">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped" id="modulesTable">
                            <thead>
                                <tr>
                                    <th>{{ __("Name") }}</th>
                                    <th>{{ __("Version") }}</th>
                                    <th>{{ __("Status") }}</th>
                                    <th>{{ __("Last Updated") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($modules as $module)
                                    <tr>
                                        <td>{{ $module->name }}</td>
                                        <td>{{ $module->version }}</td>
                                        <td>
                                            <span class="badge bg-{{ $module->is_active ? "success" : "secondary" }}">
                                                {{ $module->is_active ? __("Active") : __("Inactive") }}
                                            </span>
                                        </td>
                                        <td>{{ $module->updated_at->format("Y-m-d H:i:s") }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">{{ __("No modules found for this store") }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $modules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("moduleSearch");
    const tableBody = document.getElementById("modulesTable").querySelector("tbody");
    const rows = tableBody.getElementsByTagName("tr");

    searchInput.addEventListener("keyup", function(event) {
        const searchTerm = event.target.value.toLowerCase();

        for (let i = 0; i < rows.length; i++) {
            const nameCell = rows[i].getElementsByTagName("td")[0];
            const versionCell = rows[i].getElementsByTagName("td")[1];

            if (nameCell || versionCell) {
                const nameText = nameCell.textContent || nameCell.innerText;
                const versionText = versionCell.textContent || versionCell.innerText;

                if (nameText.toLowerCase().indexOf(searchTerm) > -1 || versionText.toLowerCase().indexOf(searchTerm) > -1) {
                    rows[i].style.display = "" // Show row
                } else {
                    rows[i].style.display = "none"; // Hide row
                }
            }
        }
    });
});
</script>
@endsection
