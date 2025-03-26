@include('layouts.header')
@include('layouts.navigation')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <h2 class="text-lg font-medium text-gray-900">
                {{ isset($influencer) ? __('Update User Information') : __('Create New User') }}
            </h2>

            <form method="POST" action="{{ isset($influencer) ? route('influencer.update', $influencer->id) : route('influencer.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                @csrf
                @isset($influencer)
                    @method('PUT')
                @endisset

                <div class="container">
                    <div class="mt-4">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $influencer->name ?? '')"  autofocus autocomplete="name" />
                        @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <div class="mt-4">
                        <x-input-label for="image" :value="__('Profile Image')" />
                        <div id="dropzone" class="dropzone border-2 border-dashed border-gray-300 rounded-lg p-4 bg-gray-50 mt-4">
                        </div>
                        <input type="hidden" name="image" id="image" value="{{ old('image', $influencer->image ?? '') }}">
                        <input type="hidden" name="previous_image" id="previous_image" value="{{ old('previous_image', $influencer->image ?? '') }}">

                        @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4 flex flex-col space-y-2">
                        @php
                            $selectedPlatforms = old('socialMedias', $influencer->socialMedias ?? []);
                        @endphp
                        <x-input-label for="socialMedias" :value="__('Select Social Media')" />
                        @foreach ($socialMedias as $socialMedia)
                            <div class="mt-4 flex items-center space-x-2">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="socialMedias[]" value="{{ $socialMedia->id }}"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           @if (isset($influencer) && $influencer->socialMedias->contains('id', $socialMedia->id)) checked @endif>
                                    <span>{{ $socialMedia->name }}</span>
                                </label>
                                <input type="number" name="prices[{{ $socialMedia->id }}]"
                                       value="{{ old('prices.' . $socialMedia->id, isset($influencer) && $influencer->socialMedias->contains('id', $socialMedia->id) ? $influencer->socialMedias->firstWhere('id', $socialMedia->id)->pivot->price : '0') }}"
                                       placeholder="Price" step="0.01" class="ml-2 rounded border-gray-300 p-1">
                            </div>
                        @endforeach

                        @error('platforms')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                            {{ isset($influencer) ? 'Save Changes' : 'Create Influencer' }}
                        </button>
                    </div>
                    <div class="flex items-center gap-4 mt-4">
                        <a href="#" id="cancelUpload" class="btn btn-block btn-secondary btn-lg font-weight-medium auth-form-btn">Cancel</a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>


@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script>
        document.getElementById('cancelUpload').addEventListener('click', function (event) {
            event.preventDefault(); // Խուսափում ենք էջի refresh-ից

            let imageName = document.getElementById('image').value; // Վերջին վերբեռնված նկարի անունը

            if (imageName) {
                fetch("{{ route('influencer.cancel') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ image: imageName })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Image deleted:", data);
                        window.location.href = "{{ route('influencer.index') }}";
                    })
                    .catch(error => console.error("Error:", error));
            } else {
                window.location.href = "{{ route('influencer.index') }}";
            }
        });



        Dropzone.autoDiscover = false;

        {{--let uploadedFile = "{{ isset($influencer) && $influencer->image ? asset('storage/temp/' . $influencer->image) : '' }}";--}}
        let uploadUrl = "{{ isset($influencer) ? route('influencer.uploadImage', $influencer->id) : route('influencer.uploadImage', 0) }}";
        let uploadedFile = "{{ isset($influencer) && $influencer->image ? asset('storage/auth/' . $influencer->image) : '' }}";

        let myDropzone = new Dropzone("#dropzone", {
            url: uploadUrl,
            paramName: "file",
            maxFilesize: 2,
            maxFiles: 1,
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            dictDefaultMessage: uploadedFile ? "Replace your avatar" : "Click or drag to upload your avatar",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            init: function () {
                let dz = this;
                let imageInput = document.getElementById('image');
                let previousImageInput = document.getElementById('previous_image');

                let previousImage = previousImageInput.value;

                if (previousImage) {
                    let mockFile = { name: previousImage, size: 1234567, type: "image/jpeg" };
                    dz.emit("addedfile", mockFile);
                    dz.emit("thumbnail", mockFile, uploadedFile);
                    dz.emit("complete", mockFile);
                    dz.files.push(mockFile);
                }

                dz.on("removedfile", function (file) {

                    let previousImageInput = document.getElementById('previous_image');
                    if (previousImageInput.value) {
                        fetch("{{ route('influencer.deleteImage') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ filename: previousImageInput.value })
                        }).then(response => response.json())
                            .then(data => console.log("Dropzone - Delete response:", data))
                            .catch(error => console.error("Dropzone - Delete error:", error));
                    }
                    document.getElementById('image').value = "";
                    document.getElementById('previous_image').value = "";
                });

                dz.on("addedfile", function (file) {
                    if (dz.files.length > 1) {
                        let oldFile = dz.files[0];
                        previousImageInput.value = imageInput.value;
                        dz.removeFile(oldFile);
                    }
                });

                dz.on("success", function (file, response) {
                    if (imageInput.value) {
                        previousImageInput.value = imageInput.value;
                    }
                    imageInput.value = response.file_path;
                });
            }
        });
    </script>

@endsection
@include('layouts.footer')
