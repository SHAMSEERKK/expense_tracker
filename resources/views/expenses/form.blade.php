<div class="field">
    <label for="amount">Amount</label>
    <input id="amount" type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount', $expense?->amount) }}" required>
    @error('amount') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
    <label for="category_id">Category</label>
    <select id="category_id" name="category_id" required>
        <option value="">Select category</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((string) old('category_id', $expense?->category_id) === (string) $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
    <label for="spent_at">Date</label>
    <input id="spent_at" type="datetime-local" name="spent_at" value="{{ old('spent_at', $expense?->spent_at?->format('Y-m-d\TH:i')) }}" required>
    @error('spent_at') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
    <label for="description">Description</label>
    <textarea id="description" name="description" required>{{ old('description', $expense?->description) }}</textarea>
    @error('description') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="actions" style="justify-content:flex-start;">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-secondary" href="{{ route('expenses.index') }}">Cancel</a>
</div>
