<x-layouts.app>
    <div class="form-signin">
        <form method="POST" action="/login">
            @csrf
          <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

          <div class="form-floating">
            <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Username">
            <label for="floatingInput">Username</label>
          </div>
          <div class="form-floating">
            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
            <label for="floatingPassword">Password</label>
          </div>

          <div class="checkbox mb-3">
            <label>
              <input type="checkbox" value="1" name="remember"> Remember me
            </label>
          </div>
          @error('username')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
          @enderror
          <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
        </form>
      </div>
</x-layouts.app>
