@if (count($errors) > 0)
  <div class="alert alert-danger fade in">
  	<button class="close" aria-hidden="true" data-dismiss="alert" type="button"></button>
      @foreach ($errors->all() as $error)
        <p><i class="fa-lg fa fa-warning"></i>{{ $error }}</p>
      @endforeach
  </div>
@endif