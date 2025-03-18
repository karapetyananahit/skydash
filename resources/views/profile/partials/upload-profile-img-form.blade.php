<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update profile picture') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile picture") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <div class="relative">
                <div id="image-preview" class="mt-2">
                    @if ($user->profile_img)
                        <img id="profileImage" src="{{ asset('storage/auth/' . $user->profile_img) }}" alt="Profile Picture" class="rounded-full w-24 h-24 object-cover" />
                    @else
                        <p id="noImageText">No image selected.</p>
                    @endif
                </div>

                <div class="mt-4">
                    <input type="file" id="profile_img" name="profile_img" class="hidden" accept="image/*" onchange="previewImage(event)" />
                    <label for="profile_img" class="bg-indigo-600 text-white font-semibold py-2 px-4 rounded cursor-pointer hover:bg-indigo-700 transition">
                        Choose Profile Image
                    </label>
                </div>

                <input type="hidden" name="delete_image" id="deleteImageInput" value="0">

                @if ($user->profile_img)
                    <button type="button" id="deleteImageBtn" class="mt-2 bg-red-600 text-white font-semibold py-2 px-4 rounded hover:bg-red-700 transition">
                        Delete Image
                    </button>
                @endif
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('profile_img')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>
    </form>
</section>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function() {
            const imagePreview = document.getElementById('image-preview');
            imagePreview.innerHTML = `<img id="profileImage" src="${reader.result}" alt="Profile Image" class="rounded-full w-24 h-24 object-cover" />`;
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    document.getElementById('deleteImageBtn')?.addEventListener('click', function() {
        document.getElementById('profileImage')?.remove();
        document.getElementById('deleteImageBtn')?.remove();
        document.getElementById('deleteImageInput').value = "1";
        document.getElementById('image-preview').innerHTML = "<p>No image selected.</p>";
    });
</script>
