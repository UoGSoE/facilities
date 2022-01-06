        <hr>
        <div class="d-flex justify-content-between bg-light p-4 shadow-sm">
            <h5>
                Room {{ $room->name }}
                <a href="{{ route('email.room_form', $room) }}" class="btn">
                    <i class="bi bi-envelope"></i>
                    <span>Email</span>
                </a>
                @if ($room->image_path)
                <span x-data="{ open: false, image: '{{ route('room.image', $room->id) }}' }">
                    <button class="btn" @click="open = ! open"><i class="bi bi-image"></i><span>Image</span></button>
                    <span x-show="open">
                        <p class="mt-4"><img :src="image" class="img-fluid"/></p>
                    </span>
                </span>
                @endif
            </h5>

            <div>
                <a href="{{ route('room.edit', $room) }}" class="btn btn-secondary">Edit</a>
            </div>
        </div>
        @if ($room->desks->count() > 0)
            <div class="d-flex justify-content-between bg-light ps-4 pe-4 shadow-sm @if ($room->lockers->count() == 0)  mb-4 pb-4 @endif">
                <div>
                    <span>Total Desks</span>
                    <span class="fw-bold">{{ $room->desks->count() }}</span>
                </div>
                <div>
                    <span>Desks Used</span>
                    <span class="fw-bold">{{ $room->desks->whereNotNull('people_id')->count() }} ({{ number_format(($room->desks->whereNotNull('people_id')->count() / $room->desks->count()) * 100, 1) }}%)</span>
                </div>
                <div>
                    <span>Desks Available</span>
                    <span class="fw-bold">{{ $room->desks->whereNull('people_id')->count() }} ({{ number_format(($room->desks->whereNull('people_id')->count() / $room->desks->count()) * 100, 1) }}%)</span>
                </div>
                <div>
                    <span>Desks free in the next 28 days</span>
                    <span class="fw-bold">
                        {{ $room->desks->whereNotNull('people_id')->sum(fn ($desk) => $desk->owner->isLeavingSoon()) }} ({{ number_format(($room->desks->whereNotNull('people_id')->sum(fn ($desk) => $desk->owner->isLeavingSoon()) / $room->desks->count()) * 100, 1) }}%)
                    </span>
                </div>
            </div>
        @endif
        @if ($room->lockers->count() > 0)
            <div class="d-flex justify-content-between mb-4 bg-light ps-4 pe-4 pb-4 shadow-sm">
                <div>
                    <span>Total Lockers</span>
                    <span class="fw-bold">{{ $room->lockers->count() }}</span>
                </div>
                <div>
                    <span>Lockers Used</span>
                    <span class="fw-bold">{{ $room->lockers->whereNotNull('people_id')->count() }} ({{ number_format(($room->lockers->whereNotNull('people_id')->count() / $room->lockers->count()) * 100, 1) }}%)</span>
                </div>
                <div>
                    <span>Lockers Available</span>
                    <span class="fw-bold">{{ $room->lockers->whereNull('people_id')->count() }} ({{ number_format(($room->lockers->whereNull('people_id')->count() / $room->lockers->count()) * 100, 1) }}%)</span>
                </div>
                <div>
                    <span>Lockers free in the next 28 days</span>
                    <span class="fw-bold">
                        {{ $room->lockers->whereNotNull('people_id')->sum(fn ($locker) => $locker->owner->isLeavingSoon()) }} ({{ number_format(($room->lockers->whereNotNull('people_id')->sum(fn ($locker) => $locker->owner->isLeavingSoon()) / $room->lockers->count()) * 100, 1) }}%)
                    </span>
                </div>
            </div>
        @endif
        @foreach ($room->desks->chunk(4) as $someDesks)
            <div class="row">
                @foreach ($someDesks as $desk)
                    <div
                        class="col p-2
                            @if (! $desk->owner) bg-info text-white @endif
                            @if ($desk->owner && $desk->owner->isLeavingSoon()) bg-warning @endif
                            @if ($desk->owner && $desk->owner->hasLeft()) bg-danger text-white @endif
                        "
                        title=""
                    >
                        Desk {{ $desk->name }} - {{ $desk->owner ? $desk->owner->full_name : 'No owner' }}
                        @if ($desk->owner && $desk->owner->isLeavingSoon()) ({{ $desk->owner->end_at->format('d/m/y') }}) @endif
                        @if ($desk->owner && $desk->owner->hasLeft()) ({{ $desk->owner->end_at->format('d/m/y') }}) @endif
                    </div>
                @endforeach
            </div>
        @endforeach

        <hr>
        @foreach ($room->lockers->chunk(4) as $someLockers)
        <div class="row">
            @foreach ($someLockers as $locker)
                <div class="col p-2
                    @if (! $locker->owner) bg-info text-white @endif
                    @if ($locker->owner && $locker->owner->isLeavingSoon()) bg-warning @endif
                    @if ($locker->owner && $locker->owner->hasLeft()) bg-danger text-white @endif
                ">
                    Locker {{ $locker->name }} - {{ $locker->owner ? $locker->owner->full_name : 'No owner' }}
                    @if ($locker->owner && $locker->owner->isLeavingSoon()) ({{ $locker->owner->end_at->format('d/m/y') }}) @endif
                    @if ($locker->owner && $locker->owner->hasLeft()) ({{ $locker->owner->end_at->format('d/m/y') }}) @endif
            </div>
            @endforeach
        </div>
        @endforeach
