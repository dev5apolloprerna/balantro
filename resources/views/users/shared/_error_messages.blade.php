@if ($errors->any())
  <div id="error_explanation">
    <h2>
      @choice('errors.messages.not_saved', $errors->count(), [
          'resource' => strtolower(class_basename($model))
      ])
    </h2>
    <ul>
      @foreach ($errors->all() as $message)
        <li>{{ $message }}</li>
      @endforeach
    </ul>
  </div>
@endif