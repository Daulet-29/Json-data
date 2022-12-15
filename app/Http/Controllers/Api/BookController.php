<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BookIndexResource;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookController extends Controller
{
    public function saveFile(Request $request)
    {
        if ($request->has('data')) {
            $validated = $request->validate([
                'data' => [
                    'name' => ['required', 'string'],
                    'description' => ['required', 'string'],
                    'createdAt' => ['required', 'date_format:Y-m-d'],
                ]
            ]);
            return $this->saveData($validated['data']);
        }
        return $this->saveData($request->all());
    }

    public function saveData(array $array)
    {
        foreach ($array as $item) {
            $data['title'] = isset($item['name']) ? $item['name'] : $item['title'];
            if (array_key_exists('description', $item)) { $data['description'] = $item['description']; }
            elseif (array_key_exists('descr', $item)) { $data['description'] = $item['descr']; }
            else { $data['description'] = $item['desc']; }
            $data['createdAt'] = isset($item['createdAt']) ? $item['createdAt'] : null;
            $data['author'] = isset($item['author']) ? $item['author'] : null;
            try {
                Book::query()->firstOrCreate($data);
            } catch (\Exception $exception) {
                return $exception;
            }
        }
        return response()->json(['success' => true, 'data' => BookIndexResource::collection(Book::all())]);
    }
}
