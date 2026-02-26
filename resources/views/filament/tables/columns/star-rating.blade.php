<div
    x-data="{
        rating: {{ (float) ($getState() ?? 0) }},
        hovered: 0,
        setRating(val) {
            this.rating = val;
            $wire.call('updateSubtopicRating', {{ $getRecord()->id }}, val);
        }
    }"
    style="display:flex;align-items:center;gap:2px;"
    aria-label="{{ (float) ($getState() ?? 0) }} out of 5 stars"
>
    @for ($i = 1; $i <= 5; $i++)
        <button
            type="button"
            x-on:mouseenter="hovered = {{ $i }}"
            x-on:mouseleave="hovered = 0"
            x-on:click="setRating({{ $i }})"
            style="background:none;border:none;padding:0;cursor:pointer;line-height:1;"
            title="{{ $i }} star{{ $i > 1 ? 's' : '' }}"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                style="width:1.15rem;height:1.15rem;display:block;"
                x-bind:style="{ color: (hovered ? hovered >= {{ $i }} : rating >= {{ $i }}) ? '#facc15' : '#6b7280' }"
                aria-hidden="true"
            >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
        </button>
    @endfor
    <button
        type="button"
        x-on:click="setRating(0)"
        x-show="rating > 0"
        style="margin-left:4px;background:none;border:none;padding:0 2px;cursor:pointer;font-size:0.7rem;color:#9ca3af;line-height:1;"
        title="Clear rating"
    >✕</button>
    <span style="margin-left:4px;font-size:0.7rem;color:#9ca3af;" x-text="rating.toFixed(1)"></span>
</div>
