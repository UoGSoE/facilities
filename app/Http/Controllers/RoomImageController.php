<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomImageController extends Controller
{
    public function show(Room $room)
    {
        if (! $room->image_path) {
            return file_get_contents('https://via.placeholder.com/800');
        }
        return Storage::get($room->image_path);
    }
}
