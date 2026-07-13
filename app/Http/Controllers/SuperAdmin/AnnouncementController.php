<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

/**
 * Platform broadcasts shown to every tenant. The live set is cached (see
 * Announcement::cachedLive via the app layout); any write busts that cache.
 */
class AnnouncementController extends Controller
{
    /** Cache key for the tenant-facing live banner set. */
    public const CACHE_KEY = 'announcements.live';

    private const LEVELS = ['info', 'warning', 'critical'];

    public function __construct(
        private readonly AuditLogService $audit
    ) {}

    public function index()
    {
        $announcements = Announcement::with('author')->latest()->paginate(20);

        return view('superadmin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('superadmin.announcements.create', [
            'announcement' => new Announcement(['level' => 'info', 'is_active' => true]),
            'levels'       => self::LEVELS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['created_by'] = auth()->id();

        $announcement = Announcement::create($data);
        $this->flush();

        $this->audit->log(auth()->user(), 'announcement_created', $announcement, ['title' => $announcement->title]);

        return redirect()->route('superadmin.announcements.index')->with('success', 'Announcement published.');
    }

    public function edit(Announcement $announcement)
    {
        return view('superadmin.announcements.edit', [
            'announcement' => $announcement,
            'levels'       => self::LEVELS,
        ]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $announcement->update($this->validateData($request));
        $this->flush();

        $this->audit->log(auth()->user(), 'announcement_updated', $announcement, ['title' => $announcement->title]);

        return redirect()->route('superadmin.announcements.index')->with('success', 'Announcement updated.');
    }

    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_active' => ! $announcement->is_active]);
        $this->flush();

        return back()->with('success', 'Announcement ' . ($announcement->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        $this->flush();

        $this->audit->log(auth()->user(), 'announcement_deleted', null, ['title' => $announcement->title]);

        return redirect()->route('superadmin.announcements.index')->with('success', 'Announcement deleted.');
    }

    /**
     * @return array<string,mixed>
     */
    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'title'     => ['required', 'string', 'max:255'],
            'body'      => ['required', 'string', 'max:2000'],
            'level'     => ['required', Rule::in(self::LEVELS)],
            'is_active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at'   => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    private function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
