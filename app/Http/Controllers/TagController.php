<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Tags
 *
 * Методы для управления тегами задач
 */
// TODO можно добавить пагинацию, сортировку и обратный сценарий с <дай мне все таски с этим тегом>
// TODO данный контроллер можно убрать, т.к. запрос ТЗ удовлетворяет функционал контроллер таски
class TagController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return TagResource::collection(Tag::all());
    }

    public function store(TagRequest $request): TagResource
    {
        $tag = Tag::create($request->validated());

        return new TagResource($tag);
    }

    public function show(Tag $tag): TagResource
    {
        return new TagResource($tag);
    }

    public function update(TagRequest $request, Tag $tag): TagResource
    {
        $tag->update($request->validated());

        return new TagResource($tag);
    }

    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully'], 204);
    }
}
