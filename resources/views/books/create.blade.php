@extends('layouts.app')

@section('content')
  <h1 class="mb-10 text-2xl">Add a book </h1>

  @if ($errors->any())
  <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
    <strong>There were some problems with your input:</strong>
    <ul class="list-disc pl-5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

  <form method="POST" action="{{ route('books.store') }}">
    @csrf
    <label for="title">title</label>
    <textarea name="title" id="title" required class="input mb-4"></textarea>

    <label for="author">author</label>
    <textarea name="author" id="author" required class="input mb-4"></textarea>

   
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('books.index') }}" class="btn btn-secondary">
            Back
        </a>

        <button type="submit" class="btn">
            Add book
        </button>
    </div>
  </form>
@endsection