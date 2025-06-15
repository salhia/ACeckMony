@extends('agentuser.user_dashboard')
@section('agentuser')
<div class="page-content">
    <h3>Set Your Opening Balance</h3>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(!$exists)
        <form method="POST" action="{{ route('cashbox.opening.store') }}">
            @csrf
            <div class="mb-3">
                <label for="amount" class="form-label">Opening Balance Amount</label>
                <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Opening Balance</button>
        </form>
    @else
        <div class="alert alert-info">You have already set your opening balance for today.</div>
    @endif
</div>
@endsection
