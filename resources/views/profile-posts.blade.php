<x-profile :sharedData=$sharedData doctitle="{{ $sharedData['username'] }}'s Profile">
  <div class="list-group">
    @foreach ($posts as $post)
      <x-post :post=$post hideUsername />
    @endforeach
  </div>
</x-profile>
