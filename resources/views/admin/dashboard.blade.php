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
@elseif($user->isBM())
    <!-- BM Dashboard -->
    @include('admin.dashboard.bm')
@elseif($user->isABM())
    <!-- ABM Dashboard -->
    @include('admin.dashboard.abm')
@elseif($user->isOO())
    <!-- OO Dashboard -->
    @include('admin.dashboard.oo')
@else
    <!-- Fallback: Staff Dashboard -->
    @include('admin.dashboard.staff')
@endif
@endsection
