<div>
    <label for="{{ $id }}" class="flex items-center">
        <input type="checkbox" id="{{ $id }}" class="form-checkbox" name="{{ $name }}" value="1"
               x-model="{{ $xModel }}">
        <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
    </label>
</div>
