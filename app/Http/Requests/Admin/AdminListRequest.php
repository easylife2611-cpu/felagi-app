<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Shared list-query validator for WP-05c admin read endpoints.
 *
 * Per Design_Data/admin.json.filtering:
 *   "Search and allowlisted status/date filters, paginated 25 rows,
 *    maximum 100; backend authorization applied before pagination."
 *
 * Query params:
 *   - page      : int, >= 1              (default 1)
 *   - per_page  : int, 1..100            (default 25)
 *   - q         : string, <= 200         (free-text search)
 *   - status    : string, allowlisted    (per-screen allowlist — enforced in controller)
 *   - date_from : date (Y-m-d)
 *   - date_to   : date (Y-m-d), >= date_from
 *   - sort      : string, allowlisted    (per-screen allowlist — enforced in controller)
 *   - dir       : asc|desc               (default desc)
 */
class AdminListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Per-area authorization handled in controller via policy
    }

    public function rules(): array
    {
        return [
            'page'      => ['sometimes', 'integer', 'min:1'],
            'per_page'  => ['sometimes', 'integer', 'min:1', 'max:100'],
            'q'         => ['sometimes', 'string', 'max:200'],
            'status'    => ['sometimes', 'string', 'max:64'],
            'date_from' => ['sometimes', 'date_format:Y-m-d'],
            'date_to'   => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'sort'      => ['sometimes', 'string', 'max:64'],
            'dir'       => ['sometimes', 'in:asc,desc'],
        ];
    }

    public function perPage(): int
    {
        return (int) $this->input('per_page', 25);
    }

    public function pageNumber(): int
    {
        return (int) $this->input('page', 1);
    }

    public function searchTerm(): ?string
    {
        $q = $this->input('q');
        return is_string($q) && $q !== '' ? $q : null;
    }

    public function statusFilter(): ?string
    {
        $s = $this->input('status');
        return is_string($s) && $s !== '' ? $s : null;
    }

    public function dateFrom(): ?string
    {
        return $this->input('date_from');
    }

    public function dateTo(): ?string
    {
        return $this->input('date_to');
    }

    public function sortKey(): ?string
    {
        $s = $this->input('sort');
        return is_string($s) && $s !== '' ? $s : null;
    }

    public function sortDir(): string
    {
        return $this->input('dir', 'desc') === 'asc' ? 'asc' : 'desc';
    }
}
