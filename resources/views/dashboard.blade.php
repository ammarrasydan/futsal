@extends('layouts.app')

@push('styles')
<style>
    .card-body p:last-child {
        margin-bottom: 0;
    }
</style>
@endpush

@section('content')
<ol class="breadcrumb bg-white">
    <li class="breadcrumb-item active">Dashboard</li>
</ol>
@endsection

@push('scripts')
<script type="text/javascript">
    function Pageload() {
        $(".pre-loader").fadeOut();
    }

    $(document).ready(function() {
        Pageload();
    });
</script>
@endpush