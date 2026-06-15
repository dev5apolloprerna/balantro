@props(['name', 'class' => '', 'width' => null, 'height' => null])

<span {{ $attributes->merge(['class' => $class]) }}>
    <svg xmlns="http://www.w3.org/2000/svg" 
         @if($width) width="{{ $width }}" @endif
         @if($height) height="{{ $height }}" @endif
         viewBox="0 0 24 24" 
         fill="none" 
         stroke="currentColor" 
         stroke-width="2" 
         stroke-linecap="round" 
         stroke-linejoin="round">
        <!-- Icon paths would be defined here based on the name -->
    </svg>
</span>