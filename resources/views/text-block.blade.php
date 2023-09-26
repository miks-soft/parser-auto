<div class="rounded bg-white mb-3 p-3">
    <div class="w-100 rounded overflow-hidden">
        @if(isset($title))
            <label class="form-label">{{$title}}</label>
        @endif
        <small class="d-block {{$classes ?? ''}}" style="{{$style ?? ''}}">{{ $message }}</small>
    </div>
</div>
