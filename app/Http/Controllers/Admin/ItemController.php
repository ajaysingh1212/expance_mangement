<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $items = Item::with('creator')->forUser($user)->latest()->paginate(15);
        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        $this->authorize('items.create');
        return view('admin.items.create');
    }

    public function store(Request $request)
    {
        $this->authorize('items.create');

        $data = $request->validate([
            'title'                   => 'required|string|max:200',
            'description'             => 'nullable|string',
            'category'                => 'nullable|string|max:100',
            'price'                   => 'nullable|numeric|min:0',
            'status'                  => 'required|in:active,inactive,draft',
            'image'                   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'share_with_creator_admin'=> 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $filename = 'item_' . time() . '.' . $request->file('image')->extension();
            $request->file('image')->storeAs('items', $filename, 'public');
            $data['image'] = $filename;
        }

        $data['created_by'] = auth()->id();
        $data['share_with_creator_admin'] = $request->boolean('share_with_creator_admin');

        $item = Item::create($data);
        ActivityLog::log('created', "Created item: {$item->title}", $item);

        return redirect()->route('admin.items.index')->with('success', "Item '{$item->title}' created successfully!");
    }

    public function show(Item $item)
    {
        $this->authorizeItemAccess($item);
        $item->load('creator');
        return view('admin.items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $this->authorize('items.edit');
        $this->authorizeItemAccess($item);
        return view('admin.items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $this->authorize('items.edit');
        $this->authorizeItemAccess($item);

        $data = $request->validate([
            'title'                   => 'required|string|max:200',
            'description'             => 'nullable|string',
            'category'                => 'nullable|string|max:100',
            'price'                   => 'nullable|numeric|min:0',
            'status'                  => 'required|in:active,inactive,draft',
            'image'                   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'share_with_creator_admin'=> 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image) Storage::disk('public')->delete('items/' . $item->image);
            $filename = 'item_' . time() . '.' . $request->file('image')->extension();
            $request->file('image')->storeAs('items', $filename, 'public');
            $data['image'] = $filename;
        }

        $data['share_with_creator_admin'] = $request->boolean('share_with_creator_admin');
        $item->update($data);

        ActivityLog::log('updated', "Updated item: {$item->title}", $item);

        return redirect()->route('admin.items.index')->with('success', "Item updated successfully!");
    }

    public function destroy(Item $item)
    {
        $this->authorize('items.delete');
        $this->authorizeItemAccess($item);

        if ($item->image) Storage::disk('public')->delete('items/' . $item->image);

        ActivityLog::log('deleted', "Deleted item: {$item->title}", $item);
        $item->delete();

        return redirect()->route('admin.items.index')->with('success', "Item deleted successfully.");
    }

    protected function authorizeItemAccess(Item $item)
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) return;
        if ($user->isAdmin()) {
            $myUserIds = $user->createdUsers()->pluck('id')->push($user->id);
            if (!$myUserIds->contains($item->created_by)) abort(403);
            return;
        }
        if ($item->created_by !== $user->id) abort(403);
    }
}
