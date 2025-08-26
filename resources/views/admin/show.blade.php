@extends('admin::admin.layouts.master')

@section('title', 'Return Refunds Management')

@section('page-title', 'Return Refund Details')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.return_refunds.index') }}">Return Refund
            Manager</a></li>
    <li class="breadcrumb-item active" aria-current="page">Return Refund Details</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="card-title mb-0">
                                Order #{{ $returnRefund?->order?->order_number ?? 'N/A' }} - {{ $returnRefund?->product?->name ?? 'N/A' }}
                            </h4>
                            <div>
                                <a href="{{ route('admin.return_refunds.index') }}" class="btn btn-secondary ml-2">
                                    Back
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0 text-white font-bold">Return Refund Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Order ID:</label>
                                                    <p>{{ $returnRefund?->order?->order_number ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Product:</label>
                                                    <p>{{ $returnRefund?->product?->name ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">User Name:</label>
                                                    <p>{{ $returnRefund?->user?->full_name ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Seller Name:</label>
                                                    <p>{{ $returnRefund?->seller?->full_name ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Request Type:</label>
                                                    <p>{!! config('return_refund.constants.refundRequestTypeLabel.' . $returnRefund->request_type, 'N/A') !!}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Status:</label>
                                                    <p>{!! config('return_refund.constants.refundStatusLabel.' . $returnRefund->status, 'N/A') !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">Reason:</label>
                                            <p>{{ $returnRefund->reason ?? 'N/A' }}</p>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">Description:</label>
                                            <p>{{ $returnRefund->description ?? 'N/A' }}</p>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Refund Amount:</label>
                                                    <p>{{ $returnRefund->refund_amount ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Refund Method:</label>
                                                    <p>{!! config('return_refund.constants.refundMethod.' . $returnRefund->refund_method, 'N/A') !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">Refund Processed At:</label>
                                            <p>
                                                {{ $returnRefund->refund_processed_at
                                                    ? $returnRefund->refund_processed_at->format(config('GET.admin_date_time_format') ?? 'Y-m-d H:i:s')
                                                    : '—' }}
                                            </p>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Return Tracking Number:</label>
                                                    <p>{{ $returnRefund->return_tracking_number ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Return Shipping Carrier:</label>
                                                    <p>{{ $returnRefund->return_shipping_carrier ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">Created At:</label>
                                            <p>
                                                {{ $returnRefund->created_at
                                                    ? $returnRefund->created_at->format(config('GET.admin_date_time_format') ?? 'Y-m-d H:i:s')
                                                    : '—' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Container fluid  -->
@endsection
