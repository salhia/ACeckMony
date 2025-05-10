<!-- resources/views/agent/discount/methods.blade.php -->
<form method="POST" action="{{ route('agent.discount.methods.update') }}">
    @csrf

    <div class="card">
        <div class="card-header">
            <h5>إعدادات طرق الخصم</h5>
        </div>

        <div class="card-body">
            <div class="form-group">
                <label>الحد الأقصى للخصم (%)</label>
                <input type="number" name="max_discount"
                       value="{{ $agent->max_discount }}"
                       min="1" max="50" class="form-control" required>
            </div>

            <hr>

            <h6>اختر طرق الخصم المسموحة:</h6>

            @foreach($availableMethods as $key => $method)
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox"
                       name="methods[]" value="{{ $key }}"
                       id="method_{{ $key }}"
                       @if(in_array($key, $agent->discount_methods ?? [])) checked @endif>
                <label class="form-check-label" for="method_{{ $key }}">
                    <strong>{{ $method['name'] }}</strong>
                    <p class="text-muted mb-0">{{ $method['description'] }}</p>
                </label>
            </div>
            @endforeach
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                حفظ التغييرات
            </button>
        </div>
    </div>
</form>
