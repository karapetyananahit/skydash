<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div>
                    <div class="container">
                        <div>
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('name', $user->username)" required autofocus autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('username')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div>
                                    <p class="text-sm mt-2 text-gray-800">
                                        {{ __('Your email address is unverified.') }}

                                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            {{ __('Click here to re-send the verification email.') }}
                                        </button>
                                    </p>

                                    @if (session('status') === 'verification-link-sent')
                                        <p class="mt-2 font-medium text-sm text-green-600">
                                            {{ __('A new verification link has been sent to your email address.') }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>


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


                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
