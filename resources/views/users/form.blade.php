<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ isset($user) ? __('Update User Information') : __('Create New User') }}
                </h2>

                <form method="POST" action="{{ isset($user) ? route('user.update', $user->id) : route('user.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf
                    @isset($user)
                        @method('PUT')
                    @endisset

                    <div class="mt-4">
                        <label for="is_admin" class="flex items-center">
                            <input id="is_admin" name="is_admin" type="checkbox"
                                   class="rounded border-gray-300 shadow-sm focus:ring focus:ring-indigo-200"
                                   value="1"
                                {{ old('is_admin', isset($user) && $user->role === 'admin' ? 'checked' : '') }}>
                            <span class="ml-2 text-gray-700">Make this user an Admin</span>
                        </label>
                        @error('is_admin')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="container">
                        <div class="mt-4">
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username ?? '')"  autofocus autocomplete="username" />
                            @error('username')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email ?? '')"  autocomplete="username" />
                            @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group mt-4">
                            <select class="form-control form-control-lg" name="country" id="exampleFormControlSelect2">
                                <option disabled value="">Country</option>
                                <option value="usa">United States of America</option>
                                <option value="uk">United Kingdom</option>
                                <option value="india">India</option>
                                <option value="germany">Germany</option>
                                <option value="argentina">Argentina</option>
                            </select>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="profile_img" :value="__('Profile Image')" />
                            <div id="dropzone" class="dropzone border-2 border-dashed border-gray-300 rounded-lg p-4 bg-gray-50 mt-4">
                            </div>
                            <input type="hidden" name="profile_img" id="profile_img" value="{{ $user->profile_img ?? '' }}">
                            @error('profile_img')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <x-input-label for="password" :value="__('New Password')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                            @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>



                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                            @error('password_confirmation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                {{ isset($user) ? 'Save Changes' : 'Create User' }}
                            </button>
                        </div>
                        <div class="flex items-center gap-4 mt-4">
                            <a href="{{ url('/users') }}" class="btn btn-block btn-secondary btn-lg font-weight-medium auth-form-btn">Cancel</a>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        let uploadedFile = "{{ isset($user) && $user->profile_img ? asset('storage/auth/' . $user->profile_img) : '' }}";
        let uploadUrl = "{{ isset($user) ? route('user.uploadImage', $user->id) : route('user.uploadImage', 0) }}";

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
                    document.getElementById('profile_img').value = response.file_path;
                });

                dz.on("removedfile", function () {
                    document.getElementById('profile_img').value = "";
                });
            }
        });
    </script>
</x-app-layout>


