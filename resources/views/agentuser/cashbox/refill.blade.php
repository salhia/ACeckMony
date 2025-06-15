@extends('agentuser.user_dashboard')
@section('agentuser')
<div class="page-content">
    <h3>Refill Cashbox</h3>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('cashbox.refill.store') }}">
        @csrf
        <div class="mb-3">
            <label for="amount" class="form-label">Refill Amount</label>
            <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description (optional)</label>
            <input type="text" name="description" id="description" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Refill</button>
    </form>
</div>
@endsection
