@extends('layouts.admin-minimal')

@section('title', 'Dashboard - BMMB Digital Forms')
@section('page-title', 'Dashboard')
@section('page-description', 'Overview of your form management system')

@section('content')
@if($user->isAdmin())
    <!-- Admin Dashboard -->
    @include('admin.dashboard.admin')
@elseif($user->isHQ())
    <!-- HQ Dashboard -->
    @include('admin.dashboard.hq')
@else
    <!-- BM/ABM/OO Dashboard -->
    @include('admin.dashboard.staff')
@endif
@endsection
