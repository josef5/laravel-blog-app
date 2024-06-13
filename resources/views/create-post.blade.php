<x-layout>
  <div class="container py-md-5 container--narrow">
      <form action="/create-post" method="POST" novalidate>
        <div class="form-group">
          @csrf
          <label for="post-title" class="text-muted mb-1"><small>Title</small></label>
          <input value="{{old('title')}}" name="title" id="post-title" class="form-control form-control-lg form-control-title" type="text" placeholder="" autocomplete="off" />
          @error('title')
              <p class="m-0 small alert alert-danger shadow-sm" role="alert">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-group">
          <label for="post-body" class="text-muted mb-1"><small>Body Content</small></label>
          <textarea required name="body" id="post-body" class="body-content tall-textarea form-control" type="text">{{old('body')}}</textarea></textarea>
          @error('body')
              <p class="m-0 small alert alert-danger shadow-sm" role="alert">{{ $message }}</p>
          @enderror
        </div>

        <button class="btn btn-primary">Save New Post</button>
      </form>
    </div>
</x-layout>