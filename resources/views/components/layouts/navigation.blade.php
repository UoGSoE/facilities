<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Admin
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="{{ route('reports.pending') }}">Pending</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.people') }}">People</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.recent') }}">Recent Allocations</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.buildings') }}">Buildings</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.itassets') }}">IT Assets</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.supervisors') }}">Supervisors</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('user.index') }}">Manage Access</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('import.new_requests_form') }}">Import New Requests</a></li>
            </ul>
          </li>
        </ul>
        <form class="d-flex" method="POST" action="/logout">
            @csrf
            <button class="btn btn-secondary">Log Out</button>
        </form>
      </div>
    </div>
  </nav>
