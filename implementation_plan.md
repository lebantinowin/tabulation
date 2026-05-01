# Fix All 20 Tabulation System Issues

Comprehensive fix across all 6 problem categories identified in the audit. No new features — this is purely correctness, security, and maintainability.

## User Review Required

> [!IMPORTANT]
> **Database changes required.** Two schema-level decisions need your input:
>
> **A. Soft Deletes** (Issue #9, #10, #15): Adding `deleted_at` columns to `events`, `contestants`, `users` (judges), and `scores` tables so records are never permanently lost. This requires a new migration. Existing data is unaffected.
>
> **B. `user_id` → `judge_id` unification** (Issue #2): The `scores` table already has a `judge_id` column from the original migration. A later code change started writing to `user_id` instead. The fix is to update all controller code to use `judge_id` consistently — **no schema change needed**, just code. Any existing scores with `user_id` set but `judge_id` null will have null judge references; this would need a one-time data fix if live data exists.

> [!WARNING]
> **Public Results Gate** (Issue #3): The fix adds a check so `/results/{event}` only shows results when the event `status === 'completed'`. If any events are currently `ongoing` and you want their results visible publicly, you'd need to mark them as `completed` first.

---

## Open Questions

> [!IMPORTANT]
> **How should weighted scores be calculated?** (Issue #8)
> The `criteria` table has a `weight` column (0–100). The admin tabulation view already does weighted calculation correctly using `$criteria->percentage`. But the `TabulationController` (used for public results and the print views) uses raw sum/avg.
> 
> **Proposed fix:** Use `weight` as a percentage weight. Final score = Σ (avg_judge_score × weight / 100). This matches what the admin results view already does.
> 
> Is this correct, or should total score = sum of all raw scores across all criteria?

---

## Proposed Changes

### Group 1 — Security Fixes

#### [MODIFY] [ScoreController.php](file:///c:/xampp/htdocs/tabulation/app/Http/Controllers/ScoreController.php)
- **Issue #1 (Event isolation):** In `store()`, `update()`, `edit()`, `destroy()` — add validation that the `contestant_id` belongs to the judge's assigned event. Reject with 403 if not.
- **Issue #2 (Judge ID field):** Replace all `user_id` references with `judge_id`. Update `Score::where('user_id', ...)` → `Score::where('judge_id', ...)`.
- **Issue #5 (Lock bypass):** No code change needed — the existing logic is fine. Documented as a non-issue after re-review.

#### [MODIFY] [Score.php](file:///c:/xampp/htdocs/tabulation/app/Models/Score.php)
- **Issue #2:** Add `user_id` → remove from relationship confusion. Ensure `$fillable` has `judge_id` and NOT `user_id` (it currently has `judge_id` which is correct — but controller was writing `user_id`).

#### [MODIFY] [routes/web.php](file:///c:/xampp/htdocs/tabulation/routes/web.php)
- **Issue #3 (Public results gate):** Wrap `/results/{event}` in a middleware check or move the guard into the controller — only show results if `$event->status === 'completed'`.

#### [MODIFY] [TabulationController.php](file:///c:/xampp/htdocs/tabulation/app/Http/Controllers/TabulationController.php)
- **Issue #4 (Override audit log):** Add `AuditLog::log(...)` call in `override()`.
- **Issue #7 (Rank not persisted):** Fix `$result['rank']` → `$results[$index]['rank']` in `results()`.
- **Issue #8 (Weights ignored):** Apply weighted calculation in `publicResults()`, `print()`, `printCategory()`.
- **Issue #16 (N+1 queries):** Replace nested score queries with a single eager-loaded collection, grouped in PHP.

#### [MODIFY] [JudgeController.php](file:///c:/xampp/htdocs/tabulation/app/Http/Controllers/JudgeController.php)
- **Issue #14 (Insecure rand):** Replace `rand()` with `random_int()` in `generateUniqueLoginCode()`.
- **Issue #9 (Orphaned scores on delete):** In `destroy()`, nullify or reassign scores before hard delete. With soft deletes enabled, this becomes a non-issue.

---

### Group 2 — Logic / Correctness

#### [MODIFY] [CriteriaController.php](file:///c:/xampp/htdocs/tabulation/app/Http/Controllers/CriteriaController.php)
- **Issue #6 (Wrong status filter):** Change `Event::where('status', 'active')` → `Event::all()` (or filter by `ongoing`/`upcoming`) in both `create()` and `edit()`.

#### [MODIFY] [EventController.php](file:///c:/xampp/htdocs/tabulation/app/Http/Controllers/EventController.php)
- **Issue #10 (Criteria orphans on event delete):** Scores are already cascade-deleted via FK. Verify criteria cascade too.
- **Issue #15 (No soft delete guard):** With `SoftDeletes` on models, `destroy()` becomes a soft delete automatically.

---

### Group 3 — Image Path Resolution (DRY)

#### [NEW] [Contestant.php accessor](file:///c:/xampp/htdocs/tabulation/app/Models/Contestant.php)
- **Issue #12:** Add `getImageUrlAttribute()` accessor to `Contestant` model that resolves the image path once and returns a full public URL (or `null`). All Blade files use `$contestant->image_url` instead of repeating the 20-line path logic.

#### [MODIFY] judge/dashboard.blade.php, results/show.blade.php, results/index.blade.php, admin views
- Replace all copy-pasted image path logic blocks with `$contestant->image_url`.

---

### Group 4 — Business Logic Out of Views

#### [MODIFY] [JudgeController.php](file:///c:/xampp/htdocs/tabulation/app/Http/Controllers/JudgeController.php) — `dashboard()` route
- **Issue #11:** The `judge.dashboard` route currently returns the view via a closure in `web.php` with no data. Move it to a proper `JudgeController::dashboard()` method that passes `$event`, `$contestants` to the view.

#### [MODIFY] [routes/web.php](file:///c:/xampp/htdocs/tabulation/routes/web.php)
- Change `judge.dashboard` route closure to point to `[JudgeController::class, 'dashboard']`.

#### [MODIFY] [judge/dashboard.blade.php](file:///c:/xampp/htdocs/tabulation/resources/views/judge/dashboard.blade.php)
- Remove the `@php` block that does DB queries. Use `$event` and `$contestants` passed from the controller.

---

### Group 5 — Soft Deletes & Data Safety

#### [NEW] Migration: `add_soft_deletes_to_events_contestants_users_scores`
- **Issue #15:** Add `$table->softDeletes()` to `events`, `contestants`, `users`, `scores` tables.

#### [MODIFY] Event.php, Contestant.php, User.php, Score.php
- Add `use SoftDeletes` trait to each model.

---

### Group 6 — Housekeeping

#### [MODIFY] [SubCriteria.php](file:///c:/xampp/htdocs/tabulation/app/Models/SubCriteria.php) & [Criteria.php](file:///c:/xampp/htdocs/tabulation/app/Models/Criteria.php)
- **Issue #17:** Remove `subCriteria()` relationship from `Criteria.php`. Leave the model file but add a comment noting it is unused pending future feature work.

#### [MODIFY] [TabulationController.php](file:///c:/xampp/htdocs/tabulation/app/Http/Controllers/TabulationController.php) — `override()` / `lock()` / `message()`
- **Issue #18:** The `Tabulation` model override is never read back in results. Two options:
  - **Option A (Simple):** Remove override/lock/message actions from the controller and routes since they have no effect.
  - **Option B (Integrate):** Make `publicResults()` and `results()` check for a `Tabulation` override and apply it.
  - **Proposed:** Option B — honor overrides in results display.

#### [MODIFY] [README.md](file:///c:/xampp/htdocs/tabulation/README.md)
- **Issue #19:** Write basic setup instructions (requirements, `.env` setup, `composer install`, `php artisan migrate`, `npm run dev`).

#### [MODIFY] [.gitignore](file:///c:/xampp/htdocs/tabulation/.gitignore)
- **Issue #20:** Verify `.env` is listed. If the `.env` file is already tracked by git, run `git rm --cached .env` to untrack it.

---

## Verification Plan

### Automated Tests
- No existing tests — will run manual verification only.

### Manual Verification Checklist
1. Log in as a judge → confirm they cannot score contestants from other events
2. Submit a score → confirm `judge_id` is written (not `user_id`) in the DB
3. Create criteria for an event → confirm event dropdown is no longer empty
4. View tabulation results → confirm rank is correctly displayed
5. Delete a judge → confirm their scores remain (soft deleted user)
6. View `/results/{ongoing-event}` → confirm it returns a "not available" message
7. Admin overrides a score → confirm it appears in the audit log and in results
8. Check that image resolution uses the model accessor everywhere
9. Check for N+1 queries using Laravel Debugbar (optional) or query count assertions
