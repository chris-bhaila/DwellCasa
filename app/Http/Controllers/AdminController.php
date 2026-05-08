<?php

namespace App\Http\Controllers;

use App\Contracts\CheckInRepositoryInterface;
use App\Contracts\RoomRepositoryInterface;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\CheckOut;
use App\Models\Inquiry;
use App\Models\InventoryEquipment;
use App\Models\InventoryStock;
use App\Models\Location;
use App\Models\Review;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    protected function currentLocationId(): ?int
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            return session('selected_location_id');
        }
        return $user->location_id;
    }

    public function dashboard(
        RoomRepositoryInterface $roomRepository,
        RoomTypeRepositoryInterface $roomTypeRepository,
        CheckInRepositoryInterface $checkInRepository
    ) {
        $rooms     = $roomRepository->all();
        $roomTypes = $roomTypeRepository->all();
        $roomTypes->loadCount([
            'rooms',
            'bookings as active_bookings_count' => fn($q) => $q->whereIn('status', ['confirmed', 'checked_in']),
        ]);
        $checkIns  = $checkInRepository->all();
        $bookings  = Booking::with(['guest', 'roomType'])->latest()->take(5)->get();

        $incompleteLocations = collect();
        if (auth()->user()->hasRole('super_admin')) {
            $incompleteLocations = Location::where('is_active', true)
                ->whereDoesntHave('websiteInfo', fn($q) => $q->withoutGlobalScopes()->whereNotNull('front_page_main_heading'))
                ->orderBy('name')
                ->get();
        }

        $today      = now()->toDateString();
        $monthStart = now()->startOfMonth();
        $monthEnd   = now()->endOfMonth();

        // Top KPI cards
        $todayArrivals      = Booking::whereDate('check_in_date', $today)->whereIn('status', ['confirmed', 'pending'])->count();
        $inHouseCount       = Booking::where('status', 'checked_in')->count();
        $monthlyRevenue     = Booking::whereBetween('check_in_date', [$monthStart, $monthEnd])->sum('amount_paid');
        $unrepliedInquiries = Inquiry::where('status', 'unreplied')->count();

        // Secondary stats
        $todayDepartures  = Booking::whereDate('check_out_date', $today)->where('status', 'checked_in')->count();
        $totalRooms       = Room::count();
        $activeBookings   = Booking::whereIn('status', ['confirmed', 'checked_in'])->count();
        $availableRooms   = max(0, $totalRooms - $activeBookings);
        $avgRating        = Review::approved()->whereNotNull('rating')->avg('rating');
        $monthlyBookings  = Booking::whereBetween('check_in_date', [$monthStart, $monthEnd])->count();

        // Bottom panels
        $recentInquiries      = Inquiry::where('status', 'unreplied')->latest()->take(5)->get();
        $monthRevenueBilled   = Booking::whereBetween('check_in_date', [$monthStart, $monthEnd])->sum('total_amount');
        $monthRevenueCollected = $monthlyRevenue;

        // Inventory snapshot for dashboard widget
        $inventoryLowStock   = InventoryStock::where('status', 'low_stock')->count();
        $inventoryOutOfStock = InventoryStock::where('status', 'out_of_stock')->count();
        $inventoryDamaged    = InventoryEquipment::whereIn('condition', ['damaged', 'under_repair'])->count();

        return view('admin.home', compact(
            'rooms', 'roomTypes', 'checkIns', 'bookings', 'incompleteLocations',
            'todayArrivals', 'inHouseCount', 'monthlyRevenue', 'unrepliedInquiries',
            'todayDepartures', 'availableRooms', 'totalRooms', 'avgRating', 'monthlyBookings',
            'recentInquiries', 'monthRevenueBilled', 'monthRevenueCollected',
            'inventoryLowStock', 'inventoryOutOfStock', 'inventoryDamaged'
        ));
    }

    public function activityLogPage()
    {
        $authUser     = auth()->user();
        $isSuperAdmin = $authUser->hasRole('super_admin');
        $locationId   = $isSuperAdmin
            ? session('selected_location_id')
            : $authUser->location_id;

        $logs = Activity::with(['causer', 'location'])
            ->when($locationId, fn ($q) => $q->where('location_id', $locationId))
            ->latest()
            ->paginate(50);

        return view('admin.activity-log', compact('logs'));
    }

    public function revenue(Request $request)
    {
        $from     = $request->query('from', now()->startOfMonth()->toDateString());
        $to       = $request->query('to',   now()->endOfMonth()->toDateString());
        $filter   = $request->query('filter');    // 'outstanding' | 'collected' | 'discounted' | null
        $roomType = $request->query('room_type'); // room type name or null

        $allBookings = Booking::with(['guest', 'roomType', 'checkOut'])
            ->whereBetween('check_in_date', [$from, $to])
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('check_in_date')
            ->get();

        // Summary stats are always computed from all bookings — filters only affect the table
        $billed            = $allBookings->sum('total_amount');
        $collected         = $allBookings->sum('amount_paid');
        $totalDiscount     = $allBookings->sum('discount');
        $totalRefunds      = $allBookings->sum('refund_amount');
        $totalExtra        = $allBookings->sum(fn($b) => $b->checkOut->extra_charges ?? 0);
        $netBilled         = max(0, $billed - $totalDiscount + $totalExtra);
        $outstanding       = max(0, $netBilled - $collected);
        $collectPct        = $netBilled > 0
            ? min(100, round(($collected / $netBilled) * 100))
            : 0;
        $totalBookingCount = $allBookings->count();

        // Apply room type filter first, then dashboard filter
        $bookings = $allBookings;

        if ($roomType) {
            $bookings = $bookings->filter(fn($b) => ($b->roomType?->name ?? 'Unknown') === $roomType)->values();
        }

        $bookings = match ($filter) {
            'outstanding' => $bookings->filter(fn($b) => ($b->total_amount - ($b->discount ?? 0) + ($b->checkOut->extra_charges ?? 0) - ($b->amount_paid ?? 0)) > 0)->values(),
            'collected'   => $bookings->filter(fn($b) => ($b->amount_paid ?? 0) >= ($b->total_amount - ($b->discount ?? 0)))->values(),
            'discounted'  => $bookings->filter(fn($b) => ($b->discount ?? 0) > 0)->values(),
            'extra'       => $bookings->filter(fn($b) => ($b->checkOut->extra_charges ?? 0) > 0)->values(),
            'refunded'    => $bookings->filter(fn($b) => ($b->refund_amount ?? 0) > 0)->values(),
            default       => $bookings,
        };

        $byRoomType = $allBookings->groupBy(fn($b) => $b->roomType?->name ?? 'Unknown')
            ->map(fn($group) => [
                'count'       => $group->count(),
                'billed'      => $group->sum('total_amount'),
                'collected'   => $group->sum('amount_paid'),
                'discount'    => $group->sum('discount'),
                'extra'       => $group->sum(fn($b) => $b->checkOut->extra_charges ?? 0),
                'refunds'     => $group->sum('refund_amount'),
                'outstanding' => max(0,
                    $group->sum('total_amount')
                    - $group->sum(fn($b) => $b->discount ?? 0)
                    + $group->sum(fn($b) => $b->checkOut->extra_charges ?? 0)
                    - $group->sum('amount_paid')
                ),
            ]);

        return view('admin.revenue', compact(
            'from', 'to', 'bookings', 'filter', 'roomType',
            'billed', 'collected', 'outstanding', 'collectPct', 'byRoomType',
            'totalDiscount', 'totalExtra', 'totalRefunds', 'totalBookingCount'
        ));
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        abort_unless(auth()->user()->hasAnyRole(['super_admin', 'admin']), 403);

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore(auth()->id())],
        ]);

        $user        = auth()->user();
        $user->name  = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        return back()->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user           = auth()->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('status', 'password-updated');
    }

    public function switchLocation(Request $request)
    {
        abort_if(!auth()->user()->hasRole('super_admin'), 403);

        $request->validate(['location_id' => 'nullable|exists:locations,id']);
        session(['selected_location_id' => $request->input('location_id')]);

        return response()->json(['success' => true]);
    }
}
