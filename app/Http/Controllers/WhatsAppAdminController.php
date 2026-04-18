<?php

namespace App\Http\Controllers;

use App\Models\WhatsappAdmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class WhatsAppAdminController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/WhatsAppAdmins', [
            'admins' => WhatsappAdmin::query()
                ->ordered()
                ->get(['*'])
                ->map(fn (WhatsappAdmin $admin): array => $this->serializeAdmin($admin))
                ->values()
                ->all(),
            'colorOptions' => $this->colorOptions(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePayload($request);

        $normalizedPhone = $this->resolvePhoneOrFail((string) $validated['phone']);
        $this->ensureUniquePhone($normalizedPhone);

        $nextSortOrder = (int) ((WhatsappAdmin::max('sort_order') ?? -1) + 1);

        $admin = WhatsappAdmin::create([
            'name' => trim((string) $validated['name']),
            'role' => trim((string) $validated['role']),
            'phone' => $normalizedPhone,
            'initial' => $this->resolveInitial(
                trim((string) $validated['name']),
                $validated['initial'] ?? null,
            ),
            'color' => (string) ($validated['color'] ?? 'matcha'),
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'sort_order' => isset($validated['sort_order'])
                ? (int) $validated['sort_order']
                : $nextSortOrder,
        ]);

        return response()->json([
            'message' => 'Kontak WhatsApp admin berhasil ditambahkan.',
            'admin' => $this->serializeAdmin($admin),
        ], 201);
    }

    public function update(Request $request, WhatsappAdmin $whatsappAdmin): JsonResponse
    {
        $validated = $this->validatePayload($request);

        $normalizedPhone = $this->resolvePhoneOrFail((string) $validated['phone']);
        $this->ensureUniquePhone($normalizedPhone, $whatsappAdmin);

        $whatsappAdmin->update([
            'name' => trim((string) $validated['name']),
            'role' => trim((string) $validated['role']),
            'phone' => $normalizedPhone,
            'initial' => $this->resolveInitial(
                trim((string) $validated['name']),
                $validated['initial'] ?? null,
            ),
            'color' => (string) ($validated['color'] ?? $whatsappAdmin->color),
            'is_active' => (bool) ($validated['is_active'] ?? $whatsappAdmin->is_active),
            'sort_order' => isset($validated['sort_order'])
                ? (int) $validated['sort_order']
                : (int) $whatsappAdmin->sort_order,
        ]);

        $freshAdmin = $whatsappAdmin->fresh();

        if (! $freshAdmin instanceof WhatsappAdmin) {
            $freshAdmin = $whatsappAdmin;
        }

        return response()->json([
            'message' => 'Kontak WhatsApp admin berhasil diperbarui.',
            'admin' => $this->serializeAdmin($freshAdmin),
        ]);
    }

    public function destroy(WhatsappAdmin $whatsappAdmin): JsonResponse
    {
        $deletedId = (int) $whatsappAdmin->getKey();

        WhatsappAdmin::destroy($deletedId);

        return response()->json([
            'message' => 'Kontak WhatsApp admin berhasil dihapus.',
            'id' => $deletedId,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:32'],
            'initial' => ['nullable', 'string', 'max:2'],
            'color' => ['nullable', Rule::in(WhatsappAdmin::availableColorKeys())],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'name.required' => 'Nama admin wajib diisi.',
            'role.required' => 'Role admin wajib diisi.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'initial.max' => 'Inisial maksimal 2 karakter.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
            'sort_order.min' => 'Urutan tidak boleh negatif.',
        ]);
    }

    private function resolvePhoneOrFail(string $phone): string
    {
        $normalized = WhatsappAdmin::normalizePhone($phone);

        if ($normalized === '' || ! preg_match('/^62[0-9]{8,13}$/', $normalized)) {
            throw ValidationException::withMessages([
                'phone' => 'Format nomor WhatsApp tidak valid. Gunakan nomor aktif Indonesia.',
            ]);
        }

        return $normalized;
    }

    private function ensureUniquePhone(string $phone, ?WhatsappAdmin $current = null): void
    {
        $query = WhatsappAdmin::query()
            ->where('phone', '=', $phone);

        if ($current instanceof WhatsappAdmin) {
            $query->whereKeyNot((int) $current->getKey());
        }

        $exists = $query->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'phone' => 'Nomor WhatsApp sudah terdaftar.',
            ]);
        }
    }

    private function resolveInitial(string $name, mixed $initialInput): string
    {
        $initial = trim((string) $initialInput);

        if ($initial !== '') {
            return mb_strtoupper(mb_substr($initial, 0, 2));
        }

        $trimmedName = trim($name);

        if ($trimmedName === '') {
            return 'A';
        }

        return mb_strtoupper(mb_substr($trimmedName, 0, 1));
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeAdmin(WhatsappAdmin $admin): array
    {
        return [
            'id' => (int) $admin->id,
            'name' => (string) $admin->name,
            'role' => (string) $admin->role,
            'phone' => (string) $admin->phone,
            'initial' => $this->resolveInitial((string) $admin->name, $admin->initial),
            'color' => $admin->resolveColorClass(),
            'color_key' => (string) $admin->color,
            'is_active' => (bool) $admin->is_active,
            'sort_order' => (int) $admin->sort_order,
            'created_at' => optional($admin->created_at)?->toISOString(),
            'updated_at' => optional($admin->updated_at)?->toISOString(),
        ];
    }

    /**
     * @return array<int, array{key: string, label: string, class: string}>
     */
    private function colorOptions(): array
    {
        $labels = [
            'matcha' => 'Matcha Green',
            'indigo' => 'Indigo Blue',
            'sakura' => 'Sakura Pink',
            'amber' => 'Amber',
            'sky' => 'Sky',
            'slate' => 'Slate',
        ];

        return collect(WhatsappAdmin::COLOR_CLASS_MAP)
            ->map(fn (string $className, string $key): array => [
                'key' => $key,
                'label' => $labels[$key] ?? ucfirst($key),
                'class' => $className,
            ])
            ->values()
            ->all();
    }
}
