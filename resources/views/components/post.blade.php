<a href="/post/{{ $post->id }}" class="list-group-item list-group-item-action">
  <img class="avatar-tiny" src="{{ $post->user->avatar }}" />
  <strong>{{ $post->title }}</strong>
  <span class="text-muted small">
    @if (!isset($hideUsername))
      by {{ $post->user->username }}
    @endif
    on {{ $post->created_at->format('F j, Y, g:i a') }}
  </span>
</a>
