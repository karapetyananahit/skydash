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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let checkboxes = document.querySelectorAll(".service-checkbox");
            let tableContainer = document.getElementById("selected-influencers-container");
            let totalFeesElement = document.getElementById("total-fees");
            let selectedInfluencersBody = document.getElementById("selected-influencers-body");

            let totalFees = 0;

            function updateTotalFees() {
                totalFeesElement.textContent = `$${totalFees.toFixed(2)}`;
            }

            function updateBadgeAndTotal(quantityInput, priceBadge, serviceFee) {
                let quantity = parseInt(quantityInput.value);
                let updatedFee = serviceFee * quantity;


                priceBadge.textContent = `$${updatedFee.toFixed(2)}`;


                totalFees += updatedFee;
                updateTotalFees();
            }

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener("change", function () {
                    let influencerName = this.dataset.influencerName;
                    let serviceName = this.dataset.serviceName;
                    let servicePrice = parseFloat(this.dataset.servicePrice);
                    let serviceFee = servicePrice;
                    let quantityInput = document.getElementById(this.dataset.target);
                    let priceBadge = this.closest("li").querySelector(".badge");
                    if (this.checked) {
                        quantityInput.disabled = false;
                        updateBadgeAndTotal(quantityInput, priceBadge, serviceFee);
                        let newRow = document.createElement("tr");
                        newRow.innerHTML = `
                    <td>${influencerName}</td>
                    <td>${serviceName}</td>
                    <td>$${servicePrice.toFixed(2)}</td>
                    <td>${serviceFee}</td>`;
                        selectedInfluencersBody.appendChild(newRow);

                        totalFees += serviceFee;
                    } else {
                        quantityInput.disabled = true;
                        quantityInput.value = 1;
                        priceBadge.textContent = `${serviceFee}$`;
                        let rows = selectedInfluencersBody.querySelectorAll("tr");
                        rows.forEach(function (row) {
                            if (row.cells[0].textContent === influencerName && row.cells[1].textContent === serviceName) {
                                let rowFee = parseFloat(row.cells[2].textContent.replace('$', ''));
                                totalFees -= rowFee;
                                selectedInfluencersBody.removeChild(row);
                            }
                        });
                    }

                    updateTotalFees();

                    let isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                    tableContainer.style.display = isChecked ? "block" : "none";
                });
            });
            document.querySelectorAll(".quantity-input").forEach(function (input) {
                input.addEventListener("input", function () {
                    let priceBadge = this.closest("li").querySelector(".badge");
                    let serviceFee = parseFloat(this.closest("li").querySelector("input[type='checkbox']").dataset.servicePrice);
                    updateBadgeAndTotal(this, priceBadge, serviceFee);
                });
            });
            selectedInfluencersBody.addEventListener("input", function (e) {
                if (e.target.classList.contains("service-quantity")) {
                    let quantity = parseInt(e.target.value);
                    let price = parseFloat(e.target.dataset.price);
                    let serviceFee = price * quantity;
                    let serviceRow = e.target.closest("tr");

                    let previousFee = parseFloat(serviceRow.cells[2].textContent.replace('$', ''));
                    totalFees -= previousFee;
                    totalFees += serviceFee;

                    serviceRow.cells[3].textContent = `$${serviceFee.toFixed(2)}`;

                    updateTotalFees();
                }
            });

            document.querySelectorAll(".service-checkbox").forEach(function (checkbox) {
                checkbox.addEventListener("change", function () {
                    let quantityInput = document.getElementById(this.dataset.target);
                    let priceBadge = this.closest("li").querySelector(".badge");
                    let servicePrice = parseFloat(this.dataset.servicePrice);
                    let serviceFee = servicePrice;

                    if (this.checked) {
                        quantityInput.disabled = false;
                        console.log(quantityInput,priceBadge,serviceFee)
                        updateBadgeAndTotal(quantityInput, priceBadge, serviceFee);
                    } else {
                        quantityInput.disabled = true;
                        quantityInput.value = 1;
                        priceBadge.textContent = `${serviceFee}$`;
                    }
                });
            });
        });


        $(document).ready(function() {
            let offset = 6;

            $('#search').on('input', function() {
                let query = $(this).val();
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
