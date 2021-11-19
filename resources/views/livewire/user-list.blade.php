<div>
    <div class="row">
        <div class="col">
            <h3>Current Admins</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr wire:key="admin-{{ $user->id }}" id="admin-{{ $user->id }}">
                            <td>{{ $user->full_name }}</td>
                            <td>
                                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                            </td>
                            <td id="checkbox-for-{{ $user->id }}">
                                @if ($user->id != Auth::id())
                                    <div class="form-check" id="checkbox-user-{{ $user->id }}">
                                        <input class="form-check-input" type="checkbox" wire:model="removals" value="{{ $user->id }}" id="checkbox-{{ $user->id }}">
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>
                            <button wire:click="remove" @if (count($removals) == 0) disabled @endif class="btn btn-danger">Remove</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="col">
            <h4>Add new admin</h4>
            <form>
                <label for="exampleFormControlInput1" class="form-label">Username (GUID)</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" aria-label="GUID" aria-describedby="button-addon1" wire:model="guid" wire:keydown.enter.prevent="search">
                    <button wire:click.prevent="search" class="btn btn-secondary" type="button" id="button-addon1">Lookup</button>
                </div>
                @if ($errorMessage)
                    <div class="alert alert-danger" role="alert">
                        {{ $errorMessage }}
                    </div>
                @endif
                @if ($email)
                    <div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Email address</label>
                            <input wire:model="email" type="email" class="form-control" id="exampleFormControlInput1">
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Forenames</label>
                            <input wire:model="forenames" type="text" class="form-control" id="exampleFormControlInput1">
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Surname</label>
                            <input wire:model="surname" type="text" class="form-control" id="exampleFormControlInput1">
                        </div>
                        <button wire:click.prevent="add" id="addNewUserButton" class="btn btn-primary">Add</button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
