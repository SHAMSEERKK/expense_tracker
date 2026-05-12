<div class="field">
    <label for="name">Name</label>
    <input id="name" type="text" name="name" value="{{ old('name', $category?->name) }}" required>
    @error('name') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="actions" style="justify-content:flex-start;">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-secondary" href="{{ route('categories.index') }}">Cancel</a>
</div>
