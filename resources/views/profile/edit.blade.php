@extends('layouts.app')

@section('title', 'Profile Settings - LaraSEOScan')

@section('content')
<div class="container-fluid px-0 py-2">
    <div class="mb-5">
        <h2 class="fw-bold mb-1 text-dark">Account Settings</h2>
        <p class="text-muted mb-0">Update your profile information, password, and manage your account.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Profile Info Card -->
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-person-badge text-primary me-2"></i> Profile Information</h5>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Password Card -->
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-shield-lock text-warning me-2"></i> Update Password</h5>
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account Card -->
            <div class="card border-0 shadow-sm p-4 mb-4 border-danger border-opacity-10" style="background: rgba(220, 53, 69, 0.05) !important;">
                <h5 class="fw-bold text-danger mb-4"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> Delete Account</h5>
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
