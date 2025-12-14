<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="py-12">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5 text-center">
                        <i class="bi bi-check-circle-fill text-success fs-1 mb-3"></i>
                        <h4 class="fw-bold mb-2">{{ __("You're logged in!") }}</h4>
                        <p class="text-muted">Welcome to the Class Mentor System.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
