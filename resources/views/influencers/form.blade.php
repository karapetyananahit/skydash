
@include('layouts.header')
@include('layouts.navigation')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <h2 class="text-lg font-medium text-gray-900">
                {{ isset($influencer) ? __('Update User Information') : __('Create New User') }}
            </h2>

            <form method="POST" action="{{ isset($influencer) ? route('user.update', $influencer->id) : route('influencer.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
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
                        <input type="hidden" name="image" id="image" value="{{ $influencer->image ?? '' }}">
                        @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <x-input-label for="socialMedias" :value="__('Select Social Media')" />

                        @php
                            $selectedPlatforms = old('socialMedias', $influencer->socialMedias ?? []);
                        @endphp

                        <div class="flex flex-col space-y-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="socialMedias[]" value="Tiktok"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ in_array('Tiktok', $selectedPlatforms) ? 'checked' : '' }}>
                                <span class="ml-2">Tiktok</span>
                            </label>

                            <label class="inline-flex items-center">
                                <input type="checkbox" name="socialMedias[]" value="Instagram Reel"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ in_array('Instagram Reel', $selectedPlatforms) ? 'checked' : '' }}>
                                <span class="ml-2">Instagram Reel</span>
                            </label>

                            <label class="inline-flex items-center">
                                <input type="checkbox" name="socialMedias[]" value="Instagram Story"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ in_array('Instagram Story', $selectedPlatforms) ? 'checked' : '' }}>
                                <span class="ml-2">Instagram Story</span>
                            </label>

                            <label class="inline-flex items-center">
                                <input type="checkbox" name="socialMedias[]" value="YouTube Integration"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ in_array('YouTube Integration', $selectedPlatforms) ? 'checked' : '' }}>
                                <span class="ml-2">YouTube Integration</span>
                            </label>

                            <label class="inline-flex items-center">
                                <input type="checkbox" name="socialMedias[]" value="YouTube Dedicated"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ in_array('YouTube Dedicated', $selectedPlatforms) ? 'checked' : '' }}>
                                <span class="ml-2">YouTube Dedicated</span>
                            </label>

                            <label class="inline-flex items-center">
                                <input type="checkbox" name="socialMedias[]" value="YouTube Short"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ in_array('YouTube Short', $selectedPlatforms) ? 'checked' : '' }}>
                                <span class="ml-2">YouTube Short</span>
                            </label>

                            <label class="inline-flex items-center">
                                <input type="checkbox" name="socialMedias[]" value="Facebook Post"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ in_array('Facebook Post', $selectedPlatforms) ? 'checked' : '' }}>
                                <span class="ml-2">Facebook Post</span>
                            </label>
                        </div>

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
                        <a href="{{ route('user.cancel') }}" class="btn btn-block btn-secondary btn-lg font-weight-medium auth-form-btn">Cancel</a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>


@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script>

        Dropzone.autoDiscover = false;

        let uploadedFile = "{{ isset($influencer) && $influencer->image ? asset('storage/auth/' . $influencer->image) : '' }}";
        let uploadUrl = "{{ isset($influencer) ? route('influencer.uploadImage', $influencer->id) : route('influencer.uploadImage', 0) }}";

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

                if (uploadedFile) {
                    let mockFile = { name: "Current Avatar", size: 1234567, type: "image/jpeg" };
                    dz.emit("addedfile", mockFile);
                    dz.emit("thumbnail", mockFile, uploadedFile);
                    dz.emit("complete", mockFile);
                    dz.files.push(mockFile);
                }

                dz.on("addedfile", function (file) {
                    if (dz.files.length > 1) {
                        dz.removeFile(dz.files[0]);
                    }
                });

                dz.on("success", function (file, response) {
                    document.getElementById('image').value = response.file_path;
                });

                dz.on("removedfile", function () {
                    document.getElementById('image').value = "";
                });
            }
        });
    </script>
@endsection
@include('layouts.footer')
