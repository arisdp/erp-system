@extends('layouts.app')

@section('page-title', 'User Profile')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Update Profile Information -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white">
                    <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-user-edit mr-2 text-primary"></i> Profile Information</h3>
                </div>
                <div class="card-body bg-light rounded-bottom">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Update Password -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-key mr-2"></i> Update Password</h3>
                </div>
                <div class="card-body bg-light rounded-bottom">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-exclamation-triangle mr-2"></i> Delete Account</h3>
                </div>
                <div class="card-body bg-light rounded-bottom">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
