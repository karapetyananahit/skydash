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

            let totalFeesText = document.getElementById("total-fees").textContent.replace('$', '').trim();
            let totalFees = parseFloat(totalFeesText) || 0;

            tableData.push({
                name: "TOTAL FEES",
                services: "",
                price: "",
                total: `$${totalFees.toFixed(2)}`
            });

            fetch("{{ route('export.influencers') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ data: tableData })
            })
                .then(response => response.json())
                .then(data => {
                        let fileName = data.filename;
                        let checkInterval = setInterval(() => {
                            fetch(`{{ url('/download-export') }}/${fileName}`, { method: "HEAD" })
                                .then(response => {
                                    if (response.ok) {
                                        clearInterval(checkInterval);
                                        let downloadUrl = `{{ url('/download-export') }}/${fileName}`;
                                        let a = document.createElement("a");
                                        a.href = downloadUrl;
                                        a.download = fileName;
                                        document.body.appendChild(a);
                                        a.click();
                                        a.remove();
                                    }
                                })
                                .catch(error => console.error("Checking file error:", error));
                        }, 2000);

                })
                .catch(error => console.error("Export error:", error));
        });



        document.addEventListener("DOMContentLoaded", function () {
            let checkboxes = document.querySelectorAll(".service-checkbox");
            let tableContainer = document.getElementById("selected-influencers-container");
            let selectedInfluencersBody = document.getElementById("selected-influencers-body");

            let totalFees = 0;

            function updateTotalFees() {
                let totalFees = 0;
                let rows = document.querySelectorAll("#selected-influencers-body tr");

                rows.forEach(row => {
                    let totalFeeText = row.cells[3].textContent.replace('$', '').trim();
                    let totalFee = parseFloat(totalFeeText) || 0;
                    totalFees += totalFee;
                });

                document.getElementById("total-fees").textContent = `$${totalFees.toFixed(2)}`;
            }


            function updateTotalFeeRow(row, newTotalFee) {
                row.cells[3].textContent = `$${newTotalFee.toFixed(2)}`;
            }

            function calculateInfluencerTotal(influencerId) {
                let rows = Array.from(selectedInfluencersBody.querySelectorAll("tr"));
                let total = 0;

                rows.forEach(function (row) {
                    if (row.cells[4].textContent === influencerId) {
                        let services = row.cells[1].textContent.split(", ");
                        services.forEach(function (serviceName) {
                            let checkbox = Array.from(document.querySelectorAll(".service-checkbox")).find(cb =>
                                cb.dataset.influencerId === influencerId &&
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

            function updateInfluencerTotal(influencerId) {
                let existingRow = Array.from(selectedInfluencersBody.querySelectorAll("tr")).find(row =>
                    row.cells[4].textContent.trim() === influencerId
                );

                if (existingRow) {
                    updateFeeRow(existingRow);

                    let newTotalFee = calculateInfluencerTotal(influencerId);
                    let oldTotalFee = parseFloat(existingRow.cells[3].textContent.replace('$', '')) || 0;

                    totalFees -= oldTotalFee;
                    totalFees += newTotalFee;

                    updateTotalFeeRow(existingRow, newTotalFee);
                    updateTotalFees();
                }
            }

            function updateInfluencerServicesAndCount(influencerId) {
                let existingRow = Array.from(selectedInfluencersBody.querySelectorAll("tr")).find(row =>
                    row.cells[4].textContent.trim() === influencerId.trim()
                );

                if (!existingRow) return;

                let serviceNames = [];
                let totalFee = 0;
                let newTotalFee = 0;

                let checkboxes = Array.from(document.querySelectorAll(".service-checkbox")).filter(cb =>
                    cb.dataset.influencerId === influencerId
                );

                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        let serviceName = checkbox.dataset.serviceName;
                        let quantityInput = document.getElementById(checkbox.dataset.target);
                        let quantity = parseInt(quantityInput.value) || 1;
                        let price = parseFloat(checkbox.dataset.servicePrice) || 0;

                        serviceNames.push(serviceName);
                        totalFee += price;
                        newTotalFee += price * quantity;
                    }
                });

                let totalCount = checkboxes.reduce((acc, checkbox) => {
                    if (checkbox.checked) {
                        let quantityInput = document.getElementById(checkbox.dataset.target);
                        let quantity = parseInt(quantityInput.value) || 1;
                        return acc + quantity;
                    }
                    return acc;
                }, 0);

                if (serviceNames.length > 0) {
                    existingRow.cells[1].textContent = `${serviceNames.join(", ")} (${totalCount})`;
                    existingRow.cells[2].textContent = `$${totalFee.toFixed(2)}`;
                    existingRow.cells[3].textContent = `$${newTotalFee.toFixed(2)}`;
                } else {
                    let oldTotalFee = parseFloat(existingRow.cells[3].textContent.replace('$', '')) || 0;
                    totalFees -= oldTotalFee;
                    selectedInfluencersBody.removeChild(existingRow);
                }

                updateTotalFees();
            }

            checkboxes.forEach(function (checkbox) {
                document.addEventListener("change", function (event) {
                    if (event.target.classList.contains("service-checkbox")) {
                        let checkbox = event.target;
                        let influencerName = checkbox.dataset.influencerName;
                        let influencerId = checkbox.dataset.influencerId;
                        let serviceName = checkbox.dataset.serviceName;
                        let servicePrice = parseFloat(checkbox.dataset.servicePrice);
                        let quantityInput = document.getElementById(checkbox.dataset.target);
                        let priceBadge = checkbox.closest("li").querySelector(".badge");

                        if (checkbox.checked) {
                            quantityInput.disabled = false;
                            let quantity = parseInt(quantityInput.value) || 1;
                            let updatedFee = servicePrice * quantity;

                            let existingRow = Array.from(selectedInfluencersBody.querySelectorAll("tr")).find(row =>
                                row.cells[4].textContent.trim() === influencerId.trim()
                            );


                            if (existingRow) {
                                let currentServices = existingRow.cells[1].textContent;
                                if (!currentServices.includes(serviceName)) {
                                    existingRow.cells[1].textContent = currentServices + ", " + serviceName;
                                    updateInfluencerTotal(influencerId);
                                }
                            } else {
                                let newRow = document.createElement("tr");
                                newRow.innerHTML =
                                    `<td>${influencerName}</td>
                                     <td>${serviceName}</td>
                                     <td>$${servicePrice.toFixed(2)}</td>
                                     <td>$${updatedFee.toFixed(2)}</td>
                                     <td style="display: none">${influencerId}</td>`;;
                                selectedInfluencersBody.appendChild(newRow);
                                totalFees += updatedFee;
                            }

                            updateInfluencerServicesAndCount(influencerId);
                            updateTotalFees();
                        } else {
                            quantityInput.disabled = true;
                            quantityInput.value = 1;
                            priceBadge.textContent = `$${servicePrice.toFixed(2)}`;

                            let rows = selectedInfluencersBody.querySelectorAll("tr");
                            rows.forEach(function (row) {
                                if (row.cells[4].textContent === influencerId) {
                                    let currentServices = row.cells[1].textContent.split(", ");
                                    let newServices = currentServices.filter(service => service !== serviceName);

                                    if (newServices.length === 0) {
                                        let rowFee = parseFloat(row.cells[3].textContent.replace('$', '')) || 0;
                                        totalFees -= rowFee;
                                        selectedInfluencersBody.removeChild(row);
                                    } else {
                                        row.cells[1].textContent = newServices.join(", ");
                                        updateInfluencerTotal(influencerId);
                                    }
                                }
                            });

                            updateInfluencerServicesAndCount(influencerId);
                            updateTotalFees();
                        }

                        let isChecked = document.querySelectorAll(".service-checkbox:checked").length > 0;
                        tableContainer.style.display = isChecked ? "block" : "none";
                    }
                });

            });

            document.querySelectorAll(".quantity-input").forEach(function (input) {
                document.addEventListener("input", function (event) {
                    if (event.target.classList.contains("quantity-input")) {
                        let input = event.target;
                        let cardBody = input.closest(".card-body");
                        let influencerNameElement = cardBody.querySelector(".card-title");
                        let influencerName = influencerNameElement.textContent.trim();
                        let influencerId = cardBody.dataset.influencerId;
                        let serviceFee = parseFloat(input.closest("li").querySelector("input[type='checkbox']").dataset.servicePrice) || 0;
                        let priceBadge = input.closest("li").querySelector(".badge");
                        let quantity = parseInt(input.value) || 1;
                        let updatedFee = serviceFee * quantity;
                        priceBadge.textContent = `$${updatedFee.toFixed(2)}`;

                        updateInfluencerTotal(influencerId);
                        updateInfluencerServicesAndCount(influencerId);
                    }
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
    </script>
@endsection

@include('layouts.footer')
