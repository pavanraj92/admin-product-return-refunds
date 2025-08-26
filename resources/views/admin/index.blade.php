@extends('admin::admin.layouts.master')

@section('title', 'Return Refunds Management')

@section('page-title', 'Return Refund Manager')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Return Refund Manager</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Return Refund Content -->
        <div class="row">
            <div class="col-12">
                <div class="card card-body">
                    <h4 class="card-title">Filter</h4>
                    <form action="{{ route('admin.return_refunds.index') }}" method="GET" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="title">Keyword</label>
                                    <input type="text" name="keyword" id="keyword" class="form-control"
                                        value="{{ app('request')->query('keyword') }}" placeholder="Enter user name or product">                                   
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="request_type">Request Type</label>
                                    <select name="request_type" id="request_type" class="form-control select2">
                                        <option value="">All</option>
                                        @foreach(config('return_refund.constants.refund_request_type') as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ app('request')->query('request_type') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control select2">
                                        <option value="">All</option>
                                        @foreach(config('return_refund.constants.refund_status') as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ app('request')->query('status') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto mt-1 text-right">
                                <div class="form-group">
                                    <label for="created_at">&nbsp;</label>
                                    <button type="submit" form="filterForm" class="btn btn-primary mt-4">Filter</button>
                                    <a href="{{ route('admin.return_refunds.index') }}" class="btn btn-secondary mt-4">Reset</a>
                                </div>
                            </div>
                        </div>                       
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">S. No.</th>
                                        <th scope="col">@sortablelink('user', 'User', [], ['class' => 'text-dark'])</th>
                                        <th scope="col">@sortablelink('product.name', 'Product', [], ['class' => 'text-dark'])</th>
                                        <th scope="col">@sortablelink('request_type', 'Request Type', [], ['class' => 'text-dark'])</th>
                                        <th scope="col">@sortablelink('status', 'Status', [], ['class' => 'text-dark'])</th>
                                        <th scope="col">@sortablelink('created_at', 'Created At', [], ['class' => 'text-dark'])</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($returnRefunds) && $returnRefunds->count() > 0)
                                        @php
                                            $i = ($returnRefunds->currentPage() - 1) * $returnRefunds->perPage() + 1;
                                        @endphp
                                        @foreach ($returnRefunds as $returnRefund)
                                            <tr>
                                                <th scope="row">{{ $i }}</th>
                                                <td>
                                                    @if (class_exists(\admin\users\Models\User::class))
                                                        {{ $returnRefund?->user?->full_name ?? 'N/A' }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $returnRefund?->product?->name ?? 'N/A' }}</td>
                                                <td>
                                                    {!! config('return_refund.constants.refundRequestTypeLabel.' . $returnRefund->request_type, 'N/A') !!}
                                                </td>
                                                 <td>
                                                    <select class="form-control form-control-sm refund-status-dropdown"
                                                        data-id="{{ $returnRefund->id }}" data-current="{{ $returnRefund->status }}">
                                                        @foreach (config('return_refund.constants.refund_status') as $key => $label)
                                                            <option value="{{ $key }}"
                                                                {{ $returnRefund->status == $key ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    {{ $returnRefund->created_at
                                                        ? $returnRefund->created_at->format(config('GET.admin_date_time_format') ?? 'Y-m-d H:i:s')
                                                        : '—' }}
                                                </td>
                                                <td style="width: 10%;">
                                                    @admincan('return_refunds_manager_view')
                                                    <a href="{{ route('admin.return_refunds.show', $returnRefund) }}" 
                                                        data-toggle="tooltip"
                                                        data-placement="top"
                                                        title="View this record"
                                                        class="btn btn-warning btn-sm"><i class="mdi mdi-eye"></i></a>
                                                    @endadmincan
                                                </td>
                                            </tr>
                                            @php
                                                $i++;
                                            @endphp
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">No records found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <!--pagination move the right side-->
                            @if ($returnRefunds->count() > 0)
                                {{ $returnRefunds->links('admin::pagination.custom-admin-pagination') }}
                            @endif                        
                            
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- End return refund Content -->
    </div>
    <!-- End Container fluid  -->
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).on('change', '.refund-status-dropdown', function() {
            var $select = $(this);
            var refundId = $select.data('id');
            var newStatus = $select.val();
            var currentStatus = $select.data('current');

            if (newStatus === currentStatus) return;

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to change the status to "' + newStatus + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, change it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.return_refunds.updateStatus') }}",
                        method: "POST",
                        data: {
                            id: refundId,
                            status: newStatus,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Success', response.message, 'success');
                                $select.data('current', newStatus);
                            } else {
                                Swal.fire('Error', response.message, 'error');
                                $select.val(currentStatus);
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to update status.', 'error');
                            $select.val(currentStatus);
                        }
                    });
                } else {
                    $select.val(currentStatus);
                }
            });
        });
    </script>
@endpush