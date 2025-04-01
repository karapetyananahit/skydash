@include('layouts.header')
@include('layouts.navigation')

<header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Influencers Cards') }}
        </h2>
    </div>
</header>
<div id="selected-influencers-container" style="display: none;" >
    @include('influencers-cards.partials.influencers-services-table')
</div>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="d-flex justify-content-between mb-3">
            <input type="text" id="search" class="form-control" placeholder="Search Influencers" />
        </div>

        <div id="influencers-list" class="row">
            @include('influencers-cards.partials.influencers-cards', ['influencers' => $influencers])
        </div>

        <div class="text-center mt-4">
            <button id="load-more" class="btn btn-primary">Show More</button>
        </div>
    </div>
</div>

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <script>
        document.getElementById("export-excel").addEventListener("click", function () {
            let tableData = [];
            let rows = document.querySelectorAll("#selected-influencers-body tr");

            rows.forEach(row => {
                let cells = row.querySelectorAll("td");
                let rowData = {
                    name: cells[0].textContent.trim(),
                    services: cells[1].textContent.trim(),
                    price: cells[2].textContent.trim(),
                    total: cells[3].textContent.trim(),
                };
                tableData.push(rowData);
            });

            fetch("{{ route('export.influencers') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ data: tableData })
            })
                .then(response => response.blob())
                .then(blob => {
                    let url = window.URL.createObjectURL(blob);
                    let a = document.createElement("a");
                    a.href = url;
                    a.download = "influencers.xlsx";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                })
                .catch(error => console.error("Export error:", error));
        });


        document.addEventListener("DOMContentLoaded", function () {
            let checkboxes = document.querySelectorAll(".service-checkbox");
            let tableContainer = document.getElementById("selected-influencers-container");
            let totalFeesElement = document.getElementById("total-fees");
            let selectedInfluencersBody = document.getElementById("selected-influencers-body");

            let totalFees = 0;

            function updateTotalFees() {
                totalFeesElement.textContent = `$${totalFees.toFixed(2)}`;
            }

            function updateTotalFeeRow(row, newTotalFee) {
                row.cells[3].textContent = `$${newTotalFee.toFixed(2)}`;
            }

            function calculateInfluencerTotal(influencerName) {
                let rows = Array.from(selectedInfluencersBody.querySelectorAll("tr"));
                let total = 0;

                rows.forEach(function (row) {
                    if (row.cells[0].textContent.trim() === influencerName.trim()) {
                        let services = row.cells[1].textContent.split(", ");
                        services.forEach(function (serviceName) {
                            let checkbox = Array.from(document.querySelectorAll(".service-checkbox")).find(cb =>
                                cb.dataset.influencerName === influencerName &&
                                cb.dataset.serviceName === serviceName
                            );
                            if (checkbox) {
                                let quantityInput = document.getElementById(checkbox.dataset.target);
                                let quantity = parseInt(quantityInput.value) || 1;
                                let price = parseFloat(checkbox.dataset.servicePrice) || 0;
                                total += quantity * price;
                            }
                        });
                    }
                });

                return total;
            }

            function updateFeeRow(row) {
                let serviceFees = row.cells[1].textContent.split(", ").map(serviceName => {
                    let checkbox = Array.from(document.querySelectorAll(".service-checkbox")).find(cb =>
                        cb.dataset.influencerName === row.cells[0].textContent.trim() &&
                        cb.dataset.serviceName === serviceName
                    );
                    return checkbox ? parseFloat(checkbox.dataset.servicePrice) : 0;
                });

                let totalFee = serviceFees.reduce((acc, fee) => acc + fee, 0);
                row.cells[2].textContent = `$${totalFee.toFixed(2)}`;
            }

            function updateInfluencerTotal(influencerName) {
                let existingRow = Array.from(selectedInfluencersBody.querySelectorAll("tr")).find(row =>
                    row.cells[0].textContent.trim() === influencerName.trim()
                );

                if (existingRow) {
                    updateFeeRow(existingRow);

                    let newTotalFee = calculateInfluencerTotal(influencerName);
                    let oldTotalFee = parseFloat(existingRow.cells[3].textContent.replace('$', '')) || 0;

                    totalFees -= oldTotalFee;
                    totalFees += newTotalFee;

                    updateTotalFeeRow(existingRow, newTotalFee);
                    updateTotalFees();
                }
            }

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener("change", function () {
                    let influencerName = this.dataset.influencerName;
                    let serviceName = this.dataset.serviceName;
                    let servicePrice = parseFloat(this.dataset.servicePrice);
                    let quantityInput = document.getElementById(this.dataset.target);
                    let priceBadge = this.closest("li").querySelector(".badge");

                    if (this.checked) {
                        quantityInput.disabled = false;
                        let quantity = parseInt(quantityInput.value) || 1;
                        let updatedFee = servicePrice * quantity;

                        let existingRow = Array.from(selectedInfluencersBody.querySelectorAll("tr")).find(row =>
                            row.cells[0].textContent.trim() === influencerName.trim()
                        );

                        if (existingRow) {
                            let currentServices = existingRow.cells[1].textContent;
                            if (!currentServices.includes(serviceName)) {
                                existingRow.cells[1].textContent = currentServices + ", " + serviceName;
                                updateInfluencerTotal(influencerName);
                            }
                        } else {
                            let newRow = document.createElement("tr");
                            newRow.innerHTML =
                                `<td>${influencerName}</td>
                            <td>${serviceName}</td>
                            <td>$${servicePrice.toFixed(2)}</td>
                            <td>$${updatedFee.toFixed(2)}</td>`;
                            selectedInfluencersBody.appendChild(newRow);
                            totalFees += updatedFee;
                        }

                        updateTotalFees();
                    } else {
                        quantityInput.disabled = true;
                        quantityInput.value = 1;
                        priceBadge.textContent = `$${servicePrice.toFixed(2)}`;

                        let rows = selectedInfluencersBody.querySelectorAll("tr");
                        rows.forEach(function (row) {
                            if (row.cells[0].textContent.trim() === influencerName.trim()) {
                                let currentServices = row.cells[1].textContent.split(", ");
                                let newServices = currentServices.filter(service => service !== serviceName);

                                if (newServices.length === 0) {
                                    let rowFee = parseFloat(row.cells[3].textContent.replace('$', '')) || 0;
                                    totalFees -= rowFee;
                                    selectedInfluencersBody.removeChild(row);
                                } else {
                                    row.cells[1].textContent = newServices.join(", ");
                                    updateInfluencerTotal(influencerName);
                                }
                            }
                        });

                        updateTotalFees();
                    }

                    let isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                    tableContainer.style.display = isChecked ? "block" : "none";
                });
            });

            document.querySelectorAll(".quantity-input").forEach(function (input) {
                input.addEventListener("input", function () {
                    let influencerName = this.closest(".card-body").querySelector(".card-title").textContent.trim();
                    let serviceFee = parseFloat(this.closest("li").querySelector("input[type='checkbox']").dataset.servicePrice);
                    let priceBadge = this.closest("li").querySelector(".badge");
                    let quantity = parseInt(this.value) || 1;
                    let updatedFee = serviceFee * quantity;
                    priceBadge.textContent = `$${updatedFee.toFixed(2)}`;

                    updateInfluencerTotal(influencerName);
                });
            });
        });

        $(document).ready(function() {
            let offset = 6;

            $('#search').on('input', function() {
                let query = $(this).val();
                $('#influencers-list').html('<p>Loading...</p>');
                $.ajax({
                    url: '{{ route('influencers-cards.search') }}',
                    method: 'GET',
                    data: { search: query },
                    success: function(response) {
                        $('#load-more').hide();
                        $('#influencers-list').html(response);
                    },
                    error: function(xhr, status, error) {
                        $('#influencers-list').html('<p class="text-danger">An error occurred while fetching influencers.</p>');
                    }
                });
            });


            $(document).ready(function() {
                let offset = 6;
                $('#load-more').on('click', function() {
                    $.ajax({
                        url: '{{ route('influencers-cards.loadMore') }}',
                        method: 'GET',
                        data: { offset: offset },
                        success: function(response) {
                            if (response.hideButton) {
                                $('#load-more').hide();
                            }
                            $('#influencers-list').append(response.html);
                            offset += 6;
                        },
                        error: function() {
                            alert('An error occurred while loading more influencers.');
                        }
                    });
                });

            });

        });

    </script>
@endsection

@include('layouts.footer')
