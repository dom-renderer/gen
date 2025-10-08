@php
    $content = $content ?? 'N/A';
@endphp
<i class="bi bi-info-circle @if(request()->route()->getName() != 'cases.view') tooltip-edit @endif" style="cursor:pointer;margin-left:12px;" data-element="{{ $elementId }}" data-bs-toggle="tooltip" title="{{ e($content) }}" ></i>