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
        document.addEventListener("DOMContentLoaded", function () {
            let checkboxes = document.querySelectorAll(".service-checkbox");
            let tableContainer = document.getElementById("selected-influencers-container");
            let totalFeesElement = document.getElementById("total-fees");
            let selectedInfluencersBody = document.getElementById("selected-influencers-body");

            let totalFees = 0;

            function updateTotalFees() {
                totalFeesElement.textContent = `$${totalFees}`;
            }

            function calculateFee(quantity, serviceFee) {
                return parseFloat(quantity) * parseFloat(serviceFee);
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
                        let updatedFee = calculateFee(quantity, servicePrice);

                        let existingRow = Array.from(selectedInfluencersBody.querySelectorAll("tr")).find(row =>
                            row.cells[0].textContent.trim() === influencerName.trim()
                        );

                        if (existingRow) {
                            let currentServices = existingRow.cells[1].textContent.split(", ");
                            let currentServicesPrice = existingRow.cells[2].textContent;
                            if (!currentServices.includes(serviceName)) {
                                console.log(currentServicesPrice);
                                currentServices.push(serviceName);
                                existingRow.cells[1].textContent = currentServices.join(", ");

                                let currentFee = parseFloat(existingRow.cells[3].textContent.replace('$', '')) || 0;
                                let newFee = currentFee + updatedFee;
                                existingRow.cells[3].textContent = `$${newFee}`;

                                totalFees += updatedFee;
                            }
                        } else {
                            let newRow = document.createElement("tr");
                            newRow.innerHTML = `
                        <td>${influencerName}</td>
                        <td>${serviceName}</td>
                        <td>$${servicePrice}</td>
                        <td>$${updatedFee}</td>`;
                            selectedInfluencersBody.appendChild(newRow);
                            totalFees += updatedFee;
                        }

                        updateTotalFees();
                    } else {
                        quantityInput.disabled = true;
                        quantityInput.value = 1;

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
                                    let currentFee = parseFloat(row.cells[3].textContent.replace('$', '')) || 0;
                                    let updatedFee = currentFee - servicePrice;
                                    row.cells[3].textContent = `$${updatedFee}`;
                                    totalFees -= servicePrice;
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
                    let priceBadge = this.closest("li").querySelector(".badge");
                    let serviceFee = parseFloat(this.closest("li").querySelector("input[type='checkbox']").dataset.servicePrice);
                    let quantity = parseInt(this.value) || 0;
                    let updatedFee = calculateFee(quantity, serviceFee);

                    let influencerName = this.closest("li").querySelector("input[type='checkbox']").dataset.influencerName;
                    let row = Array.from(selectedInfluencersBody.querySelectorAll("tr")).find(row =>
                        row.cells[0].textContent.trim() === influencerName.trim()
                    );

                    if (row) {
                        let currentFee = parseFloat(row.cells[3].textContent.replace('$', '')) || 0;
                        totalFees -= currentFee;
                        row.cells[3].textContent = `$${updatedFee}`;
                        totalFees += updatedFee;
                    }

                    updateTotalFees();
                });
            });
        });

        $(document).ready(function() {
            let offset = 6;

            $('#search').on('input', function() {
                let query = $(this).val();
                $('#influencers-list').html('<p>Loading...</p>');  // Show loading indicator
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
