<div class="rounded-md bg-red-50/80 p-4">
  <div class="flex">
    <div class="flex-shrink-0">
      {{-- <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
      </svg> --}}
{{--	  <x-icon name="far-circle-x" class='w-8 text-red-400' />--}}
    </div>
    <div class="ml-3">
      <h3 class="text-sm font-medium text-red-800">Login Failed</h3>
      <div class="mt-2 text-sm text-red-700">
		{{ $message }}
      </div>
    </div>
  </div>
</div>
