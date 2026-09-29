// Global front-end bootstrap.
//
// Axios was removed because the app uses the native fetch() API for its only
// AJAX call (admin machine status toggle in admin/machines/index.blade.php),
// which sends its own CSRF header. Add shared browser setup here if needed.
