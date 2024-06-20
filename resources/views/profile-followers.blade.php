<x-profile :sharedData=$sharedData doctitle="{{ $sharedData['username'] }}'s Followers">
  <div class="list-group">
    @foreach ($followers as $follow)
      <a href="/profile/{{ $follow->userDoingTheFollowing->username }}" class="list-group-item list-group-item-action">
        <img class="avatar-tiny" src="{{ $follow->userDoingTheFollowing->avatar }}" />
        {{ $follow->userDoingTheFollowing->username }} followed on {{ $follow->created_at->format('F j, Y, g:i a') }}
    @endforeach
  </div>
</x-profile>
