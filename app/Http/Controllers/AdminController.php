<?php

namespace App\Http\Controllers;

use App\Models\{User, MenuItem, Order, OrderItem, Contact};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers      = User::count();
        $newUsersThisWeek = User::where('created_at', '>=', now()->startOfWeek())->count();
        $totalOrders     = Order::count();
        $ordersToday     = Order::whereDate('created_at', today())->count();
        $totalRevenue    = Order::where('status', 'completed')->sum('total');
        $revenueToday    = Order::where('status', 'completed')->whereDate('created_at', today())->sum('total');
        $totalMenuItems  = MenuItem::count();
        $availableItems  = MenuItem::available()->count();
        $pendingCount    = Order::where('status', 'pending')->count();
        $unreadContacts  = Contact::where('is_read', false)->count();
        $recentOrders    = Order::with(['user', 'items'])->latest()->take(8)->get();
        $topItems        = MenuItem::withCount(['orderItems as total_sold' => fn($q) => $q->whereHas('order')])
                            ->orderByDesc('total_sold')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'newUsersThisWeek', 'totalOrders', 'ordersToday',
            'totalRevenue', 'revenueToday', 'totalMenuItems', 'availableItems',
            'pendingCount', 'unreadContacts', 'recentOrders', 'topItems'
        ));
    }

    public function users(Request $request)
    {
        $query = User::withCount('orders');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('first_name', 'like', "%$s%")
                ->orWhere('last_name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%"));
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $validator = validator($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'nullable|string|max:20',
            'role'       => 'required|in:user,staff,admin',
            'password'   => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        User::create([
            'first_name'  => $data['first_name'],
            'last_name'   => $data['last_name'],
            'email'       => $data['email'],
            'phone'       => $data['phone'] ?? null,
            'role'        => $data['role'],
            'password'    => Hash::make($data['password']),
            'preferences' => ['email_orders' => true, 'email_promos' => false, 'sms_notifications' => false],
        ]);

        return response()->json(['success' => true, 'message' => 'User created successfully.']);
    }

    public function updateUser(Request $request, User $user)
    {
        $validator = validator($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:20',
            'role'       => 'required|in:user,staff,admin',
            'password'   => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $payload = [
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'role'       => $data['role'],
        ];

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return response()->json(['success' => true, 'message' => 'User updated successfully.']);
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:user,staff,admin']);
        $user->update(['role' => $request->role]);
        return response()->json(['success' => true]);
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 403);
        }
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted.']);
    }

    public function menuIndex(Request $request)
    {
        $query = MenuItem::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $items      = $query->orderBy('category')->orderBy('name')->paginate(20);
        $categories = MenuItem::distinct()->pluck('category');

        return view('admin.menu-index', compact('items', 'categories'));
    }

    public function menuCreate()
    {
        return view('admin.menu-form');
    }

    public function menuStore(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'description'  => 'nullable|string|max:500',
            'price'        => 'required|numeric|min:0',
            'category'     => 'required|string',
            'emoji'        => 'nullable|string|max:8',
            'image'        => 'nullable|image|max:2048',
            'is_available' => 'nullable',
            'is_featured'  => 'nullable',
            'prep_time'    => 'nullable|integer|min:1',
            'allergens'    => 'nullable|string',
            'calories'     => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public_images');
        }

        $data['is_available'] = $request->has('is_available');
        $data['is_featured']  = $request->has('is_featured');

        MenuItem::create($data);
        return redirect(route('admin.menu'))->with('success', 'Menu item added!');
    }

    public function menuEdit(MenuItem $item)
    {
        return view('admin.menu-form', compact('item'));
    }

    public function menuUpdate(Request $request, MenuItem $item)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'description'  => 'nullable|string|max:500',
            'price'        => 'required|numeric|min:0',
            'category'     => 'required|string',
            'emoji'        => 'nullable|string|max:8',
            'image'        => 'nullable|image|max:2048',
            'is_available' => 'nullable',
            'is_featured'  => 'nullable',
            'prep_time'    => 'nullable|integer|min:1',
            'allergens'    => 'nullable|string',
            'calories'     => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image) {
                @unlink(public_path('images/menu/' . $item->image));
            }
            $data['image'] = $request->file('image')->store('menu', 'public_images');
        }

        $data['is_available'] = $request->has('is_available');
        $data['is_featured']  = $request->has('is_featured');

        $item->update($data);
        return redirect(route('admin.menu'))->with('success', 'Menu item updated!');
    }

    public function menuToggle(MenuItem $item)
    {
        $item->update(['is_available' => !$item->is_available]);
        return back()->with('success', $item->name . ' marked as ' . ($item->is_available ? 'available' : 'unavailable') . '.');
    }

    public function menuFeature(MenuItem $item)
    {
        $item->update(['is_featured' => !$item->is_featured]);
        return back();
    }

    public function menuDestroy(MenuItem $item)
    {
        if ($item->image) @unlink(public_path('images/menu/' . $item->image));
        $item->delete();
        return back()->with('success', 'Item deleted.');
    }

    public function contacts(Request $request)
    {
        $query = Contact::latest();
        if ($request->filter === 'unread') {
            $query->where('is_read', false);
        }
        $contacts    = $query->paginate(20);
        $totalCount  = Contact::count();
        $unreadCount = Contact::where('is_read', false)->count();

        return view('admin.contacts', compact('contacts', 'totalCount', 'unreadCount'));
    }

    public function markContactRead(Contact $contact)
    {
        $contact->update(['is_read' => true]);
        if (request()->ajax()) return response()->json(['success' => true]);
        return back();
    }

    public function markAllContactsRead()
    {
        Contact::where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All messages marked as read.');
    }

    public function destroyContact(Contact $contact)
    {
        $contact->delete();
        return back()->with('success', 'Message deleted.');
    }

    public function orders(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(20);

        $statusCounts = [];
        foreach (Order::statusList() as $s) {
            $statusCounts[$s] = Order::where('status', $s)->count();
        }

        return view('admin.orders', compact('orders', 'statusCounts'));
    }

    public function showOrder(Order $order)
    {
        $order->load(['user', 'items.menuItem']);
        return view('admin.order-detail', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled']);
        $order->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function analytics()
    {
        $monthRevenue  = Order::where('status', 'completed')
                            ->whereMonth('created_at', now()->month)
                            ->sum('total');
        $monthOrders   = Order::whereMonth('created_at', now()->month)->count();
        $avgOrderValue = Order::where('status', 'completed')->avg('total') ?? 0;
        $newUsers      = User::whereMonth('created_at', now()->month)->count();

        $dailyLabels  = [];
        $dailyRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dailyLabels[]  = $day->format('M d');
            $dailyRevenue[] = (float) Order::where('status', 'completed')
                                ->whereDate('created_at', $day)->sum('total');
        }

        $statusLabels = [];
        $statusData   = [];
        foreach (Order::statusList() as $s) {
            $c = Order::where('status', $s)->count();
            if ($c > 0) {
                $statusLabels[] = ucfirst($s);
                $statusData[]   = $c;
            }
        }

        $categoryStats  = OrderItem::join('menu_items', 'menu_items.id', '=', 'order_items.menu_item_id')
                            ->selectRaw('menu_items.category, SUM(order_items.quantity) as total')
                            ->groupBy('menu_items.category')
                            ->orderByDesc('total')
                            ->get();
        $categoryLabels = $categoryStats->pluck('category')->toArray();
        $categoryData   = $categoryStats->pluck('total')->toArray();

        $topItems = MenuItem::withCount(['orderItems as total_sold' => fn($q) => $q->whereHas('order')])
                        ->orderByDesc('total_sold')->take(8)->get();

        return view('admin.analytics', compact(
            'monthRevenue', 'monthOrders', 'avgOrderValue', 'newUsers',
            'dailyLabels', 'dailyRevenue', 'statusLabels', 'statusData',
            'categoryLabels', 'categoryData', 'topItems'
        ));
    }
}