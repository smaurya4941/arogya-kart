<x-app-layout>
    <div class="page mx-auto max-w-4xl">
        <div class="page-header">
            <h1 class="page-title">Profile</h1>
        </div>

        <div class="card card-pad">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card card-pad">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card card-pad">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
