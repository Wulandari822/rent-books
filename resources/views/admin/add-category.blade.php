@extends('layouts.main')

@section('title', 'Add Category')

@section ('content')
<h1>Add category</h1>

            @if ($errors->any())
              <div class="alert alert-danger w-50">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
            @endif

<form action="/category-add" method="post">
    @csrf
    <label for="name" class="form-label">Category Name</label>
    <input type="text" name="name" id="name" class="form-control w-50">
    <button type="submit" class="btn btn-success mt-3">Save</button>
</form>

@endsection