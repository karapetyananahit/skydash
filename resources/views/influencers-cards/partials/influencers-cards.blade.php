@foreach ($influencers as $influencer)
    <div class="col-md-4 mb-4 influencer-item">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <img src="{{ asset($influencer->image ? 'storage/auth/' . $influencer->image : 'images/default-profile.png') }}"
                         alt="Profile Picture" class="me-3" width="100" height="100">
                    <h5 class="card-title mb-0">{{ $influencer->name }}</h5>
                </div>
                <ul class="list-group list-group-flush mt-3">
                    @foreach($influencer->socialmedias as $socialmedia)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <input type="checkbox" name="selected_services[]" value="{{ $socialmedia->id }}"
                                   class="me-2 service-checkbox"
                                   data-influencer-name="{{ $influencer->name }}"
                                   data-service-name="{{ $socialmedia->name }}"
                                   data-service-price="{{ $socialmedia->pivot->price }}"
                                   data-target="quantity-{{ $socialmedia->id }}">

                            {{ $socialmedia->name }}
                            <input type="number" name="quantity[{{ $socialmedia->id }}]" value="1" min="1"
                                   class="form-control form-control-sm mx-2 quantity-input"
                                   id="quantity-{{ $socialmedia->id }}" disabled
                                   style="width: 35px; height: 25px; padding: 2px; text-align: center;">
                            <span class="badge rounded-pill">{{ $socialmedia->pivot->price }}$</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endforeach
